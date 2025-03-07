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

# Проверка задач
Write-Host "`nScheduled tasks:"
php artisan schedule:list

# Запуск планировщика
Write-Host "`nStarting scheduler..."
while ($true) {
    $currentTime = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "`nRunning at $currentTime"
    
    # Запуск планировщика
    $output = php artisan schedule:run
    Write-Host $output
    
    # Проверка логов Laravel
    $lastLog = Get-Content storage/logs/laravel.log -Tail 1
    Write-Host "Last Laravel log: $lastLog"
    
    # Проверка тестовых логов
    if (Test-Path "storage/logs/scheduler-test.log") {
        $lastTest = Get-Content storage/logs/scheduler-test.log -Tail 1
        Write-Host "Last test log: $lastTest"
    }
    
    Write-Host "Waiting for next minute..."
    Start-Sleep -Seconds 60
} 