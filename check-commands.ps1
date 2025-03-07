$workingDirectory = "C:\project\telegram-autopost"
Set-Location $workingDirectory

Write-Host "`nClearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan schedule:clear-cache

Write-Host "`nAvailable commands:"
php artisan list

Write-Host "`nTesting scheduler:test command:"
php artisan scheduler:test

Write-Host "`nTesting auto-posting:run command:"
php artisan auto-posting:run

Write-Host "`nScheduled tasks:"
php artisan schedule:list 