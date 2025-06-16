<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFoodImage implements ShouldQueue
{
    use Queueable;

    protected $imagePath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $imageContent = Storage::disk('public')->get($this->imagePath);
            $base64Image = base64_encode($imageContent);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => '画像に含まれる食材を日本語で識別してください。食材名のリストをJSON形式で返してください。例: {"ingredients": ["トマト", "レタス", "きゅうり"]}'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => config('services.openai.max_tokens', 300)
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'];
                $ingredients = json_decode($content, true);
                
                Log::info('Food recognition completed', [
                    'image_path' => $this->imagePath,
                    'ingredients' => $ingredients
                ]);
                
                // ここで結果をデータベースに保存したり、
                // ユーザーに通知したりすることができます
            } else {
                Log::error('Failed to process food image', [
                    'image_path' => $this->imagePath,
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing food image', [
                'image_path' => $this->imagePath,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
