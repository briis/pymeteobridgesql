<?php

ini_set('display_errors', 'Off');
error_reporting(E_ALL);

require 'getenv.php';
use DevCoder\DotEnv;
(new DotEnv(__DIR__ . '/.env'))->load();
$db_host = $_ENV["DB_HOST"];
$db_user = $_ENV["DB_USER"];
$db_pass = $_ENV["DB_PASS"];
$db_name = $_ENV["DB_NAME"];

/*
Run this once to create the table:

CREATE TABLE IF NOT EXISTS `pollen_data` (
  `id`                    INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
  `region`                VARCHAR(20)     NOT NULL,
  `date`                  DATE            NOT NULL,
  `birk_count`            SMALLINT        NULL,
  `birk_severity`         VARCHAR(10)     NULL,
  `bynke_count`           SMALLINT        NULL,
  `bynke_severity`        VARCHAR(10)     NULL,
  `el_count`              SMALLINT        NULL,
  `el_severity`           VARCHAR(10)     NULL,
  `elm_count`             SMALLINT        NULL,
  `elm_severity`          VARCHAR(10)     NULL,
  `graes_count`           SMALLINT        NULL,
  `graes_severity`        VARCHAR(10)     NULL,
  `hassel_count`          SMALLINT        NULL,
  `hassel_severity`       VARCHAR(10)     NULL,
  `alternaria_count`      MEDIUMINT       NULL,
  `alternaria_severity`   VARCHAR(10)     NULL,
  `cladosporium_count`    INT             NULL,
  `cladosporium_severity` VARCHAR(10)     NULL,
  `updated_at`            TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `region_date` (`region`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

// ***************************************
// Fetch Pollen Data from Astma-Allergi DK
// ***************************************
$url  = "https://www.astma-allergi.dk/umbraco/Api/PollenApi/GetPollenFeed";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Accept: application/json, text/plain, */*",
    "Accept-Language: da-DK,da;q=0.9,en;q=0.8",
    "Referer: https://www.astma-allergi.dk/",
]);

$resp      = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($resp === false || $http_code !== 200) {
    die("Failed to fetch pollen data. HTTP: " . $http_code . "\n");
}

// The API returns a double-encoded JSON string, so decode twice
$inner = json_decode($resp, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_string($inner)) {
    die("Invalid outer JSON from pollen API\n");
}
$raw = json_decode($inner, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($raw["fields"])) {
    die("Invalid inner JSON from pollen API\n");
}

// ***************************************
// Insert Data into MySQL Database
// ***************************************
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Station IDs from Astma-Allergi DK (Firestore document keys)
$stations = [
    "48" => "koebenhavn",
    "49" => "viborg",
];

// Allergen IDs as used in the API response
$pollen_ids = [
    "7"  => "birk",
    "31" => "bynke",
    "1"  => "el",
    "4"  => "elm",
    "28" => "graes",
    "2"  => "hassel",
    "44" => "alternaria",
    "45" => "cladosporium",
];

// Ascending thresholds: [low, moderate, high] or [low, moderate, high, very_high]
$thresholds = [
    "birk"         => [30,   100,   200,  300],
    "bynke"        => [10,    50,   100],
    "el"           => [10,    50,   100],
    "elm"          => [10,    50,   100],
    "graes"        => [10,    50,   100],
    "hassel"       => [ 5,    15,    30],
    "alternaria"   => [20,   100,   500],
    "cladosporium" => [2000, 6000, 10000],
];

// Maps the 0-4 severity index used in prediction/override fields to a label
$severity_index_map = [0 => "none", 1 => "low", 2 => "moderate", 3 => "high", 4 => "very_high"];

foreach ($stations as $station_id => $region) {
    if (!isset($raw["fields"][$station_id])) {
        continue;
    }

    $station   = firestoreValue($raw["fields"][$station_id]);
    $today_iso = date("Y-m-d");  // always use server date so today's row stays aligned
    $allergens = $station["data"] ?? [];

    $today_row     = ["region" => $region, "date" => $today_iso];
    $forecast_rows = [];  // keyed by ISO date string

    foreach ($pollen_ids as $type_id => $name) {
        $allergen    = $allergens[$type_id] ?? null;
        $predictions = $allergen["predictions"] ?? [];
        $overrides   = $allergen["overrides"]   ?? [];

        // ── Today's row ─────────────────────────────────────────────────────
        // Count: actual grain measurement when in season, else 0.
        // Severity: always from today's prediction entry (same source as forecast
        //           days) so the value is never null regardless of inSeason state.
        $count    = 0;
        $severity = "unknown";
        if ($allergen !== null) {
            $level    = isset($allergen["level"]) ? (int)$allergen["level"] : null;
            $inSeason = $allergen["inSeason"] ?? false;
            if ($inSeason && $level !== null && $level >= 0) {
                $count = $level;
            }
            $severity = parsePrediction(
                $predictions, $overrides, $today_iso,
                $name, $thresholds, $severity_index_map
            );
            // If today is absent from predictions, use the nearest future prediction
            if ($severity === "unknown" && !empty($predictions)) {
                $pred_keys = array_keys($predictions);
                usort($pred_keys, fn($a, $b) => strcmp(toIsoDate($a), toIsoDate($b)));
                foreach ($pred_keys as $k) {
                    if (toIsoDate($k) > $today_iso) {
                        $severity = parsePrediction(
                            $predictions, $overrides, toIsoDate($k),
                            $name, $thresholds, $severity_index_map
                        );
                        break;
                    }
                }
            }
            if ($severity === "unknown") {
                $severity = $inSeason ? calcSeverity($name, $count, $thresholds) : "none";
            }
        }
        $today_row[$name . "_count"]    = $count;
        $today_row[$name . "_severity"] = $severity;

        // ── Forecast rows for future dates ───────────────────────────────────
        if (empty($predictions)) {
            continue;
        }

        $pred_keys = array_keys($predictions);
        usort($pred_keys, fn($a, $b) => strcmp(toIsoDate($a), toIsoDate($b)));

        foreach ($pred_keys as $raw_key) {
            $iso_key = toIsoDate($raw_key);
            if ($iso_key <= $today_iso) {
                continue;
            }

            $sev = parsePrediction(
                $predictions, $overrides, $iso_key,
                $name, $thresholds, $severity_index_map
            );

            if (!isset($forecast_rows[$iso_key])) {
                $forecast_rows[$iso_key] = ["region" => $region, "date" => $iso_key];
                foreach (array_values($pollen_ids) as $n) {
                    $forecast_rows[$iso_key][$n . "_count"]    = null;
                    $forecast_rows[$iso_key][$n . "_severity"] = "unknown";
                }
            }
            $forecast_rows[$iso_key][$name . "_severity"] = $sev;
        }
    }

    // Upsert forecast rows first, then today's actual row so that today's
    // grain counts win if the predictions array also contains today's date.
    foreach ($forecast_rows as $row) {
        upsertRow($conn, $row);
    }
    upsertRow($conn, $today_row);
}

mysqli_close($conn);

// ***************************************
// Helper Functions
// ***************************************

// Resolve the severity for a single ISO date from a predictions map + overrides list.
// Predictions keys are DD-MM-YYYY; values are either {prediction: val} maps or bare values.
// Returns a severity string; never returns null.
function parsePrediction(
    array $predictions, array $overrides, string $iso_date,
    string $pollen_name, array $thresholds, array $sev_map
): string {
    $sorted_keys = array_keys($predictions);
    usort($sorted_keys, fn($a, $b) => strcmp(toIsoDate($a), toIsoDate($b)));

    foreach ($sorted_keys as $i => $raw_key) {
        if (toIsoDate($raw_key) !== $iso_date) {
            continue;
        }
        $pred_data = $predictions[$raw_key];
        if (is_array($pred_data) && array_key_exists("prediction", $pred_data)) {
            $raw_val = $pred_data["prediction"];
        } elseif (!is_array($pred_data)) {
            $raw_val = $pred_data;
        } else {
            $raw_val = null;
        }

        if ($raw_val !== null && $raw_val !== "") {
            $pred_num = (int) round((float) $raw_val);
            // Values 0-4 are severity indices; anything higher is a grain count
            return $pred_num <= 4
                ? ($sev_map[$pred_num] ?? "none")
                : calcSeverity($pollen_name, $pred_num, $thresholds);
        }
        // Empty prediction — fall back to override at same sorted index
        if (isset($overrides[$i]) && $overrides[$i] !== null) {
            return $sev_map[(int)$overrides[$i]] ?? "none";
        }
        return "none";
    }
    return "unknown";  // date not present in predictions
}

function upsertRow($conn, array $row): void {
    $cols    = implode(", ", array_map(fn($k) => "`$k`", array_keys($row)));
    $vals    = implode(", ", array_map(
        fn($v) => $v === null ? "NULL" : "'" . mysqli_real_escape_string($conn, (string)$v) . "'",
        array_values($row)
    ));
    $updates = implode(", ", array_map(
        fn($k, $v) => "`$k` = " . ($v === null ? "NULL" : "'" . mysqli_real_escape_string($conn, (string)$v) . "'"),
        array_keys($row),
        array_values($row)
    )) . ", `updated_at` = NOW()";

    $sql = "INSERT INTO `pollen_data` ($cols) VALUES ($vals) ON DUPLICATE KEY UPDATE $updates";
    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . $sql . "\n" . mysqli_error($conn) . "\n";
    }
}

// Convert DD-MM-YYYY to YYYY-MM-DD; passes through anything that doesn't match
function toIsoDate(string $raw): string {
    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $raw, $m)) {
        return "{$m[3]}-{$m[2]}-{$m[1]}";
    }
    return $raw;
}

// Recursively unwrap a Firestore REST API value node
function firestoreValue(array $node): mixed {
    if (array_key_exists("stringValue",  $node)) return $node["stringValue"];
    if (array_key_exists("integerValue", $node)) return (int)$node["integerValue"];
    if (array_key_exists("doubleValue",  $node)) return (float)$node["doubleValue"];
    if (array_key_exists("booleanValue", $node)) return (bool)$node["booleanValue"];
    if (array_key_exists("nullValue",    $node)) return null;

    if (isset($node["mapValue"]["fields"])) {
        $result = [];
        foreach ($node["mapValue"]["fields"] as $key => $child) {
            $result[$key] = firestoreValue($child);
        }
        return $result;
    }

    if (isset($node["arrayValue"]["values"])) {
        return array_map('firestoreValue', $node["arrayValue"]["values"]);
    }

    return null;
}

// Derive severity label from grain count using ascending thresholds
function calcSeverity(string $name, int $count, array $thresholds): string {
    if (!isset($thresholds[$name])) return "unknown";
    $labels = ["low", "moderate", "high", "very_high"];
    $t      = $thresholds[$name];

    for ($i = count($t) - 1; $i >= 0; $i--) {
        if ($count >= $t[$i]) {
            return $labels[$i];
        }
    }
    return "none";
}
?>
