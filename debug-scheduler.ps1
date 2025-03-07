$workingDirectory = "C:\project\telegram-autopost"
Set-Location $workingDirectory

# Очистка всего
Write-Host "Cleaning up..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan schedule:clear-cache
php artisan optimize:clear

# Создание директории для логов
if (-not(Test-Path "storage/logs")) {
    New-Item -Path "storage/logs" -ItemType Directory -Force
}

# Проверка прав доступа
Write-Host "`nChecking permissions..."
$logPath = "storage/logs"
$acl = Get-Acl $logPath
Write-Host "Current permissions for $logPath :"
$acl.Access | Format-Table IdentityReference,FileSystemRights,AccessControlType

# Проверка задач
Write-Host "`nScheduled tasks:"
php artisan schedule:list

# Проверка текущего времени сервера
Write-Host "`nServer time:"
php artisan tinker --execute="echo now();"

# Запуск планировщика с отладкой
Write-Host "`nStarting scheduler with debug info..."
while ($true) {
    $currentTime = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "`n----------------------------------------"
    Write-Host "Current time: $currentTime"
    
    # Запуск планировщика
    Write-Host "`nRunning scheduler..."
    $result = php artisan schedule:run --verbose
    Write-Host "Scheduler output: $result"
    
    # Проверка файлов
    Write-Host "`nChecking log files..."
    
    if (Test-Path "storage/logs/test.log") {
        $lastLog = Get-Content "storage/logs/test.log" -Tail 1
        Write-Host "Last test log: $lastLog"
    } else {
        Write-Host "Test log file not found"
    }
    
    if (Test-Path "storage/logs/last_run.txt") {
        $lastRun = Get-Content "storage/logs/last_run.txt"
        Write-Host "Last run timestamp: $lastRun"
    } else {
        Write-Host "Last run file not found"
    }
    
    Write-Host "`nWaiting 60 seconds..."
    Start-Sleep -Seconds 60
} 