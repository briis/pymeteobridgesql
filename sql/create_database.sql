CREATE TABLE `realtime_data` ( 
  `ID` VARCHAR(20) NOT NULL,
  `temperature` FLOAT NULL DEFAULT 'NULL' ,
  `tempmax` FLOAT NULL DEFAULT 'NULL' ,
  `tempmin` FLOAT NULL DEFAULT 'NULL' ,
  `windchill` FLOAT NULL DEFAULT 'NULL' ,
  `pm1` FLOAT NULL DEFAULT 'NULL' ,
  `pm25` FLOAT NULL DEFAULT 'NULL' ,
  `pm10` FLOAT NULL DEFAULT 'NULL' ,
--  `aqi` INT NULL DEFAULT 'NULL' ,
  `heatindex` FLOAT NULL DEFAULT 'NULL' ,
  `temp15min` FLOAT NULL DEFAULT 'NULL' ,
  `humidity` INT NULL DEFAULT 'NULL' ,
  `windspeedavg` FLOAT NULL DEFAULT 'NULL' ,
  `windgust` FLOAT NULL DEFAULT 'NULL' ,
  `dewpoint` FLOAT NULL DEFAULT 'NULL' ,
  `rainrate` FLOAT NULL DEFAULT 'NULL' ,
  `raintoday` FLOAT NULL DEFAULT 'NULL' ,
  `rainyesterday` FLOAT NULL DEFAULT 'NULL' ,
--  `precipprob` INT NULL DEFAULT 'NULL' ,
  `windbearing` INT NULL DEFAULT 'NULL' ,
  `beaufort` INT NULL DEFAULT 'NULL' ,
--  `windcardinal` VARCHAR(3) NULL DEFAULT 'NULL' ,
  `sealevelpressure` FLOAT NULL DEFAULT 'NULL' ,
  `uv` FLOAT NULL DEFAULT 'NULL' ,
  `uvdaymax` FLOAT NULL DEFAULT 'NULL' ,
  `solarrad` FLOAT NULL DEFAULT 'NULL' ,
  `solarraddaymax` FLOAT NULL DEFAULT 'NULL' ,
--  `feelslike` FLOAT NULL DEFAULT 'NULL' ,
--  `visibility` INT NULL DEFAULT 'NULL' ,
--  `condition` VARCHAR(50) NULL DEFAULT 'NULL' ,
--  `conditiontext` VARCHAR(250) NULL DEFAULT 'NULL' ,
  `pressuretrend` FLOAT NULL DEFAULT 'NULL' ,
--  `pressuretrendtext` VARCHAR(10) NULL DEFAULT 'NULL' ,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`ID`),
  CONSTRAINT `primary_index` UNIQUE (`ID`)
);

-- This is the string used on the Meteobridge Service settings page to update the values. You must create a row with the [mbsystem-mac:0] as ID before starting
UPDATE `realtime_data` SET `temperature` = '[th0temp-act:0]',`tempmax` = '[th0temp-dmax:0]', `tempmin` = '[th0temp-dmin:0]', `windchill` = '[wind0chill-act:0]', `pm1` = '[air0pm-act:0]', `pm25` = '[air1pm-act:0]', `pm10` = '[air2pm-act:0]', `heatindex` = '[th0heatindex-act:0]', `temp15min` = '[th0temp-val15:0]', `humidity` = '[th0hum-act:0]', `windspeedavg` = '[wind0avgwind-act:0]', `windgust` = '[wind0wind-max10:0]', `dewpoint` = '[th0dew-act:0]', `rainrate` = '[rain0rate-act:0]', `raintoday` = '[rain0total-daysum:0]', `rainyesterday` = '[rain0total-ydaysum:0]', `windbearing` = '[wind0dir-act:0]', `beaufort` = '[wind0wind-act=bft.0:0]', `sealevelpressure` = '[thb0seapress-act:0]', `uv` = '[uv0index-act:0]', `uvdaymax` = '[uv0index-dmax:0]', `solarrad` = '[sol0rad-act:0]', `solarraddaymax` = '[sol0rad-dmax:0]', `pressuretrend` = '[thb0seapress-delta3h:0]' WHERE `ID` = '[mbsystem-mac:0]'