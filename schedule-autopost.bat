@echo off
:loop
php artisan auto-posting:run
timeout /t 60 /nobreak
goto loop 