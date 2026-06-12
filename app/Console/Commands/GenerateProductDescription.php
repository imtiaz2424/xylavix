<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GenerateProductDescription extends Command
{
    protected $signature = 'product:generate-description {id}';
    protected $description = 'Generate product description using Groq AI';

    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();
        $this->productRepository = $productRepository;
    }

    public function handle()
    {
        $id = $this->argument('id');
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->error("Product not found!");
            return;
        }

        // Get product name from flat table
        $productFlat = DB::table('product_flat')
            ->where('product_id', $id)
            ->where('locale', 'en')
            ->first();

        $name = $productFlat->name ?? 'Tech Product';

        $this->info("Generating description for: $name");

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

            // Update description in product_flat table
            DB::table('product_flat')
                ->where('product_id', $id)
                ->where('locale', 'en')
                ->update(['description' => $description]);

            $this->info("✅ Description generated successfully!");
            $this->line($description);
        } else {
            $this->error("❌ API Error: " . $response->body());
        }
    }
}