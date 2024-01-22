"""This module describes dataclasses used by pymeteobridgesql."""

from __future__ import annotations

import dataclasses

@dataclasses.dataclass
class RealtimeData:
  ID: str
  temperature: float
  tempmax: float
  tempmin: float
  windchill: float
  pm1: float
  pm25: float
  pm10: float
  heatindex: float
  temp15min: float
  humidity: int
  windspeedavg: float
  windgust: float
  dewpoint: float
  rainrate: float
  raintoday: float
  rainyesterday: float
  windbearing: int
  beaufort: int
  sealevelpressure: float
  uv: float
  uvdaymax: float
  solarrad: float
  solarraddaymax: float
  pressuretrend: float
