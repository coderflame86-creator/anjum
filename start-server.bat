@echo off
cd /d "%~dp0"
title Anjum Iqbal Dynamic Web Server
cls
echo =========================================================================
echo               ANJUM IQBAL DYNAMIC WEBSITE & ADMIN PANEL
echo =========================================================================
echo.
echo   Working Directory: %~dp0
echo   Local Web Server:  http://localhost:8000/
echo.
echo   - Public Website:  http://localhost:8000/
echo   - Admin Panel:     http://localhost:8000/admin/
echo.
echo   Press Ctrl+C in this terminal window to stop the server anytime.
echo =========================================================================
echo.

IF EXIST "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" -S localhost:8000 -t "%~dp0"
) ELSE (
    php -S localhost:8000 -t "%~dp0"
)

pause

