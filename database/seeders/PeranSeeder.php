<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Peran;

class PeranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $credits = require database_path('data/cast_credits.php');

        foreach (Film::all() as $film) {
            $posterName = basename($film->getRawOriginal('poster'));

            foreach ($credits[$posterName] ?? [] as $credit) {
                $cast = Cast::where('name', $credit['name'])->firstOrFail();

                Peran::updateOrCreate(
                    [
                        'film_id' => $film->id,
                        'cast_id' => $cast->id,
                    ],
                    ['actor' => $credit['actor']]
                );
            }
        }
    }
}
