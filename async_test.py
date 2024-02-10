# ruff: noqa: F401
"""This module is only used to run some realtime data tests using the async functions, while developing the module.

Create a .env file and add STATION_ID with the id of your station and API_TOKEN with the personal Token.
"""
from __future__ import annotations

from dotenv import load_dotenv
import os
import logging
import asyncio
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

    try:
        weather = MeteobridgeSQL(_host, _user, _password, _database)
        await weather.async_init()

        # result = await weather.async_get_realtime_data(_id)
        # print("")
        # print("========================================================")
        # print("ID: ", result.ID)
        # print("ABSOLUTE HUMIDITY: ", result.absolute_humidity)
        # print("FEELS LIKE: ", result.feels_like_temperature)
        # print("FREEZING ALTITUDE: ", result.freezing_altitude)
        # print("PM2.5: ", result.pm25)
        # print("AIR QUALITY: ", result.aqi)
        # print("TEMPERATURE: ", result.temperature)
        # print("RAIN TODAY: ", result.raintoday)
        # print("VISIBILITY: ", result.visibility)
        # print("WIND BEARING: ", result.windbearing)
        # print("WIND DIRECTION: ", result.wind_direction)
        # print("WIND GUST: ", result.windgust)
        # print("")

    except Exception as err:
        print(err)

    # try:
    #     result = await weather.async_get_station_data(_id)
    #     print("")
    #     print("========================================================")
    #     print("ID: ", result.ID)
    #     print("IP: ", result.mb_ip)
    #     print("SW Version: ", result.mb_swversion)
    #     print("BUILD NUM: ", result.mb_buildnum)
    #     print("PLATFORM: ", result.mb_platform)
    #     print("STATION: ", result.mb_station)
    #     print("STATION NAME: ", result.mb_stationname)
    #     print("")

    # except Exception as err:
    #     print(err)


    try:
        result = await weather.async_get_forecast(True)
        for row in result:
            print("")
            print("========================================================")
            print("DATE: ", row.datetime)
            print("TEMP: ", row.temperature)
            print("ICON: ", row.icon)
            print("")

    except Exception as err:
        print(err)

    try:
        result = await weather.async_get_forecast(False)
        for row in result:
            print("")
            print("========================================================")
            print("DATE: ", row.datetime)
            print("TEMP: ", row.temperature)
            print("ICON: ", row.icon)
            print("CONDITION: ", row.description)
            print("")

    except Exception as err:
        print(err)

    await weather.async_disconnect()

    end = time.time()

    _LOGGER.info("Execution time: %s seconds", round(end - start, 3))

asyncio.run(main())
