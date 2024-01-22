"""This module contains constants used by pymeteobridgesql."""

from __future__ import annotations

SQL_REALTIME = "SELECT JSON_OBJECT('temperature', temperature, 'tempmax', tempmax, 'tempmin', tempmin, 'windchill', windchill, 'pm1', pm1, 'pm25', pm25, 'pm10', pm10, 'heatindex', heatindex, 'temp15min', temp15min, 'humidity', humidity, 'windspeedavg', windspeedavg, 'windgust', windgust, 'dewpoint', dewpoint, 'rainrate', rainrate, 'raintoday', raintoday, 'rainyesterday', rainyesterday, 'windbearing', windbearing, 'beaufort', beaufort, 'sealevelpressure', sealevelpressure, 'uv', uv, 'uvdaymax', uvdaymax, 'solarrad', solarrad, 'solarraddaymax', solarraddaymax, 'pressuretrend', pressuretrend)"
