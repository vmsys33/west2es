@echo off
REM West2ES Auto Documentation Update Batch File
REM This file provides easy access to the auto-update functionality

echo.
echo ========================================
echo   West2ES Auto Documentation Update
echo ========================================
echo.

if "%1"=="docs" (
    echo Updating documentation only...
    powershell -ExecutionPolicy Bypass -File "scripts\auto-doc-update.ps1" -UpdateDocs
) else if "%1"=="push" (
    echo Auto-pushing to GitHub...
    powershell -ExecutionPolicy Bypass -File "scripts\auto-doc-update.ps1" -AutoPush
) else if "%1"=="full" (
    echo Full update (docs + push)...
    powershell -ExecutionPolicy Bypass -File "scripts\auto-doc-update.ps1" -FullUpdate
) else (
    echo Usage:
    echo   auto-update.bat docs    - Update documentation only
    echo   auto-update.bat push    - Auto-push to GitHub
    echo   auto-update.bat full    - Full update (docs + push)
    echo.
    echo Examples:
    echo   auto-update.bat docs
    echo   auto-update.bat push
    echo   auto-update.bat full
)

echo.
pause
