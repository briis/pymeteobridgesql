"""
AQI Calculator for PM2.5 and PM10
Based on US EPA breakpoints (revised 2024).
Intended for use in a Home Assistant custom component.

Usage:
    from .aqi_calculator import calculate_aqi, AQIResult

    result = calculate_aqi(pm25=12.4, pm10=30.0)
    print(result.aqi)        # e.g. 51
    print(result.category)   # e.g. "Moderate"
    print(result.pollutant)  # e.g. "PM2.5"
    print(result.color)      # e.g. "#FFFF00"
"""

from dataclasses import dataclass

# ---------------------------------------------------------------------------
# Breakpoint tables: (C_low, C_high, AQI_low, AQI_high)
# EPA PM2.5 24-hour breakpoints (revised Jan 2024, lowered Good/Moderate boundary)
# ---------------------------------------------------------------------------

_PM25_BREAKPOINTS = [
    (0.0, 9.0, 0, 50),
    (9.1, 35.4, 51, 100),
    (35.5, 55.4, 101, 150),
    (55.5, 125.4, 151, 200),
    (125.5, 225.4, 201, 300),
    (225.5, 325.4, 301, 500),
]

_PM10_BREAKPOINTS = [
    (0, 54, 0, 50),
    (55, 154, 51, 100),
    (155, 254, 101, 150),
    (255, 354, 151, 200),
    (355, 424, 201, 300),
    (425, 604, 301, 500),
]

AQI_CATEGORIES = [
    (0, 50, "Good", "#00E400"),
    (51, 100, "Moderate", "#FFFF00"),
    (101, 150, "Unhealthy for Sensitive Groups", "#FF7E00"),
    (151, 200, "Unhealthy", "#FF0000"),
    (201, 300, "Very Unhealthy", "#8F3F97"),
    (301, 500, "Hazardous", "#7E0023"),
]


@dataclass
class AQIResult:
    """Holds the calculated AQI and associated metadata."""

    aqi: int
    category: str
    color: str
    pollutant: str  # Which pollutant drove the AQI ("PM2.5" or "PM10")
    pm25_aqi: int | None  # Individual AQI for PM2.5 (None if input was None)
    pm10_aqi: int | None  # Individual AQI for PM10 (None if input was None)


def _find_breakpoint(concentration: float, breakpoints: list) -> tuple | None:
    """Return the matching breakpoint row for a given concentration."""
    for c_low, c_high, aqi_low, aqi_high in breakpoints:
        if c_low <= concentration <= c_high:
            return c_low, c_high, aqi_low, aqi_high
    return None


def _linear_interpolation(concentration: float, breakpoints: list) -> int | None:
    """
    Apply the EPA piecewise linear formula:
        AQI = ((AQI_high - AQI_low) / (C_high - C_low)) * (C - C_low) + AQI_low
    Returns None if concentration is out of range.
    """
    row = _find_breakpoint(concentration, breakpoints)
    if row is None:
        return None
    c_low, c_high, aqi_low, aqi_high = row
    aqi = ((aqi_high - aqi_low) / (c_high - c_low)) * (concentration - c_low) + aqi_low
    return round(aqi)


def _truncate_pm25(value: float) -> float:
    """EPA requires PM2.5 to be truncated to one decimal place."""
    return int(value * 10) / 10


def _truncate_pm10(value: float) -> float:
    """EPA requires PM10 to be truncated to integer."""
    return int(value)


def _aqi_to_category(aqi: int) -> tuple[str, str]:
    """Return (category_name, hex_color) for a given AQI value."""
    for low, high, name, color in AQI_CATEGORIES:
        if low <= aqi <= high:
            return name, color
    # Beyond 500 — treat as Hazardous
    return "Hazardous", "#7E0023"


def calculate_aqi(
    pm25: float | None = None,
    pm10: float | None = None,
) -> AQIResult | None:
    """
    Calculate AQI from PM2.5 and/or PM10 concentrations.

    Args:
        pm25: PM2.5 concentration in µg/m³ (24-hour average recommended).
        pm10: PM10 concentration in µg/m³ (24-hour average recommended).

    Returns:
        AQIResult with the dominant (highest) AQI value, or None if both inputs
        are None or out of range.

    Notes:
        - PM1 is not part of the EPA AQI standard and is ignored.
        - For real-time sensors, consider passing a 24-hour rolling average or
          a NowCast-weighted average rather than instantaneous readings.
    """
    pm25_aqi: int | None = None
    pm10_aqi: int | None = None

    if pm25 is not None:
        pm25_aqi = _linear_interpolation(_truncate_pm25(pm25), _PM25_BREAKPOINTS)

    if pm10 is not None:
        pm10_aqi = _linear_interpolation(_truncate_pm10(pm10), _PM10_BREAKPOINTS)

    # Determine dominant pollutant (highest AQI wins)
    candidates = {
        "PM2.5": pm25_aqi,
        "PM10": pm10_aqi,
    }
    valid = {k: v for k, v in candidates.items() if v is not None}

    if not valid:
        return None

    dominant_pollutant = max(valid, key=lambda k: valid[k])
    dominant_aqi = valid[dominant_pollutant]
    category, color = _aqi_to_category(dominant_aqi)

    return AQIResult(
        aqi=dominant_aqi,
        category=category,
        color=color,
        pollutant=dominant_pollutant,
        pm25_aqi=pm25_aqi,
        pm10_aqi=pm10_aqi,
    )


# ---------------------------------------------------------------------------
# NowCast helper — useful for real-time / recent sensor data
# ---------------------------------------------------------------------------


def nowcast_average(hourly_readings: list[float | None]) -> float | None:
    """
    Compute a NowCast weighted average from up to 12 hours of hourly PM readings.

    Args:
        hourly_readings: List of up to 12 values, index 0 = most recent hour.
                         Pass None for missing hours.

    Returns:
        NowCast concentration (float), or None if insufficient data
        (fewer than 2 valid readings in the last 3 hours).

    Reference: https://www.airnow.gov/publications/air-quality-index/technical-assistance-document-for-reporting-the-daily-aqi/
    """
    readings = hourly_readings[:12]

    # Require at least 2 valid readings in the most recent 3 hours
    recent = [r for r in readings[:3] if r is not None]
    if len(recent) < 2:
        return None

    valid = [r for r in readings if r is not None]
    if not valid:
        return None

    c_max = max(valid)
    c_min = min(valid)

    if c_max == 0:
        return 0.0

    rate = (c_max - c_min) / c_max

    # Weight factor: clamp between 0.5 and 1.0
    weight = max(0.5, 1.0 - rate)

    weighted_sum = 0.0
    weight_total = 0.0
    for i, val in enumerate(readings):
        if val is not None:
            w = weight**i
            weighted_sum += val * w
            weight_total += w

    return weighted_sum / weight_total if weight_total > 0 else None
