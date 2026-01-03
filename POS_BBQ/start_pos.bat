@echo off
echo Starting POS System Components...
echo --------------------------------

echo 1. Starting Web Server (php artisan serve) ...
start "POS Web Server" php artisan serve

echo 2. Starting Sync Worker (php artisan queue:work) ...
start "POS Sync Worker" php artisan queue:work

echo 3. Starting Internet Access (Cloudflare Tunnel) ...
start "POS Remote Access" cloudflared tunnel --config config.yml run

echo --------------------------------
echo All services started! 
echo.
echo IMPORTANT: 
echo - You will see 3 new black windows pop up.
echo - DO NOT CLOSE THEM. If you close them, the system will stop working.
echo - You can minimize them to get them out of your way.
echo.
pause
