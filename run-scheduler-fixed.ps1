$workingDirectory = "C:\project\telegram-autopost"
Set-Location $workingDirectory

# Очистка кэша
Write-Host "Clearing cache..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan schedule:clear-cache

# Проверка текущего времени
Write-Host "`nServer time:"
php artisan tinker --execute="echo now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');"

# Запуск планировщика
Write-Host "`nStarting scheduler..."
while ($true) {
    $time = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "`n[$time] Running scheduler..."
    
    # Запуск с отладкой
    php artisan schedule:run --verbose
    
    # Проверка лога
    if (Test-Path "storage/logs/scheduler-test.log") {
        $lastLog = Get-Content "storage/logs/scheduler-test.log" -Tail 1
        Write-Host "Last log: $lastLog"
    }
    
    Write-Host "Waiting for next minute..."
    Start-Sleep -Seconds 60
} 