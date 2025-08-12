@echo off
echo Enabling ZIP extension in php.ini...
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace '^;extension=zip$', 'extension=zip' | Set-Content 'C:\xampp\php\php.ini'"
echo ZIP extension enabled!
echo Please restart Apache in XAMPP Control Panel
pause
