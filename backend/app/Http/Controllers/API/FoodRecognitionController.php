<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessFoodImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class FoodRecognitionController extends Controller
{
    public function recognize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // APIキーの確認
            $apiKey = config('services.openai.api_key');
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI API key not configured. Please set OPENAI_API_KEY in .env file.',
                    'demo_response' => [
                        'ingredients' => ['トマト', 'レタス', 'きゅうり']
                    ]
                ], 200);
            }

            $image = $request->file('image');
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
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
                
                return response()->json([
                    'success' => true,
                    'data' => $ingredients
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to process image',
                'status' => $response->status(),
                'error' => $response->json()
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recognizeAsync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $image = $request->file('image');
        $imagePath = $image->store('food-images', 'public');
        
        ProcessFoodImage::dispatch($imagePath);
        
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully. Processing in background.',
            'image_path' => $imagePath
        ], 202);
    }
}
