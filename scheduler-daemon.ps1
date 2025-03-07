$workingDirectory = "C:\project\telegram-autopost"
Set-Location $workingDirectory

while ($true) {
    Write-Host "Running scheduler at $(Get-Date)"
    php artisan schedule:run
    Start-Sleep -Seconds 60
} 