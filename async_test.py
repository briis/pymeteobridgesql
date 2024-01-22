# ruff: noqa: F401
"""This module is only used to run some realtime data tests using the async functions, while developing the module.

Create a .env file and add STATION_ID with the id of your station and API_TOKEN with the personal Token.
"""
from __future__ import annotations

from dotenv import load_dotenv
import os
import logging
import asyncio
import mysql.connector
import time

_LOGGER = logging.getLogger(__name__)

async def main() -> None:
    """Async test module."""

    logging.basicConfig(level=logging.DEBUG)
    start = time.time()

    load_dotenv()
    _host = os.getenv("HOST")
    _user = os.getenv("USER")
    _password = os.getenv("PASSWORD")
    _database = os.getenv("DATABASE")
    _id = os.getenv("ID")

    weatherdb = mysql.connector.connect(
        host=_host,
        user=_user,
        password=_password,
        database=_database
    )

    weather_cursor = weatherdb.cursor()
    weather_cursor.execute(f"SELECT JSON_OBJECT ('temperature', temperature) FROM realtime_data WHERE ID = '{_id}'")

    result = weather_cursor.fetchone()

    print(result)

    end = time.time()

    _LOGGER.info("Execution time: %s seconds", round(end - start, 3))

asyncio.run(main())
