# ruff: noqa: F401
"""Python module to interface with Weather Data in a MySQL database."""

from __future__ import annotations

from .api import MeteobridgeSQL
from .data import RealtimeData

__title__ = "pymeteobridgesql"
__version__ = "1.0.0"
__author__ = "briis"
__license__ = "MIT"
