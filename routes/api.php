// Маршруты для проверки статуса бота
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/check-bot-status/{channel}', [App\Http\Controllers\ApiController::class, 'checkBotStatus']);
    Route::post('/update-bot-status/{channel}', [App\Http\Controllers\ApiController::class, 'updateBotStatus']);
}); 