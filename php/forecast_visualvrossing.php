<?php

ini_set ('display_errors', 'Off');error_reporting(E_ALL);

// Return AQI value from PM25 value
$url = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/roskilde?unitGroup=metric&lang=da&iconSet=icons2&key=XUDK94GTNWGS3RUHKSN8C8VP3&contentType=json";
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

$this_directory = dirname(__FILE__);
$filename = $this_directory . "/forecast_visual.json";
file_put_contents($filename, $resp);header('Content-Type: application/json');echo "success";

?>