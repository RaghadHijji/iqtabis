<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * The 8 categories of the site, in the order they show.
     */
    public function run(): void
    {
        foreach (['Religious', 'Motivation', 'Love', 'Sad', 'Humor', 'Wisdom', 'Poetry', 'Facts'] as $i => $name) {
            Category::updateOrCreate(['name' => $name], ['position' => $i + 1]);
        }
    }
}
