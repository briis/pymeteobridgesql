<?php

ini_set('display_errors', 'Off');
error_reporting(E_ALL);

require 'getenv.php';
use DevCoder\DotEnv;
(new DotEnv(__DIR__ . '/.env'))->load();
$api_key = $_ENV["VISUALCROSSING_API_KEY"];
$db_host = $_ENV["DB_HOST"];
$db_user = $_ENV["DB_USER"];
$db_pass = $_ENV["DB_PASS"];
$db_name = $_ENV["DB_NAME"];
$timezone = $_ENV["TIMEZONE"];
$station_id = $_ENV["STATION_ID"];

// ***************************************
// Return Forecast data from Visual Crossing
// ***************************************
$url = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/roskilde?unitGroup=metric&lang=da&iconSet=icons2&key=" . $api_key . "&contentType=json";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$resp = curl_exec($curl);
curl_close($curl);

// ***************************************
// Insert Data into MySQL Database
// ***************************************

// Connect to Database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Loop Through Data and Insert into Database
$tz = new DateTimeZone($timezone);
$forecastdata = json_decode($resp, TRUE);

// Update Current Weather
$currentdata = $forecastdata["currentConditions"];
$icon = conditionState($currentdata["icon"]);
$current_description = $forecastdata["description"];
$sql = "UPDATE `realtime_data` SET `description` = '".$current_description."', `icon` = '".$icon."' WHERE `ID` = '".$station_id."'";
if (!mysqli_query($conn, $sql)) {
    echo "Error: " . $sql . "\n" . mysqli_error($conn). "\n";
}

// Add Daily Forecast
$day_num = 1;
$hour_num = 1;
foreach($forecastdata["days"] as $value) {
    $jobj_fcst = new StdClass;
    $epoch = $value["datetimeEpoch"];
    $dt = new DateTime("@$epoch");
    $dt->setTimezone($tz);
    $jobj_fcst->datetime = $dt->format('Y-m-d 00:00:00');
    $jobj_fcst->native_temperature = $value["tempmax"];
    $jobj_fcst->icon = conditionState($value["icon"]);
    $jobj_fcst->description = $value["description"];
    $jobj_fcst->native_templow = $value["tempmin"];
    $jobj_fcst->native_precipitation = $value["precip"];
    $jobj_fcst->precipitation_probability = $value["precipprob"];
    $jobj_fcst->native_pressure = $value["pressure"];
    $jobj_fcst->wind_bearing = $value["winddir"];
    $jobj_fcst->native_wind_speed = kmh2ms($value["windspeed"]);
    $jobj_fcst->native_wind_gust = kmh2ms($value["windgust"]);

    $sql = "INSERT INTO `forecast_daily` (`day_num`, `datetime`, `temperature`, `temp_low`, `description`, `icon`, `precipitation_probability`, `precipitation`, `pressure`, `wind_bearing`, `wind_speed`, `wind_gust`) ";
    $sql = $sql . "VALUES (".$day_num.",'".$jobj_fcst->datetime."',".$jobj_fcst->native_temperature.",".$jobj_fcst->native_templow.",'".$jobj_fcst->description."','".$jobj_fcst->icon."',".$jobj_fcst->precipitation_probability."," .$jobj_fcst->native_precipitation."," .$jobj_fcst->native_pressure.",".$jobj_fcst->wind_bearing.",".$jobj_fcst->native_wind_speed.",".$jobj_fcst->native_wind_gust.") ";
    $sql = $sql . "ON DUPLICATE KEY UPDATE `datetime`='".$jobj_fcst->datetime."', `temperature`=".$jobj_fcst->native_temperature.", `temp_low`=".$jobj_fcst->native_templow.", `description`='".$jobj_fcst->description."', `icon`='".$jobj_fcst->icon."', `precipitation_probability`=".$jobj_fcst->precipitation_probability.", `precipitation`=".$jobj_fcst->native_precipitation.", `pressure`=".$jobj_fcst->native_pressure.", `wind_bearing`=".$jobj_fcst->wind_bearing.", `wind_speed`=".$jobj_fcst->native_wind_speed.", `wind_gust`=".$jobj_fcst->native_wind_gust;
    ++$day_num;

    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . $sql . "\n" . mysqli_error($conn). "\n";
    }

    // Loop through the Hours for this day
    // But only for the first two days
    if ($day_num > 4) {
        continue;
    }

    foreach($value["hours"] as $hour) {
        $jobj_hour = new StdClass;
        $epoch = $hour["datetimeEpoch"];
        $dt = new DateTime("@$epoch");
        $dt->setTimezone($tz);
        $jobj_hour->datetime = $dt->format('Y-m-d H:00:00');
        $jobj_hour->native_temperature = $hour["temp"];
        $jobj_hour->apparent_temperature = $hour["feelslike"];
        $jobj_hour->description = $hour["conditions"];
        $jobj_hour->icon = conditionState($hour["icon"]);
        $jobj_hour->humidity = $hour["humidity"];
        $jobj_hour->native_precipitation = $hour["precip"];
        $jobj_hour->precipitation_probability = $hour["precipprob"];
        $jobj_hour->native_pressure = $hour["pressure"];
        $jobj_hour->wind_bearing = $hour["winddir"];
        $jobj_hour->native_wind_speed = kmh2ms($hour["windspeed"]);
        $jobj_hour->native_wind_gust = kmh2ms($hour["windgust"]);
        $jobj_hour->uvindex = $hour["uvindex"];

        $sql = "INSERT INTO `forecast_hourly` (`hour_num`, `datetime`, `temperature`, `apparent_temperature`, `description`, `icon`, `humidity`, `precipitation_probability`, `precipitation`, `pressure`, `wind_bearing`, `wind_speed`, `wind_gust`, `uv_index`) ";
        $sql = $sql . "VALUES (".$hour_num.",'".$jobj_hour->datetime."',".$jobj_hour->native_temperature.",".$jobj_hour->apparent_temperature.",'".$jobj_hour->description."','".$jobj_hour->icon."',".$jobj_hour->humidity.",".$jobj_hour->precipitation_probability.",".$jobj_hour->native_precipitation."," .$jobj_hour->native_pressure.",".$jobj_hour->wind_bearing.",".$jobj_hour->native_wind_speed.",".$jobj_hour->native_wind_gust.",".$jobj_hour->uvindex.") ";
        $sql = $sql . "ON DUPLICATE KEY UPDATE `datetime`='".$jobj_hour->datetime."', `temperature`=".$jobj_hour->native_temperature.", `apparent_temperature`=".$jobj_hour->apparent_temperature.", `description`='".$jobj_hour->description."', `icon`='".$jobj_hour->icon."', `humidity`=".$jobj_hour->humidity.", `precipitation_probability`=".$jobj_hour->precipitation_probability.", `precipitation`=".$jobj_hour->native_precipitation.", `pressure`=".$jobj_hour->native_pressure.", `wind_bearing`=".$jobj_hour->wind_bearing.", `wind_speed`=".$jobj_hour->native_wind_speed.", `wind_gust`=".$jobj_hour->native_wind_gust.", `uv_index`=".$jobj_hour->uvindex;
        ++$hour_num;

        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "\n" . mysqli_error($conn). "\n";
        }
}
}

mysqli_close($conn);

// ***************************************
// Helper Functions
// ***************************************

function conditionState($icon) {
    if ($icon == "clear-day") { return "sunny";}
    if ($icon == "partly-cloudy-day") { return "partlycloudy";}
    if ($icon == "partly-cloudy-night") { return "partlycloudy";}
    if ($icon == "showers-day") { return "rainy";}
    if ($icon == "showers-night") { return "rainy";}
    if ($icon == "thunder-showers-day") { return "lightning-rainy";}
    if ($icon == "thunder-showers-night") { return "lightning-rainy";}
    if ($icon == "rain") { return "rainy";}
    if ($icon == "snow") { return "snowy";}
    return $icon;
}

function kmh2ms($kmh) {
    return $kmh / 3.6;
}
?>