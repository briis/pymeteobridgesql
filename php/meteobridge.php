<?php 
require('phpMQTT.php');

//******************************************* */
// Read Config File
//******************************************* */
$config = file_get_contents(".env.json");
$config_json = json_decode($config, true);

$mqtt_server = $config_json["mqtt_server"];
$mqtt_port = $config_json["mqtt_port"];
$mqtt_username = $config_json["mqtt_username"];
$mqtt_password = $config_json["mqtt_password"];
$mqtt_client_id = $config_json["mqtt_client_id"];
//******************************************* */

$array = [];
$jobj = new StdClass;
$jbinobj = new StdClass;
$jfcstobj = new StdClass;
ini_set ('display_errors', 'Off');error_reporting(E_ALL);
if( isset($_GET['d']) ) {$data=$_GET['d'];
    if (!empty($data)) {
        $array = explode(";", $data);
        $jobj->station = 'Vindinge';
        $jobj->temperature = $array[0];
        $jobj->tempmax = $array[1];
        $jobj->tempmin = $array[2];
        $jobj->windchill = $array[3];
        $jobj->pm10 = $array[4];
        $jobj->pm25 = $array[5];
        $jobj->pm1 = $array[6];
        $jobj->aqi = aqiFromPM($array[5]);
        $jobj->heatindex = $array[7];
        $jobj->temp15min = $array[8];
        $jobj->humidity = $array[9];
        $jobj->windspeedavg = $array[10];
        $jobj->windgust = $array[11];
        $jobj->tempmaxtime = $array[12];
        $jobj->tempmintime = $array[13];
        $jobj->dewpoint = $array[14];
        $jobj->rainrate = $array[15];
        $jobj->raintoday = $array[16];
        $jobj->beaufort = $array[17];
        $jobj->windspeed10minmax = $array[18];
        $jobj->windspeeddayavg = $array[19];
        $jobj->beaufortdaymax = $array[20];
        $jobj->beaufortdayavg = $array[21];
        $jobj->windgustmax = $array[22];
        $jobj->windgustmaxtime = $array[23];
        $jobj->beaufortdaymaxtime = $array[24];
        $jobj->windbearing = $array[25];
        $jobj->windcardinal = convertDegreesToWindDirection($array[25]);
        $jobj->windbearing10minavg = $array[26];
        $jobj->windbearingdayavg = $array[27];
        $jobj->windbearingmonthavg = $array[28];
        $jobj->rainlast24hr = $array[29];
        $jobj->rainthismonth = $array[30];
        $jobj->rainthisyear = $array[31];
        $jobj->rainyesterday = $array[32];
        $jobj->humidity15min = $array[33];
        $jobj->humiditymax = $array[34];
        $jobj->humiditymin = $array[35];
        $jobj->humiditymaxtime = $array[36];
        $jobj->humiditymintime = $array[37];
        $jobj->humiditydayavg = $array[38];
        $jobj->sealevelpressure = $array[39];
        $jobj->sealevelpressure60min = $array[40];
        $jobj->sealevelpressuredaymax = $array[41];
        $jobj->sealevelpressuredaymaxtime = $array[42];
        $jobj->sealevelpressuredaymin = $array[43];
        $jobj->sealevelpressuredaymintime = $array[44];
        $jobj->sealevelpressure24hr = $array[45];
        $jobj->pm10dayavg = $array[46];
        $jobj->pm25dayavg = $array[47];
        $jobj->pm1dayavg = $array[48];
        $jobj->pm10daymax = $array[49];
        $jobj->pm25daymax = $array[50];
        $jobj->tempmaxyday = $array[51];
        $jobj->tempminyday = $array[52];
        $jobj->dewpointmaxyday = $array[53];
        $jobj->dewpointminyday = $array[54];
        $jobj->windgustmaxyday = $array[55];
        $jobj->sealevelpressuremaxyday = $array[56];
        $jobj->sealevelpressureminyday = $array[57];
        $jobj->tempmonthmax = $array[58];
        $jobj->tempmonthmaxtime = $array[59];
        $jobj->tempmonthmin = $array[60];
        $jobj->tempmonthmintime = $array[61];
        $jobj->tempyearmax = $array[62];
        $jobj->tempyearmaxtime = $array[63];
        $jobj->tempyearmin = $array[64];
        $jobj->tempyearmintime = $array[65];
        $jobj->tempallmax = $array[66];
        $jobj->tempallmaxtime = $array[67];
        $jobj->tempallmin = $array[68];
        $jobj->tempallmintime = $array[69];
        $jobj->rainlastmonth = $array[70];
        $jobj->rainlastyear = $array[71];
        $jobj->windgustmaxydaytime = $array[72];
        $jobj->windgustmonthmax = $array[73];
        $jobj->windgustmonthmaxtime = $array[74];
        $jobj->windgustyearmax= $array[75];
        $jobj->windgustyearmaxtime = $array[76];
        $jobj->windgustallmax = $array[77];
        $jobj->windgustallmaxtime = $array[78];
        $jobj->stationepochtime = $array[79];
        $jobj->windspeedydayavg = $array[80];
        $jobj->windbearingydayavg = $array[81];
        $jobj->windbearingyearavg = $array[82];
        $jobj->humidityydmax = $array[83];
        $jobj->humidityydmaxtime = $array[84];
        $jobj->humidityydmin = $array[85];
        $jobj->humidityydmintime = $array[86];
        $jobj->humiditymonthmax = $array[87];
        $jobj->humiditymonthmaxtime = $array[88];
        $jobj->humiditymonthmin = $array[89];
        $jobj->humiditymonthmintime = $array[90];
        $jobj->humidityyearmax = $array[91];
        $jobj->humidityyearmaxtime = $array[92];
        $jobj->humidityyearmin = $array[93];
        $jobj->humidityyearmintime = $array[94];
        $jobj->sealevelpressureydmax = $array[95];
        $jobj->sealevelpressureydmaxtime = $array[96];
        $jobj->sealevelpressureydmin = $array[97];
        $jobj->sealevelpressureydmintime = $array[98];
        $jobj->sealevelpressuremonthmax = $array[99];
        $jobj->sealevelpressuremonthmaxtime = $array[100];
        $jobj->sealevelpressuremonthmin = $array[101];
        $jobj->sealevelpressuremonthmintime = $array[102];
        $jobj->sealevelpressureyearmax = $array[103];
        $jobj->sealevelpressureyearmaxtime = $array[104];
        $jobj->sealevelpressureyearmin = $array[105];
        $jobj->sealevelpressureyearmintime = $array[106];
        $jobj->humidityallmax = $array[107];
        $jobj->humidityallmaxtime = $array[108];
        $jobj->humidityallmin = $array[109];
        $jobj->humidityallmintime = $array[110];
        $jobj->sealevelpressureallmax = $array[111];
        $jobj->sealevelpressureallmaxtime = $array[112];
        $jobj->sealevelpressureallmin = $array[113];
        $jobj->sealevelpressureallmintime = $array[114];
        $jobj->pm10ydavg = $array[115];
        $jobj->pm25ydavg = $array[116];
        $jobj->epochtime = $array[117];
        $jobj->rainrate10minavg = $array[118];
        $jobj->rainrate10minmax = $array[119];
        $jobj->dewpointmaxday = $array[120];
        $jobj->dewpointminday = $array[121];
        $jobj->lightningenergy = $array[122];
        $jobj->lightningdistance = $array[123];
        $jobj->lightningtime = $array[124];
        $jobj->lightningtotalday = $array[125];
        $jobj->lightningtotalmonth = $array[126];
        $jobj->lightningtotalyear = $array[127];
        $jobj->lightningtotal10min = $array[128];
        $jobj->uvindex = $array[129];
        $jobj->uvindexdaymax = $array[130];
        $jobj->uvindexdaymaxtime = $array[131];
        $jobj->uvindexmonthmax = $array[132];
        $jobj->uvindexmonthmaxtime = $array[133];
        $jobj->uvindexyearmax = $array[134];
        $jobj->uvindexyearmaxtime = $array[135];
        $jobj->solarrad = $array[136];
        $jobj->solarraddaymax = $array[137];
        $jobj->solarraddaymaxtime = $array[138];
        $jobj->solarradmonthmax = $array[139];
        $jobj->solarradmonthmaxtime = $array[140];
        $jobj->solarradyearmax = $array[141];
        $jobj->solarradyearmaxtime = $array[142];
        $jobj->uvindexyesterdaymax = $array[143];
        $jobj->solarradyesterdaymax = $array[144];
        $jobj->uvindexyesterdaymaxtime = $array[145];
        $jobj->solarradyesterdaymaxtime = $array[146];
        $jobj->uvindexalltimemax = $array[147];
        $jobj->uvindexalltimemaxtime = $array[148];
        $jobj->solarradalltimemax = $array[149];
        $jobj->solarradalltimemaxtime = $array[150];
        $jobj->lightningtotalyesterday = $array[151];    
        $jobj->swversion = $array[152];
        $jobj->buildnum = $array[153];
        $jobj->mac = $array[154];
        $jobj->platform = $array[155];
        $jobj->station = $array[156];
        $jobj->uptime = $array[157];
        $jobj->status = $array[152]."-".$array[153];
        $jobj->feelslike = calcFeelsLikeTemperature($array[0],$array[9],$array[7],$array[3],$array[11]);
        $jbinobj->is_freezing = calcIsFreezing($array[0]);
        $jbinobj->is_raining = calcIsRaining($array[158]);
        $jobj->pressuretrend = $array[159];
        $jobj->pressuretrendtext = pressureTrend($array[159]);
        $jobj->is_day = $array[160];
        $jobj->rainlasthour = $array[161];
        $jobj->daylength = $array[162];
        $jobj->daylengthmin = $array[163];
        $jobj->daylengthmax = $array[164];
    }

    // Get Additional Data from Forecast File
    $fcst_filename = "forecast_visual.json";
    $forecastdata = json_decode(file_get_contents($fcst_filename), TRUE);
    $jobj->visibility = $forecastdata["currentConditions"]["visibility"];
    $jobj->condition = conditionState($forecastdata["currentConditions"]["icon"]);
    $jobj->condition_text = conditionState($forecastdata["currentConditions"]["conditions"]);
    $jobj->precipprob = $forecastdata["currentConditions"]["precipprob"];

    // Add a Daily and Hourly Forecast
    $day_fcst = [];
    $hour_fcst = [];
    $hours_added = 0;
    $hours_max = 48;
    foreach($forecastdata["days"] as $value) {
        $jobj_fcst = new StdClass;
        $epoch = $value["datetimeEpoch"];
        $dt = new DateTime("@$epoch");
        $jobj_fcst->datetime = $dt->format('c');
        $jobj_fcst->native_temperature = $value["tempmax"];
        $jobj_fcst->condition = conditionState($value["icon"]);
        $jobj_fcst->native_templow = $value["tempmin"];
        $jobj_fcst->native_precipitation = $value["precip"];
        $jobj_fcst->precipitation_probability = $value["precipprob"];
        $jobj_fcst->native_pressure = $value["pressure"];
        $jobj_fcst->wind_bearing = $value["winddir"];
        $jobj_fcst->native_wind_speed = $value["windspeed"];

        //Push to Array
        array_push($day_fcst,$jobj_fcst);

        // Loop through the Hours for this day
        foreach($value["hours"] as $hour) {            
            $jobj_hour = new StdClass;
            $epoch = $hour["datetimeEpoch"];
            if (time() < $epoch and $hours_added < $hours_max) {
                $dt = new DateTime("@$epoch");
                $jobj_hour->datetime = $dt->format('c');
                $jobj_hour->native_temperature = $hour["temp"];
                $jobj_hour->condition = conditionState($hour["icon"]);
                $jobj_hour->native_precipitation = $hour["precip"];
                $jobj_hour->precipitation_probability = $hour["precipprob"];
                $jobj_hour->native_pressure = $hour["pressure"];
                $jobj_hour->wind_bearing = $hour["winddir"];
                $jobj_hour->native_wind_speed = $hour["windspeed"];
                    
                //Push to Array
                array_push($hour_fcst, $jobj_hour);
                ++$hours_added;
            }
        }
    }
    $jfcstobj->forecast_daily = $day_fcst;
    $jfcstobj->forecast_hourly = $hour_fcst;

    // Build json array and return this
    $json_data = json_encode($jobj, JSON_NUMERIC_CHECK);
    $json_bin_data = json_encode($jbinobj, JSON_NUMERIC_CHECK);
    $json_fcst_data = json_encode($jfcstobj, JSON_NUMERIC_CHECK);

$filename = "livedata.json";
file_put_contents($filename, $json_data);header('Content-Type: application/json');echo "success";}

// Publish to MQTT
$device_serial = 'meteobridge2mqtt';
$mqtt_base_topic = 'homeassistant/sensor/';
$mqtt_binary_topic = 'homeassistant/binary_sensor/';

$mqtt = new Bluerhinos\phpMQTT($mqtt_server, $mqtt_port, $mqtt_client_id);
if ($mqtt->connect(true, NULL, $mqtt_username, $mqtt_password)) {
    // Publish State
    $mqtt->publish($mqtt_base_topic.$device_serial."/state", $json_data, 0, false);
    $mqtt->publish($mqtt_binary_topic.$device_serial."/state", $json_bin_data, 0, false);
    $mqtt->publish($mqtt_base_topic.$device_serial."/forecast", $json_fcst_data, 0, false);

    $mqtt->close();
} else {
    echo "Time out!\n";
}

function convertDegreesToWindDirection($degrees) {
	$directions = array('N', 'NNØ', 'NØ', 'ØNØ', 'Ø', 'ØSØ', 'SØ', 'SSØ', 'S', 'SSV', 'SV', 'VSV', 'V', 'VNV', 'NV', 'NNV', 'N');
	return $directions[round(intval($degrees) / 22.5)];
}

function calcFeelsLikeTemperature($temp,$humidity,$heatindex,$windchill,$windspeed) {
    if ($temp >= 26.7 and $humidity >= 40) {
        return $heatindex;
    }

    if ($temp <= 10 and $windspeed >= 1.3411) {
        return $windchill;
    }

    return $temp;
}

function calcIsFreezing($temp) {

    if ($temp < 0) {
        return 'on';
    }

    return 'off';
}

function calcIsRaining($rate) {

    if ($rate > 0) {
        return 'on';
    }

    return 'off';
}

function conditionState($icon) {
    if ($icon == "clear-day") { return "sunny";}
    if ($icon == "partly-cloudy-day") { return "partlycloudy";}
    if ($icon == "partly-cloudy-night") { return "partlycloudy";}
    if ($icon == "showers-day") { return "rainy";}
    if ($icon == "showers-night") { return "rainy";}
    if ($icon == "thunder-showers-day") { return "lightning-rainy";}
    if ($icon == "thunder-showers-night") { return "lightning-rainy";}
    if ($icon == "rain") { return "rainy";}
    return $icon;
}

function pressureTrend($value) {
    if ($value < 0) {return "Falder";}
    if ($value > 0) {return "Stiger";}
    return "Stabil";
}

function aqiFromPM($pm) 
{
	/*                                  AQI         RAW PM2.5
	Good                               0 - 50   |   0.0 – 12.0
	Moderate                          51 - 100  |  12.1 – 35.4
	Unhealthy for Sensitive Groups   101 – 150  |  35.5 – 55.4
	Unhealthy                        151 – 200  |  55.5 – 150.4
	Very Unhealthy                   201 – 300  |  150.5 – 250.4
	Hazardous                        301 – 400  |  250.5 – 350.4
	Hazardous                        401 – 500  |  350.5 – 500.4
	*/

	if ($pm > 350.5) 
	{
		return calcAQI($pm, 500, 401, 500.4, 350.5); // Hazardous
	} 
	else if ($pm > 250.5) 
	{
		return calcAQI($pm, 400, 301, 350.4, 250.5); // Hazardous
	} 
	else if ($pm > 150.5) 
	{
		return calcAQI($pm, 300, 201, 250.4, 150.5); // Very Unhealthy
	} 
	else if ($pm > 55.5) 
	{
		return calcAQI($pm, 200, 151, 150.4, 55.5); // Unhealthy
	} 
	else if ($pm > 35.5) 
	{
		return calcAQI($pm, 150, 101, 55.4, 35.5); // Unhealthy for Sensitive Groups
	} 
	else if ($pm > 12.1) 
	{
		return calcAQI($pm, 100, 51, 35.4, 12.1); // Moderate
	} 
	else if ($pm >= 0) 
	{
		return calcAQI($pm, 50, 0, 12, 0); // Good
	}
}

function calcAQI($Cp, $Ih, $Il, $BPh, $BPl) 
{
	$a = ($Ih - $Il);
	$b = ($BPh - $BPl);
	$c = ($Cp - $BPl);
	return round(($a/$b) * $c + $Il);
}

?>