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

# Проверка команд
Write-Host "`nTesting commands directly:"
Write-Host "Testing scheduler:test"
php artisan scheduler:test

Write-Host "`nTesting auto-posting:run"
php artisan auto-posting:run

# Проверка планировщика
Write-Host "`nTesting scheduler:"
Write-Host "Current tasks:"
php artisan schedule:list

Write-Host "`nRunning scheduler:"
php artisan schedule:run

Write-Host "`nStarting scheduler daemon:"
php artisan schedule:work 