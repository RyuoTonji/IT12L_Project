@echo off
echo ========================================
echo Starting ULAS BBQ Complete System
echo ========================================
echo.

echo [0/5] Clearing existing PHP processes...
taskkill /F /IM php.exe >nul 2>&1
timeout /t 1 /nobreak >nul

echo [1/5] Starting ERP Web Server (port 8000)...
start "ERP Web Server" cmd /k "cd IT12L_FullProject_ERP && php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 2 /nobreak >nul

echo [2/5] Starting ERP Queue Worker...
start "ERP Sync Worker" cmd /k "cd IT12L_FullProject_ERP && php artisan queue:work --queue=erp_queue"
timeout /t 2 /nobreak >nul

echo [3/5] Starting POS Web Server (port 8001)...
start "POS Web Server" cmd /k "cd POS_BBQ && php artisan serve --host=127.0.0.1 --port=8001"
timeout /t 2 /nobreak >nul

echo [4/5] Starting POS Queue Worker...
start "POS Sync Worker" cmd /k "cd POS_BBQ && php artisan queue:work --queue=pos_queue"
timeout /t 2 /nobreak >nul

echo [5/5] Starting Cloudflare Tunnel...
start "Cloudflare Tunnel (Stable)" cmd /k "cd POS_BBQ && cloudflared tunnel --config config.yml run"

echo.
echo ========================================
echo All services started successfully! 
echo ========================================
echo.
echo LOCAL ACCESS:
echo - ERP System:  http://localhost:8000
echo - POS System:  http://localhost:8001
echo.
echo REMOTE ACCESS:
echo - ERP System:  https://crm.ulasbbqlagao.dpdns.org
echo - POS System:  https://ulasbbqlagao.dpdns.org
echo.
echo IMPORTANT: 
echo - You will see 5 new command windows pop up
echo - DO NOT CLOSE THEM or the system will stop working
echo - You can minimize them to get them out of the way
echo.
echo Press any key to exit this window (services will keep running)...
pause >nul
