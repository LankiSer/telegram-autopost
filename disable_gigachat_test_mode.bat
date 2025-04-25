@echo off
echo Отключение тестового режима GigaChat...

:: Проверить, существует ли строка в .env файле
findstr /c:"GIGACHAT_TEST_MODE" .env >nul
if %errorlevel% equ 0 (
    :: Обновить значение
    powershell -Command "(Get-Content .env) -replace 'GIGACHAT_TEST_MODE=.*', 'GIGACHAT_TEST_MODE=false' | Set-Content .env"
    echo Тестовый режим GigaChat отключен!
) else (
    echo Настройка GIGACHAT_TEST_MODE не найдена в .env файле.
)

echo Перезапустите приложение Laravel для применения изменений.
echo.
pause 