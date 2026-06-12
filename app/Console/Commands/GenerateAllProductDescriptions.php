<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GenerateAllProductDescriptions extends Command
{
    protected $signature = 'product:generate-all-descriptions';
    protected $description = 'Generate descriptions for all products using Groq AI';

    public function handle()
    {
        $products = DB::table('product_flat')
            ->where('locale', 'en')
            ->whereNull('description')
            ->orWhere('description', '')
            ->get();

        if ($products->isEmpty()) {
            $this->info("No products found without description!");
            return;
        }

        $this->info("Found {$products->count()} products. Generating descriptions...");
        $this->newLine();

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $name = $product->name ?? 'Tech Product';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional product copywriter for a tech gadget store called Xylavix.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Write a professional and engaging product description for: $name. Include key features, benefits, and a compelling call to action. Keep it under 150 words."
                    ]
                ],
                'max_tokens' => 300,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $description = $response->json()['choices'][0]['message']['content'];

                DB::table('product_flat')
                    ->where('product_id', $product->product_id)
                    ->where('locale', 'en')
                    ->update(['description' => $description]);
            }

            $bar->advance();
            sleep(1); // Rate limit এর জন্য
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ All product descriptions generated successfully!");
    }
}