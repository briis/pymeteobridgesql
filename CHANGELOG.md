
# Release 1.4.0

**Date**: `2025-02-23`

## What Changed

* Added two new fields to the realtime table. `windbearingavg10` and `windbearingdavg`. The two fields represent the last 10 minute average wind bearing and the daily average wind bearing. This is a breaking change as these fields need to be manually created in the table as follows:

  `windbearingavg10` INT NULL DEFAULT 'NULL' ,
  `windbearingdavg` INT NULL DEFAULT 'NULL' ,


# Release 1.3.3

**Date**: `2025-01-12`

## What Changed

* Added Non Async Initialize Method to fix Blocking Errorsin Home Assistant 2025.1

# Release 1.3.2

**Date**: `2025-01-12`

## What Changed

* If PM2.5 is 0, we should return 0 as AQI and None

# Release 1.3.1

**Date**: `2025-01-12`

## What Changed

* Fixing wrong dataset returned from Station Data

# Release 1.3.0

**Date**: `2024-05-25`

## What Changed

* Added function to get data from `daily_data` using a view to get calculated values also.


# Release 1.2.5

**Date**: `2024-05-14`

## What Changed

* Now retrieving `minute_data` from View instead of Table to be able to get calculated fields


# Release 1.2.4


**Date**: `2024-05-13`

## What Changed

* Added `visibility` to Minute Data


# Release 1.2.3

**Date**: `2024-05-06`

## What Changed

* Added `rain_hour` to Minute Data

# Release 1.2.2

**Date**: `2024-05-05`

## What Changed

* Added `visibility` to Hourly Forecast

# Release 1.2.1

**Date**: `2024-05-01`

## What Changed

* Added `conditions` to Daily Forecast

# Release 1.2.0

**Date**: `2024-04-29`

## What Changed

* Added dataclass for `MonthlyData` and `MinuteData`
* Added Functions to retrieve data from the above two tables

# Release 1.1.7

**Date**: `2024-04-27`

## What Changed

- Added daily sunrise and sunset values to forecast
- Added `to_json` function to alle data tables

# Release 1.1.4

**Date**: `2024-03-06`

## What Changed

- Added missing icon for `snow-showers-day` and `snow-showers-night`.


# Release 1.1.2

**Date**: `2024-02-13`

## Changes

- Added conditions to `realtime` table.


**Date**: `2024-02-10`

## Changes

- Ensure we always retrieve the next 48 hours of Hourly Forecast data.



# Release 1.1.1

**Date**: `2024-02-10`

## Changes

- Added new function `async_disconnect` to ensure connection to the database is closed.
- Converted the date field in the `ForecastDaily` table to a `datetime`


# Release 1.1.0

**Date**: `2024-02-10`

## Changes

- Added new function `async_get_forecast` to get etiher the hourly our daily weather forecast. To get Hourly data use `True` as parameter.


# Release 1.0.7

**Date**: `2024-02-10`

## Changes

- Added condition icon and forecast description to realtime data
- Preparing for adding Weather Forecast from Visual Crossing


# Release 1.0.6

**Date**: `2024-02-08`

## Changes

- Added cloud base as calculated sensor

# Release 1.0.5

**Date**: `2024-01-28`

## Changes

- Added Air Quality calculated sensor (AQI)

# Release 1.0.4

**Date**: `2024-01-26`

## Changes

- Made direction string lowercase to conform with Home Assistant requirements


# Release 1.0.3

**Date**: `2024-01-26`

## Changes

- Added Absolute Humidity calculated sensor
- Added Freezing Altitude calculated sensor
- Added Visibility calculated sensor

# Release 1.0.2

**Date**: `2024-01-26`

## Changes

- Added beaufort and uv index textual description sensors
- Added elevation as field. Must be manually set on installation