// Маршруты для проверки статуса бота
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/check-bot-status/{channel}', [App\Http\Controllers\ApiController::class, 'checkBotStatus']);
    Route::post('/update-bot-status/{channel}', [App\Http\Controllers\ApiController::class, 'updateBotStatus']);
});

// Маршруты для GigaChat API и конфигурации
Route::get('/config/gigachat-test-mode', [App\Http\Controllers\GigaChatConfigController::class, 'checkTestMode']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/config/gigachat-test-mode/enable', [App\Http\Controllers\GigaChatConfigController::class, 'enableTestMode']);
    Route::post('/config/gigachat-test-mode/disable', [App\Http\Controllers\GigaChatConfigController::class, 'disableTestMode']);
}); 