<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cast;

class CastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $credits = require database_path('data/cast_credits.php');
        $castNames = collect($credits)
            ->flatten(1)
            ->pluck('name')
            ->unique();

        foreach ($castNames as $castName) {
            Cast::updateOrCreate(
                ['name' => $castName],
                [
                    'age' => 0,
                    'bio' => 'Data cast film',
                ]
            );
        }
    }
}
