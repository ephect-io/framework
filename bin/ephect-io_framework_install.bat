@echo off
SETLOCAL ENABLEDELAYEDEXPANSION

REM Get the file path of the script
SET "FILE_PATH=%~dpnx0"
SET "CWD=%~dp0"
SET "CWD=%CWD:~0,-1%"
SET "PARENT_DIR=%CWD%\.."

REM Read the first line of the framework configuration
FOR /F "usebackq tokens=*" %%A IN ("%PARENT_DIR%\config\framework") DO (
    SET "FRAMEWORK_DIR=%%A"
    GOTO :continue
)
:continue

SET "MODULES_PATH=%FRAMEWORK_DIR%\Modules"

REM List all directories in the modules path
FOR /D %%B IN ("%MODULES_PATH%\*") DO (
    SET "MODULE=%%~nB"
    php use install:module "%%B" %1 %2
)
ENDLOCAL
