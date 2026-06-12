<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GenerateAllProductSEO extends Command
{
    protected $signature = 'product:generate-seo';
    protected $description = 'Generate SEO meta data for all products using Groq AI';

    public function handle()
    {
        $products = DB::table('product_flat')
            ->where('locale', 'en')
            ->get();

        if ($products->isEmpty()) {
            $this->error("No products found!");
            return;
        }

        $this->info("Found {$products->count()} products. Generating SEO...");
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
                        'content' => 'You are an SEO expert for a tech gadget store called Xylavix. Return only valid JSON, no extra text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate SEO metadata for product: $name. Return ONLY this JSON format:
{
    \"meta_title\": \"product title | Xylavix\",
    \"meta_description\": \"compelling description under 160 chars\",
    \"meta_keywords\": \"keyword1, keyword2, keyword3\"
}"
                    ]
                ],
                'max_tokens' => 200,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'];

                // JSON parse করো
                preg_match('/\{.*\}/s', $content, $matches);
                if (!empty($matches[0])) {
                    $seo = json_decode($matches[0], true);

                    if ($seo) {
                        DB::table('product_flat')
                            ->where('product_id', $product->product_id)
                            ->where('locale', 'en')
                            ->update([
                                'meta_title' => $seo['meta_title'] ?? '',
                                'meta_description' => $seo['meta_description'] ?? '',
                                'meta_keywords' => $seo['meta_keywords'] ?? '',
                            ]);
                    }
                }
            }

            $bar->advance();
            sleep(1);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ All product SEO generated successfully!");
    }
}