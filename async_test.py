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
from pymeteobridgesql import MeteobridgeSQL

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

    weather = MeteobridgeSQL(_host, _user, _password, _database)
    await weather.async_init()

    result = await weather.async_get_data(_id, "realtime_data")

    print(result)

    end = time.time()

    _LOGGER.info("Execution time: %s seconds", round(end - start, 3))

asyncio.run(main())