<?php

// Bootstrap Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set test mode manually regardless of .env
putenv('GIGACHAT_TEST_MODE=true');
putenv('APP_DEBUG=true');

// Import necessary classes
use App\Services\GigaChatService;
use App\Models\GigaChatCredential;
use Illuminate\Support\Facades\Log;

echo "Testing GigaChat Direct with Test Mode\n";
echo "Test Mode: " . (getenv('GIGACHAT_TEST_MODE') ? 'Enabled' : 'Disabled') . "\n\n";

try {
    // Create credential and service
    $credential = new GigaChatCredential();
    $credential->client_id = 'test_client_id';
    $credential->client_secret = 'test_client_secret';
    $credential->auth_url = 'https://example.com/auth';
    $credential->api_url = 'https://example.com/api';
    $credential->user_id = 1;
    
    $gigaChatService = new GigaChatService($credential);
    
    // Verify hasCredentials result
    echo "hasCredentials check: " . ($gigaChatService->hasCredentials() ? 'PASSED' : 'FAILED') . "\n\n";
    
    // Create a post generation prompt
    $basePrompt = "Создай информативный и увлекательный пост для Telegram канала о последних технологических трендах. ";
    $titlePrompt = "Тема поста: Искусственный интеллект в повседневной жизни. ";
    $customPrompt = "Дополнительные указания: сделай текст футуристичным и вовлекающим. ";
    
    $formattingInstructions = "
    Пожалуйста, соблюдай следующие правила форматирования:
    1. Текст должен быть компактным, не более 5-7 абзацев
    2. Используй markdown для форматирования: *курсив*, **жирный шрифт**, __подчеркнутый__
    3. Добавь эмодзи для увеличения читаемости и выразительности
    4. Разбей текст на логические части с подзаголовками
    5. Добавь 2-3 хэштега в конце поста, связанные с тематикой технологий
    6. Общий объем текста - не более 1500 символов
    ";
    
    $fullPrompt = $basePrompt . $titlePrompt . $customPrompt . $formattingInstructions;
    
    echo "Testing generateText method with prompt:\n";
    echo "-------\n";
    echo $fullPrompt . "\n";
    echo "-------\n\n";
    
    // Generate text
    $text = $gigaChatService->generateText($fullPrompt);
    
    // Output result
    echo "RESULT from generateText:\n";
    echo "-------\n";
    echo $text . "\n";
    echo "-------\n\n";
    
    // Test generatePost method
    echo "Testing generatePost method with the same prompt\n";
    $post = $gigaChatService->generatePost($fullPrompt);
    
    echo "RESULT from generatePost:\n";
    echo "-------\n";
    echo $post . "\n";
    echo "-------\n\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "Test completed.\n"; 