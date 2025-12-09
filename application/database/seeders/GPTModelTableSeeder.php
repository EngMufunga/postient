<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GPTModelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $models = [
            ["label" => "model", "name" => "gpt-4o"],
            ["label" => "model", "name" => "gpt-4o-mini"],
            ["label" => "Older GPT-4 turbo model – large context", "name" => "gpt-4-turbo"],
            ["label" => "model", "name" => "gpt-3.5-turbo"],
            ["label" => "model", "name" => "gpt-3.5-turbo-16k"],
            ["label" => "model", "name" => "gpt-3.5-turbo-instruct"],
            ["label" => "model", "name" => "gpt-3.5-turbo-instruct-0914"],
            ["label" => "model", "name" => "gpt-3.5-turbo-1106"],
            ["label" => "model", "name" => "gpt-3.5-turbo-0125"],
            ["label" => "model", "name" => "davinci-002"],
            ["label" => "model", "name" => "babbage-002"],
            ["label" => "model", "name" => "o1-preview"],
            ["label" => "model", "name" => "o1-preview-2024-09-12"],
            ["label" => "model", "name" => "o1-mini"],
            ["label" => "model", "name" => "o1-mini-2024-09-12"],
            ["label" => "model", "name" => "gpt-4.1"],
            ["label" => "model", "name" => "gpt-4.1-2025-04-14"],
            ["label" => "model", "name" => "gpt-4.1-mini"],
            ["label" => "model", "name" => "gpt-4.1-mini-2025-04-14"],
            ["label" => "model", "name" => "gpt-4.1-nano"],
            ["label" => "model", "name" => "gpt-4.1-nano-2025-04-14"],
            ["label" => "Reasoning Model", "name" => "o3"],
            ["label" => "Reasoning Model", "name" => "o3-mini"],
            ["label" => "Open-Weight Model", "name" => "gpt-oss-120b"],
            ["label" => "Open-Weight Model", "name" => "gpt-oss-20b"],
        ];

        foreach ($models as $model) {
            DB::table('gpt_models')->updateOrInsert(
                ['name' => $model['name']],
                ['label' => $model['label']]
            );
        }
    }
}
