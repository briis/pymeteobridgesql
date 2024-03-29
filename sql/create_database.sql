CREATE TABLE `realtime_data` (
  `ID` VARCHAR(20) NOT NULL,
  `temperature` FLOAT NULL DEFAULT NULL ,
  `tempmax` FLOAT NULL DEFAULT NULL ,
  `tempmin` FLOAT NULL DEFAULT NULL ,
  `windchill` FLOAT NULL DEFAULT NULL ,
  `pm1` FLOAT NULL DEFAULT NULL ,
  `pm25` FLOAT NULL DEFAULT NULL ,
  `pm10` FLOAT NULL DEFAULT NULL ,
  `heatindex` FLOAT NULL DEFAULT NULL ,
  `temp15min` FLOAT NULL DEFAULT NULL ,
  `humidity` INT NULL DEFAULT NULL ,
  `windspeedavg` FLOAT NULL DEFAULT NULL ,
  `windgust` FLOAT NULL DEFAULT NULL ,
  `dewpoint` FLOAT NULL DEFAULT NULL ,
  `rainrate` FLOAT NULL DEFAULT NULL ,
  `raintoday` FLOAT NULL DEFAULT NULL ,
  `rainyesterday` FLOAT NULL DEFAULT NULL ,
  `windbearing` INT NULL DEFAULT NULL ,
  `beaufort` INT NULL DEFAULT NULL ,
  `sealevelpressure` FLOAT NULL DEFAULT NULL ,
  `uv` FLOAT NULL DEFAULT NULL ,
  `uvdaymax` FLOAT NULL DEFAULT NULL ,
  `solarrad` FLOAT NULL DEFAULT NULL ,
  `solarraddaymax` FLOAT NULL DEFAULT NULL ,
  `pressuretrend` FLOAT NULL DEFAULT NULL ,
  `mb_ip` VARCHAR(20) NULL DEFAULT NULL ,
  `mb_swversion` VARCHAR(20) NULL DEFAULT NULL ,
  `mb_buildnum` VARCHAR(10) NULL DEFAULT NULL ,
  `mb_platform` VARCHAR(20) NULL DEFAULT NULL ,
  `mb_station` VARCHAR(20) NULL DEFAULT NULL ,
  `mb_stationname` VARCHAR(50) NULL DEFAULT NULL ,
  `elevation` INT NULL DEFAULT NULL ,
  `description` VARCHAR(250) NULL DEFAULT NULL ,
  `icon` VARCHAR(50) NULL DEFAULT NULL ,
  `conditions` VARCHAR(50) NULL DEFAULT NULL ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`ID`),
  CONSTRAINT `primary_index` UNIQUE (`ID`)
);
DROP TABLE IF EXISTS `forecast_daily`;
CREATE TABLE `forecast_daily` (
  `day_num` INT NOT NULL,
  `datetime` DATETIME NULL DEFAULT NULL ,
  `temperature` FLOAT NULL DEFAULT NULL ,
  `temp_low` FLOAT NULL DEFAULT NULL ,
  `description` VARCHAR(250) NULL DEFAULT NULL ,
  `icon` VARCHAR(50) NULL DEFAULT NULL ,
  `precipitation_probability` INT NULL DEFAULT NULL ,
  `precipitation` FLOAT NULL DEFAULT NULL ,
  `pressure` FLOAT NULL DEFAULT NULL ,
  `wind_bearing` INT NULL DEFAULT NULL ,
  `wind_speed` FLOAT NULL DEFAULT NULL ,
  `wind_gust` FLOAT NULL DEFAULT NULL ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`day_num`)
);

CREATE TABLE `forecast_hourly` (
  `hour_num` INT NOT NULL,
  `datetime` DATETIME NULL DEFAULT NULL ,
  `temperature` FLOAT NULL DEFAULT NULL ,
  `apparent_temperature` FLOAT NULL DEFAULT NULL ,
  `humidity` INT NULL DEFAULT NULL ,
  `description` VARCHAR(250) NULL DEFAULT NULL ,
  `icon` VARCHAR(50) NULL DEFAULT NULL ,
  `precipitation_probability` INT NULL DEFAULT NULL ,
  `precipitation` FLOAT NULL DEFAULT NULL ,
  `pressure` FLOAT NULL DEFAULT NULL ,
  `wind_bearing` INT NULL DEFAULT NULL ,
  `wind_speed` FLOAT NULL DEFAULT NULL ,
  `wind_gust` FLOAT NULL DEFAULT NULL ,
  `uv_index` FLOAT NULL DEFAULT NULL ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`hour_num`)
);

-- Once the empty table has been created you need to insert a row, that then later can be updated from the Meteobridge device
INSERT INTO `realtime_data` (`ID`, `mb_stationname`, `elevation`) VALUES ('MAC_ADDRESS_OF_METEOBRIDGE', 'YOUR_STATION_NAME', elevation_about_sealevel_in_meters);


-- This is the string used on the Meteobridge Service settings page to update the values. You must create a row with the [mbsystem-mac:0] as ID before starting
UPDATE `realtime_data` SET `temperature` = '[th0temp-act:0]',`tempmax` = '[th0temp-dmax:0]', `tempmin` = '[th0temp-dmin:0]', `windchill` = '[wind0chill-act:0]', `pm1` = '[air0pm-act:0]', `pm25` = '[air1pm-act:0]', `pm10` = '[air2pm-act:0]', `heatindex` = '[th0heatindex-act:0]', `temp15min` = '[th0temp-val15:0]', `humidity` = '[th0hum-act:0]', `windspeedavg` = '[wind0avgwind-act:0]', `windgust` = '[wind0wind-max10:0]', `dewpoint` = '[th0dew-act:0]', `rainrate` = '[rain0rate-act:0]', `raintoday` = '[rain0total-daysum:0]', `rainyesterday` = '[rain0total-ydaysum:0]', `windbearing` = '[wind0dir-act:0]', `beaufort` = '[wind0wind-act=bft.0:0]', `sealevelpressure` = '[thb0seapress-act:0]', `uv` = '[uv0index-act:0]', `uvdaymax` = '[uv0index-dmax:0]', `solarrad` = '[sol0rad-act:0]', `solarraddaymax` = '[sol0rad-dmax:0]', `pressuretrend` = '[thb0seapress-delta3h:0]', `mb_ip` = '[mbsystem-ip:-]', `mb_swversion` = '[mbsystem-swversion:-]', `mb_buildnum` = '[mbsystem-buildnum:-]', `mb_platform` = '[mbsystem-platform:-]', `mb_station` = '[mbsystem-station:-]' WHERE `ID` = '[mbsystem-mac:0]'

INSERT INTO `forecast_daily` (`day_num`, `datetime`, `temperature`, `temp_low`, `condition`, `icon`, `precipitation_probability`, `precipitation`, `wind_bearing`, `wind_speed`, `wind_gust`)
VALUES (day_num_value, 'date_value', temperature_value, temp_low_value, 'condition_value', 'icon_value', precipitation_probability_value, precipitation_value, wind_bearing_value, wind_speed_value, wind_gust_value)
ON DUPLICATE KEY UPDATE
`date` = 'date_value',
`temperature` = temperature_value,
`temp_low` = temp_low_value,
`condition` = 'condition_value',
`icon` = 'icon_value',
`precipitation_probability` = precipitation_probability_value,
`precipitation` = precipitation_value,
`wind_bearing` = wind_bearing_value,
`wind_speed` = wind_speed_value,
`wind_gust` = wind_gust_value;
