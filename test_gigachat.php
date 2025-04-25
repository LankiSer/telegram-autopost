<?php

// Bootstrap Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set test mode manually regardless of .env
putenv('GIGACHAT_TEST_MODE=true');

// Import necessary classes
use App\Services\GigaChatService;
use App\Models\GigaChatCredential;
use Illuminate\Support\Facades\Log;

echo "Starting GigaChat Test Script\n";
echo "Test Mode: " . (getenv('GIGACHAT_TEST_MODE') ? 'Enabled' : 'Disabled') . "\n\n";

// Create a dummy credential for testing
$credential = new GigaChatCredential();
$credential->client_id = 'test_client_id';
$credential->client_secret = 'test_client_secret';
$credential->auth_url = 'https://example.com/auth';
$credential->api_url = 'https://example.com/api';
$credential->user_id = 1;

// Output credential information
echo "Credential Information:\n";
echo "- Client ID: " . $credential->client_id . "\n";
echo "- Auth URL: " . $credential->auth_url . "\n";
echo "- API URL: " . $credential->api_url . "\n";
echo "- User ID: " . $credential->user_id . "\n\n";

// Create service instance
$service = new GigaChatService($credential);

// Verify hasCredentials result
echo "hasCredentials check: " . ($service->hasCredentials() ? 'PASSED' : 'FAILED') . "\n\n";

// Test the mock post generation
$prompt = "Сгенерируй интересный пост о технологиях для Telegram канала";
echo "Requesting mock post with prompt: $prompt\n\n";

// Generate text
$text = $service->generateText($prompt);

// Output result
echo "RESULT from generateText:\n";
echo "-------\n";
echo $text . "\n";
echo "-------\n\n";

// Test generatePost method
echo "Testing generatePost method\n";
$post = $service->generatePost($prompt);

echo "RESULT from generatePost:\n";
echo "-------\n";
echo $post . "\n";
echo "-------\n\n";

echo "GigaChat test completed.\n"; 