@echo off
:loop
php artisan telegram:send-scheduled-posts
timeout /t 60 /nobreak
goto loop 