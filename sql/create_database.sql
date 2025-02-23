DROP TABLE IF EXISTS `realtime_data`;
CREATE TABLE `realtime_data` (
  `ID` VARCHAR(20) NOT NULL,
  `temperature` FLOAT NULL DEFAULT 'NULL' ,
  `tempmax` FLOAT NULL DEFAULT 'NULL' ,
  `tempmin` FLOAT NULL DEFAULT 'NULL' ,
  `windchill` FLOAT NULL DEFAULT 'NULL' ,
  `pm1` FLOAT NULL DEFAULT 'NULL' ,
  `pm25` FLOAT NULL DEFAULT 'NULL' ,
  `pm10` FLOAT NULL DEFAULT 'NULL' ,
  `heatindex` FLOAT NULL DEFAULT 'NULL' ,
  `temp15min` FLOAT NULL DEFAULT 'NULL' ,
  `humidity` INT NULL DEFAULT 'NULL' ,
  `windspeedavg` FLOAT NULL DEFAULT 'NULL' ,
  `windgust` FLOAT NULL DEFAULT 'NULL' ,
  `dewpoint` FLOAT NULL DEFAULT 'NULL' ,
  `rainrate` FLOAT NULL DEFAULT 'NULL' ,
  `raintoday` FLOAT NULL DEFAULT 'NULL' ,
  `rainyesterday` FLOAT NULL DEFAULT 'NULL' ,
  `windbearing` INT NULL DEFAULT 'NULL' ,
  `windbearingavg10` INT NULL DEFAULT 'NULL' ,
  `windbearingdavg` INT NULL DEFAULT 'NULL' ,
  `beaufort` INT NULL DEFAULT 'NULL' ,
  `sealevelpressure` FLOAT NULL DEFAULT 'NULL' ,
  `uv` FLOAT NULL DEFAULT 'NULL' ,
  `uvdaymax` FLOAT NULL DEFAULT 'NULL' ,
  `solarrad` FLOAT NULL DEFAULT 'NULL' ,
  `solarraddaymax` FLOAT NULL DEFAULT 'NULL' ,
  `pressuretrend` FLOAT NULL DEFAULT 'NULL' ,
  `mb_ip` VARCHAR(20) NULL DEFAULT 'NULL' ,
  `mb_swversion` VARCHAR(20) NULL DEFAULT 'NULL' ,
  `mb_buildnum` VARCHAR(10) NULL DEFAULT 'NULL' ,
  `mb_platform` VARCHAR(20) NULL DEFAULT 'NULL' ,
  `mb_station` VARCHAR(20) NULL DEFAULT 'NULL' ,
  `mb_stationname` VARCHAR(50) NULL DEFAULT 'NULL' ,
  `elevation` INT NULL DEFAULT 'NULL' ,
  `description` VARCHAR(250) NULL DEFAULT 'NULL' ,
  `icon` VARCHAR(50) NULL DEFAULT 'NULL' ,
  `conditions` VARCHAR(50) NULL DEFAULT 'NULL' ,
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
  `sunriseepoch` INT NULL DEFAULT NULL ,
  `sunsetepoch` INT NULL DEFAULT NULL ,
  `wind_bearing` INT NULL DEFAULT NULL ,
  `wind_speed` FLOAT NULL DEFAULT NULL ,
  `wind_gust` FLOAT NULL DEFAULT NULL ,
  `conditions` VARCHAR(50) NULL DEFAULT NULL ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`day_num`)
);

DROP TABLE IF EXISTS `forecast_hourly`;
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
  `visibility` FLOAT NULL DEFAULT NULL ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`hour_num`)
);

DROP TABLE IF EXISTS `daily_data`;
CREATE TABLE `daily_data` (
  `logdate` DATE NOT NULL,
  `temperature_low` FLOAT NULL DEFAULT 'NULL' ,
  `temperature_high` FLOAT NULL DEFAULT 'NULL' ,
  `humidity_low` INT NULL DEFAULT 'NULL' ,
  `humidity_high` INT NULL DEFAULT 'NULL' ,
  `rain_total` FLOAT NULL DEFAULT 'NULL' ,
  `wind_speed_max` FLOAT NULL DEFAULT 'NULL' ,
  `wind_speed_avg` FLOAT NULL DEFAULT 'NULL' ,
  `wind_direction_avg` INT NULL DEFAULT 'NULL' ,
  `uvindex_max` FLOAT NULL DEFAULT 'NULL' ,
  `solar_radiation_max` FLOAT NULL DEFAULT 'NULL' ,
  `pressure_low` FLOAT NULL DEFAULT 'NULL' ,
  `pressure_high` FLOAT NULL DEFAULT 'NULL' ,
  `air_quality_low` FLOAT NULL DEFAULT 'NULL' ,
  `air_quality_high` FLOAT NULL DEFAULT 'NULL' ,
  `dewpoint_low` FLOAT NULL DEFAULT 'NULL' ,
  `dewpoint_high` FLOAT NULL DEFAULT 'NULL' ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`logdate`)
);

DROP TABLE IF EXISTS `minute_data`;
CREATE TABLE `minute_data` (
  `logdate` DATETIME NOT NULL,
  `temperature` FLOAT NULL DEFAULT 'NULL' ,
  `wind_chill` FLOAT NULL DEFAULT 'NULL' ,
  `air_Quality_pm1` FLOAT NULL DEFAULT 'NULL' ,
  `air_Quality_pm10` FLOAT NULL DEFAULT 'NULL' ,
  `air_Quality_pm25` FLOAT NULL DEFAULT 'NULL' ,
  `heat_index` FLOAT NULL DEFAULT 'NULL' ,
  `humidity` INT NULL DEFAULT 'NULL' ,
  `dewpoint` FLOAT NULL DEFAULT 'NULL' ,
  `rain_rate` FLOAT NULL DEFAULT 'NULL' ,
  `rain_day` FLOAT NULL DEFAULT 'NULL' ,
  `rain_hour` FLOAT NULL DEFAULT 'NULL' ,
  `wind_speed` FLOAT NULL DEFAULT 'NULL' ,
  `wind_gust` FLOAT NULL DEFAULT 'NULL' ,
  `wind_bearing` INT NULL DEFAULT 'NULL' ,
  `pressure` FLOAT NULL DEFAULT 'NULL' ,
  `pressure_trend` FLOAT NULL DEFAULT 'NULL' ,
  `uv` FLOAT NULL DEFAULT 'NULL' ,
  `solar_radiation` FLOAT NULL DEFAULT 'NULL' ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`logdate`),
  CONSTRAINT `date_index` UNIQUE (`logdate`)
);

DROP TABLE IF EXISTS `monthly_data`;
CREATE TABLE `monthly_data` (
  `logdate` DATE NOT NULL,
  `temperature_low` FLOAT NULL DEFAULT 'NULL' ,
  `temperature_high` FLOAT NULL DEFAULT 'NULL' ,
  `humidity_low` INT NULL DEFAULT 'NULL' ,
  `humidity_high` INT NULL DEFAULT 'NULL' ,
  `rain_total` FLOAT NULL DEFAULT 'NULL' ,
  `wind_speed_max` FLOAT NULL DEFAULT 'NULL' ,
  `wind_speed_avg` FLOAT NULL DEFAULT 'NULL' ,
  `wind_direction_avg` INT NULL DEFAULT 'NULL' ,
  `uvindex_max` FLOAT NULL DEFAULT 'NULL' ,
  `solar_radiation_max` FLOAT NULL DEFAULT 'NULL' ,
  `pressure_low` FLOAT NULL DEFAULT 'NULL' ,
  `pressure_high` FLOAT NULL DEFAULT 'NULL' ,
  `air_quality_low` FLOAT NULL DEFAULT 'NULL' ,
  `air_quality_high` FLOAT NULL DEFAULT 'NULL' ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`logdate`)
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

CREATE FUNCTION `calcVisibility`()
RETURNS float DETERMINISTIC
BEGIN
	DECLARE visibility FLOAT;
    DECLARE elevation_min FLOAT;
    DECLARE max_visibility FLOAT;
    DECLARE percent_reduction FLOAT;
    DECLARE percent_reduction_a FLOAT;

    IF elevation > 2 THEN
    	SET elevation_min = elevation;
    ELSE
    	SET elevation_min = 2;
    END IF;
    SET max_visibility = 3.56972 * SQRT(elevation_min);
    SET percent_reduction_a = (1.13 * ABS(temperature - dewpoint) - 1.15) / 10;
    IF percent_reduction_a > 1 THEN
    	SET percent_reduction = 1;
    ELSEIF percent_reduction_a < 0.025 THEN
    	SET percent_reduction = 0.025;
    ELSE
    	SET percent_reduction = percent_reduction_a;
    END IF;

    RETURN max_visibility * percent_reduction;
END;

CREATE VIEW `viewMinuteData` AS select `weather_history`.`minute_data`.`logdate` AS `logdate`,`weather_history`.`minute_data`.`temperature` AS `temperature`,`weather_history`.`minute_data`.`wind_chill` AS `wind_chill`,`weather_history`.`minute_data`.`air_Quality_pm1` AS `air_Quality_pm1`,`weather_history`.`minute_data`.`air_Quality_pm10` AS `air_Quality_pm10`,`weather_history`.`minute_data`.`air_Quality_pm25` AS `air_Quality_pm25`,`weather_history`.`minute_data`.`heat_index` AS `heat_index`,`weather_history`.`minute_data`.`humidity` AS `humidity`,`weather_history`.`minute_data`.`dewpoint` AS `dewpoint`,`weather_history`.`minute_data`.`rain_rate` AS `rain_rate`,`weather_history`.`minute_data`.`rain_day` AS `rain_day`,`weather_history`.`minute_data`.`rain_hour` AS `rain_hour`,`weather_history`.`minute_data`.`wind_speed` AS `wind_speed`,`weather_history`.`minute_data`.`wind_gust` AS `wind_gust`,`weather_history`.`minute_data`.`wind_bearing` AS `wind_bearing`,`weather_history`.`minute_data`.`pressure` AS `pressure`,`weather_history`.`minute_data`.`pressure_trend` AS `pressure_trend`,`weather_history`.`minute_data`.`uv` AS `uv`,`weather_history`.`minute_data`.`solar_radiation` AS `solar_radiation`,`calcVisibility`(53,`weather_history`.`minute_data`.`temperature`,`weather_history`.`minute_data`.`dewpoint`) AS `visibility` from `weather_history`.`minute_data`;

CREATE VIEW `viewDailyData` AS
SELECT
  `logdate`,
  `temperature_low`,
  `temperature_high`,
  `humidity_low`,
  `humidity_high`,
  `rain_total`,
  `wind_speed_max`,
  `wind_speed_avg`,
  `wind_direction_avg`,
  `uvindex_max`,
  `solar_radiation_max`,
  `pressure_low`,
  `pressure_high`,
  `air_quality_low`,
  `air_quality_high`,
  `dewpoint_low`,
  `dewpoint_high`,
  `calcVisibility`(53,`weather_history`.`daily_data`.`temperature_low`,`weather_history`.`daily_data`.`dewpoint_low`) AS `visibility_low`,
  `calcVisibility`(53,`weather_history`.`daily_data`.`temperature_high`,`weather_history`.`daily_data`.`dewpoint_high`) AS `visibility_high`
FROM
  `daily_data`;

DROP TRIGGER IF EXISTS `before_insert_minute_data`;
CREATE TRIGGER `before_insert_minute_data` BEFORE INSERT ON `minute_data`
FOR EACH ROW
SET NEW.uv = (SELECT `uv` FROM `realtime_data` WHERE `ID` = '94:A4:08:E8:B0:41'), NEW.solar_radiation = (SELECT `solarrad` FROM `realtime_data` WHERE `ID` = '94:A4:08:E8:B0:41');

DROP TRIGGER IF EXISTS `before_insert_daily_data`;
CREATE TRIGGER `before_insert_daily_data` BEFORE INSERT ON `daily_data`
FOR EACH ROW
SET NEW.uvindex_max = (SELECT `uvdaymax` FROM `realtime_data` WHERE `ID` = '94:A4:08:E8:B0:41'), NEW.solar_radiation_max = (SELECT `solarraddaymax` FROM `realtime_data` WHERE `ID` = '94:A4:08:E8:B0:41');

DROP TRIGGER IF EXISTS `before_update_daily_data`;
CREATE TRIGGER `before_update_daily_data` BEFORE UPDATE ON `daily_data`
FOR EACH ROW
SET NEW.uvindex_max = (SELECT `uvdaymax` FROM `realtime_data` WHERE `ID` = '94:A4:08:E8:B0:41'), NEW.solar_radiation_max = (SELECT `solarraddaymax` FROM `realtime_data` WHERE `ID` = '94:A4:08:E8:B0:41');
