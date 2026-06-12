<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful customer support assistant for Xylavix, a premium tech gadget store in Bangladesh. Help customers with product queries, orders, shipping, and payments. Be friendly, professional, and concise. If asked about prices, mention that customers can browse the store at xylavix.com.'
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ],
            'max_tokens' => 300,
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $reply = $response->json()['choices'][0]['message']['content'];
            return response()->json(['reply' => $reply]);
        }

        return response()->json(['reply' => 'Sorry, I am unable to respond right now. Please try again later.']);
    }
}