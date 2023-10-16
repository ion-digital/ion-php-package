@echo off

set TARGET=%1
if "%TARGET%"=="" (set TARGET=build)

 .\vendor\bin\phpunit.bat .\tests\unit
