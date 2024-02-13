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