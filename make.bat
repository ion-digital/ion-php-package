@echo off

set SCRIPT=%1
if "%SCRIPT%"=="" (set SCRIPT=builds)

composer run-script %SCRIPT%
