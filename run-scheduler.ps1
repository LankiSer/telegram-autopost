$workingDirectory = "C:\project\telegram-autopost"
Set-Location $workingDirectory

# Очистка кэша
Write-Host "Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan schedule:clear-cache
php artisan optimize:clear

# Создание тестового лог-файла
$logFile = "storage/logs/scheduler-test.log"
if (-not(Test-Path $logFile)) {
    New-Item -Path $logFile -ItemType File -Force
}

# Запуск планировщика
Write-Host "`nStarting scheduler..."
while ($true) {
    $currentTime = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "Running scheduler at $currentTime"
    
    # Запуск планировщика
    php artisan schedule:run
    
    # Проверка логов
    if (Test-Path $logFile) {
        $lastLine = Get-Content $logFile -Tail 1
        if ($lastLine) {
            Write-Host "Last log entry: $lastLine"
        }
    }
    
    # Ожидание 60 секунд
    Start-Sleep -Seconds 60
} 