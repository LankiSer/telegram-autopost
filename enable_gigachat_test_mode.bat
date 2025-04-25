@echo off
echo Включение тестового режима GigaChat...

:: Проверить, существует ли строка в .env файле
findstr /c:"GIGACHAT_TEST_MODE" .env >nul
if %errorlevel% equ 0 (
    :: Обновить значение
    powershell -Command "(Get-Content .env) -replace 'GIGACHAT_TEST_MODE=.*', 'GIGACHAT_TEST_MODE=true' | Set-Content .env"
) else (
    :: Добавить новую строку
    echo. >> .env
    echo # Настройка тестового режима для GigaChat >> .env
    echo GIGACHAT_TEST_MODE=true >> .env
)

echo Тестовый режим GigaChat успешно включен!
echo Перезапустите приложение Laravel для применения изменений.
echo.
echo Чтобы отключить тестовый режим, запустите disable_gigachat_test_mode.bat
echo.
pause 