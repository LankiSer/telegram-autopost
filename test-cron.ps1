$workingDirectory = "C:\project\telegram-autopost"
Set-Location $workingDirectory

# Очистка кэша
Write-Host "Clearing cache..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan schedule:clear-cache

# Создание тестовой директории
$date = Get-Date -Format "yyyy-MM-dd"
$logPath = "storage/logs"
if (-not(Test-Path $logPath)) {
    New-Item -Path $logPath -ItemType Directory -Force
}

# Проверка времени
Write-Host "`nServer time:"
php artisan tinker --execute="echo now();"

# Запуск в цикле
Write-Host "`nStarting test loop..."
while ($true) {
    $time = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "`n[$time] Running scheduler..."
    
    # Запуск планировщика с отладкой
    $output = php artisan schedule:run --verbose
    Write-Host $output
    
    # Проверка файлов
    $logFile = "storage/logs/scheduler_$date.log"
    $lastRunFile = "storage/logs/last_run.txt"
    
    if (Test-Path $logFile) {
        $log = Get-Content $logFile -Tail 1
        Write-Host "Last log entry: $log"
    }
    
    if (Test-Path $lastRunFile) {
        $lastRun = Get-Content $lastRunFile
        Write-Host "Last run info: $lastRun"
    }
    
    Write-Host "Waiting for next minute..."
    Start-Sleep -Seconds 60
} 