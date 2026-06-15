"""This module contains the code to get weather data from an MYSQL Table."""

from __future__ import annotations

import logging
from typing import Any, cast

import mysql.connector
from mysql.connector.abstracts import MySQLConnectionAbstract, MySQLCursorAbstract

from .data import (
    DailyData,
    ForecastDaily,
    ForecastHourly,
    MinuteData,
    MonthlyData,
    RealtimeData,
    StationData,
)

_LOGGER = logging.getLogger(__name__)


class MeteobridgeSQLDatabaseConnectionError(Exception):
    """Cannot connect to database."""


class MeteobridgeSQLDataError(Exception):
    """Cannot lookup data in the database."""


class MeteobridgeSQL:
    """Class that interfaces with a MySQL database, with weather data supplied by Meteobridge."""

    def __init__(
        self,
        host: str,
        user: str,
        password: str,
        database: str,
        port: int = 3306,
    ) -> None:
        """Initialize the connection."""
        self._host = host
        self._user = user
        self._password = password
        self._database = database
        self._port = port

        self._weatherdb: MySQLConnectionAbstract | None = None
        self._weather_cursor: MySQLCursorAbstract | None = None

    @property
    def _cursor(self) -> MySQLCursorAbstract:
        if self._weather_cursor is None:
            raise MeteobridgeSQLDatabaseConnectionError(
                "Not initialized. Call initialize() or async_init() first."
            )
        return self._weather_cursor

    def initialize(self) -> None:
        """Initialize the connection."""
        try:
            conn = cast(
                MySQLConnectionAbstract,
                mysql.connector.connect(
                    host=self._host,
                    user=self._user,
                    password=self._password,
                    database=self._database,
                    port=self._port,
                ),
            )
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDatabaseConnectionError(
                f"Failed to connect to the database: {err.msg}"
            ) from err

        self._weatherdb = conn
        self._weather_cursor = conn.cursor()

    async def async_init(self) -> None:
        """Initialize the connection."""
        try:
            conn = cast(
                MySQLConnectionAbstract,
                mysql.connector.connect(
                    host=self._host,
                    user=self._user,
                    password=self._password,
                    database=self._database,
                    port=self._port,
                ),
            )
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDatabaseConnectionError(
                f"Failed to connect to the database: {err.msg}"
            ) from err

        self._weatherdb = conn
        self._weather_cursor = conn.cursor()

    async def async_disconnect(self) -> None:
        """Disconnect from the database."""
        if self._weather_cursor is not None:
            self._weather_cursor.close()
        if self._weatherdb is not None:
            self._weatherdb.close()

    async def async_get_realtime_data(self, station_id: str) -> RealtimeData:
        """Get the latest data from the database."""
        try:
            self._cursor.execute(f"SELECT * FROM realtime_data WHERE ID = '{station_id}'")
            row: Any = self._cursor.fetchone()
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDataError(f"Failed to lookup data in the database: {err.msg}") from err

        if row is None:
            raise MeteobridgeSQLDataError(f"No realtime data found for station ID: {station_id}")

        return RealtimeData(*row)

    async def async_get_station_data(self, station_id: str) -> StationData:
        """Get station data from the database."""
        try:
            self._cursor.execute(
                "SELECT ID, mb_ip, mb_swversion, mb_buildnum, mb_platform, mb_station, mb_stationname "
                f"FROM realtime_data WHERE ID = '{station_id}'"
            )
            row: Any = self._cursor.fetchone()
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDataError(f"Failed to lookup data in the database: {err.msg}") from err

        if row is None:
            raise MeteobridgeSQLDataError(f"No station data found for station ID: {station_id}")

        return StationData(*row)

    async def async_get_forecast(self, hourly: bool = False) -> list[ForecastHourly] | list[ForecastDaily]:
        """Get the latest forecast."""
        if hourly:
            try:
                self._cursor.execute("SELECT * FROM forecast_hourly WHERE `datetime` >= NOW() LIMIT 48;")
                rows: list[Any] = self._cursor.fetchall()
            except mysql.connector.Error as err:
                raise MeteobridgeSQLDataError(
                    f"Failed to lookup hourly forecast in the database: {err.msg}"
                ) from err
            return [ForecastHourly(*row) for row in rows]

        try:
            self._cursor.execute("SELECT * FROM forecast_daily;")
            rows = self._cursor.fetchall()
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDataError(
                f"Failed to lookup daily forecast in the database: {err.msg}"
            ) from err
        return [ForecastDaily(*row) for row in rows]

    async def async_get_minute_data(self, interval: str = "24") -> list[MinuteData]:
        """Get data from the Minute Data table."""
        try:
            self._cursor.execute(
                f"SELECT * FROM viewMinuteData WHERE `logdate` > NOW() - INTERVAL {interval} HOUR;"
            )
            rows: list[Any] = self._cursor.fetchall()
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDataError(
                f"Failed to lookup data from the minute_data table in the database: {err.msg}"
            ) from err

        return [MinuteData(*row) for row in rows]

    async def async_get_daily_data(self, interval: str = "31") -> list[DailyData]:
        """Get data from the Daily Data table."""
        try:
            self._cursor.execute(
                f"SELECT * FROM viewDailyData WHERE `logdate` > NOW() - INTERVAL {interval} DAY;"
            )
            rows: list[Any] = self._cursor.fetchall()
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDataError(
                f"Failed to lookup data from the daily_data table in the database: {err.msg}"
            ) from err

        return [DailyData(*row) for row in rows]

    async def async_get_monthly_data(self, interval: str = "1") -> list[MonthlyData]:
        """Get data from the Monthly Data table."""
        try:
            self._cursor.execute(
                f"SELECT * FROM monthly_data WHERE `logdate` > NOW() - INTERVAL {interval} YEAR;"
            )
            rows: list[Any] = self._cursor.fetchall()
        except mysql.connector.Error as err:
            raise MeteobridgeSQLDataError(
                f"Failed to lookup data from the monthly_data table in the database: {err.msg}"
            ) from err

        return [MonthlyData(*row) for row in rows]
