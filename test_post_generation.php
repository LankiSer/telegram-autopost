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
use App\Http\Controllers\PostGenerationController;
use App\Services\GigaChatService;
use App\Models\GigaChatCredential;
use App\Models\User;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

echo "Testing Post Generation Controller with Test Mode\n";
echo "Test Mode: " . (getenv('GIGACHAT_TEST_MODE') ? 'Enabled' : 'Disabled') . "\n\n";

try {
    // Create a request with parameters
    $requestData = [
        'channel_id' => 1,  // This would be a valid channel ID in a real scenario
        'title' => 'Test Post About Technology',
        'custom_prompt' => 'Make it futuristic and engaging'
    ];
    
    // Create a mock channel for testing
    $channel = new Channel();
    $channel->id = 1;
    $channel->name = 'Tech Channel';
    $channel->content_prompt = 'latest technology trends';
    $channel->user_id = 1;
    
    // Mock the Channel::findOrFail method
    Channel::shouldReceive('findOrFail')
        ->once()
        ->with(1)
        ->andReturn($channel);
    
    // Set a fake authenticated user
    Auth::shouldReceive('id')
        ->andReturn(1);
    
    // Create credential and service
    $credential = new GigaChatCredential();
    $credential->client_id = 'test_client_id';
    $credential->client_secret = 'test_client_secret';
    $credential->auth_url = 'https://example.com/auth';
    $credential->api_url = 'https://example.com/api';
    $credential->user_id = 1;
    
    $gigaChatService = new GigaChatService($credential);
    
    // Create the controller with our service
    $controller = new PostGenerationController($gigaChatService);
    
    // Create a request
    $request = Request::create('/api/generate-post', 'POST', $requestData);
    
    echo "Sending request to PostGenerationController with parameters:\n";
    echo "- Channel ID: " . $requestData['channel_id'] . "\n";
    echo "- Title: " . $requestData['title'] . "\n";
    echo "- Custom Prompt: " . $requestData['custom_prompt'] . "\n\n";
    
    // Call the controller method
    $response = $controller->generate($request);
    
    // Get the response content
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    echo "Response from controller:\n";
    echo "- Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    
    if (isset($data['content'])) {
        echo "- Generated Content:\n";
        echo "-------\n";
        echo $data['content'] . "\n";
        echo "-------\n";
    } else {
        echo "- Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n"; 