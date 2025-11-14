<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Fantasia',
            'Ficção Científica',
            'Romance',
            'Suspense',
            'Terror',
            'Aventura',
            'Drama',
            'Poesia',
            'HQ / Mangá',
            'Biografia',
            'Autoajuda',
            'História',
            'Mistério',
            'Clássicos',
            'Infantil',
            'Young Adult (YA)',
        ];

        foreach ($genres as $name) {
            \DB::table('generos')->updateOrInsert(
                ['nome' => $name],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
