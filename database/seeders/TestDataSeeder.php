<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user if not exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Sample channel categories and topics
        $categories = ['Технологии', 'Финансы', 'Развлечения', 'Образование', 'Спорт', 'Новости', 'Мода', 'Путешествия', 'Здоровье', 'Еда'];
        
        // Create test channels
        $techChannels = [];
        $financeChannels = [];
        $entertainmentChannels = [];
        $educationChannels = [];
        
        // Tech channels
        for ($i = 1; $i <= 5; $i++) {
            $channel = Channel::create([
                'user_id' => $user->id,
                'name' => "TechNews $i",
                'description' => "Канал о новостях технологий и IT-индустрии #$i",
                'type' => 'telegram',
                'telegram_username' => "technews$i",
                'bot_added' => rand(0, 1),
                'telegram_chat_id' => rand(1000000, 9999999),
                'category' => 'Технологии',
                'tags' => ['it', 'tech', 'development', 'programming', 'gadgets'],
                'content_prompt' => 'Новости технологий, IT, программирования и гаджетов'
            ]);
            
            $techChannels[] = $channel;
            
            // Create sample posts for each channel
            for ($j = 1; $j <= rand(5, 10); $j++) {
                Post::create([
                    'channel_id' => $channel->id,
                    'user_id' => $user->id,
                    'title' => "Tech Post #$j for Channel $i",
                    'content' => "Это тестовый пост #$j о технологиях для канала $i. Здесь может быть много интересной информации о новых технологиях, гаджетах и программном обеспечении. " . Str::random(100),
                    'status' => rand(0, 1) ? 'published' : 'draft',
                    'scheduled_at' => rand(0, 1) ? now()->addDays(rand(1, 10)) : null,
                    'published_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }
        
        // Finance channels
        for ($i = 1; $i <= 4; $i++) {
            $channel = Channel::create([
                'user_id' => $user->id,
                'name' => "Finance Daily $i",
                'description' => "Канал о финансах, инвестициях и экономике #$i",
                'type' => 'telegram',
                'telegram_username' => "finance$i",
                'bot_added' => rand(0, 1),
                'telegram_chat_id' => rand(1000000, 9999999),
                'category' => 'Финансы',
                'tags' => ['finance', 'investing', 'economy', 'trading', 'business'],
                'content_prompt' => 'Новости финансов, инвестиций, экономики и бизнеса'
            ]);
            
            $financeChannels[] = $channel;
            
            // Create sample posts for each channel
            for ($j = 1; $j <= rand(5, 10); $j++) {
                Post::create([
                    'channel_id' => $channel->id,
                    'user_id' => $user->id,
                    'title' => "Finance Post #$j for Channel $i",
                    'content' => "Это тестовый пост #$j о финансах для канала $i. Здесь может быть много полезной информации об инвестициях, экономике и бизнесе. " . Str::random(100),
                    'status' => rand(0, 1) ? 'published' : 'draft',
                    'scheduled_at' => rand(0, 1) ? now()->addDays(rand(1, 10)) : null,
                    'published_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }
        
        // Entertainment channels
        for ($i = 1; $i <= 3; $i++) {
            $channel = Channel::create([
                'user_id' => $user->id,
                'name' => "Entertainment Zone $i",
                'description' => "Канал о развлечениях, кино, сериалах и играх #$i",
                'type' => 'telegram',
                'telegram_username' => "entertain$i",
                'bot_added' => rand(0, 1),
                'telegram_chat_id' => rand(1000000, 9999999),
                'category' => 'Развлечения',
                'tags' => ['movies', 'games', 'entertainment', 'fun', 'series'],
                'content_prompt' => 'Новости кино, сериалов, игр и развлечений'
            ]);
            
            $entertainmentChannels[] = $channel;
            
            // Create sample posts for each channel
            for ($j = 1; $j <= rand(5, 10); $j++) {
                Post::create([
                    'channel_id' => $channel->id,
                    'user_id' => $user->id,
                    'title' => "Entertainment Post #$j for Channel $i",
                    'content' => "Это тестовый пост #$j о развлечениях для канала $i. Здесь может быть много интересной информации о кино, сериалах и играх. " . Str::random(100),
                    'status' => rand(0, 1) ? 'published' : 'draft',
                    'scheduled_at' => rand(0, 1) ? now()->addDays(rand(1, 10)) : null,
                    'published_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }
        
        // Education channels
        for ($i = 1; $i <= 4; $i++) {
            $channel = Channel::create([
                'user_id' => $user->id,
                'name' => "Learning Hub $i",
                'description' => "Канал об образовании, науке и саморазвитии #$i",
                'type' => 'telegram',
                'telegram_username' => "education$i",
                'bot_added' => rand(0, 1),
                'telegram_chat_id' => rand(1000000, 9999999),
                'category' => 'Образование',
                'tags' => ['education', 'science', 'learning', 'knowledge', 'development'],
                'content_prompt' => 'Новости образования, науки и саморазвития'
            ]);
            
            $educationChannels[] = $channel;
            
            // Create sample posts for each channel
            for ($j = 1; $j <= rand(5, 10); $j++) {
                Post::create([
                    'channel_id' => $channel->id,
                    'user_id' => $user->id,
                    'title' => "Education Post #$j for Channel $i",
                    'content' => "Это тестовый пост #$j об образовании для канала $i. Здесь может быть много полезной информации о науке, саморазвитии и обучении. " . Str::random(100),
                    'status' => rand(0, 1) ? 'published' : 'draft',
                    'scheduled_at' => rand(0, 1) ? now()->addDays(rand(1, 10)) : null,
                    'published_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }
        
        // Create channel groups
        $techGroup = ChannelGroup::create([
            'user_id' => $user->id,
            'name' => 'Technology Network',
            'description' => 'Группа технологических каналов для кросс-промо',
            'category' => 'Технологии',
        ]);
        
        $financeGroup = ChannelGroup::create([
            'user_id' => $user->id,
            'name' => 'Finance Network',
            'description' => 'Группа финансовых каналов для кросс-промо',
            'category' => 'Финансы',
        ]);
        
        $mixedGroup = ChannelGroup::create([
            'user_id' => $user->id,
            'name' => 'Mixed Topics',
            'description' => 'Группа каналов разной тематики для кросс-промо',
            'category' => 'Смешанные',
        ]);
        
        // Attach channels to groups
        foreach ($techChannels as $channel) {
            $techGroup->channels()->attach($channel->id);
        }
        
        foreach ($financeChannels as $channel) {
            $financeGroup->channels()->attach($channel->id);
        }
        
        // Add some random channels to the mixed group
        $mixedGroup->channels()->attach($techChannels[0]->id);
        $mixedGroup->channels()->attach($financeChannels[0]->id);
        $mixedGroup->channels()->attach($entertainmentChannels[0]->id);
        $mixedGroup->channels()->attach($educationChannels[0]->id);
    }
}
