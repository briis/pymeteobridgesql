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

    @property
    def wind_direction(self) -> str:
        """Calculates the wind direction from the wind bearing."""
        if self.windbearing is None:
            return None

        directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW']
        index = round(self.windbearing / 22.5) % 16
        return directions[index]

    @property
    def feels_like_temperature(self) -> float:
        """Calculate feels like temperature using windchill and heatindex."""
        if self.windchill is not None and self.heatindex is not None and self.temperature is not None and self.humidity is not None and self.windspeedavg is not None:
            if self.temperature > 26.7 and self.humidity > 40:
                return self.heatindex
            if self.temperature < 10 and self.windspeedavg > 4.8:
                return self.windchill
            return self.temperature
        return None

    @property
    def pressuretrend_text(self) -> str:
        """Converts the pressure trend to text."""
        if self.pressuretrend is None:
            return None

        if self.pressuretrend > 0:
            return "rising"
        if self.pressuretrend < 0:
            return "falling"
        return "steady"