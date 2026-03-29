<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $themes = [
            [
                'id' => 'air',
                'name' => 'Air',
                'preview' => 'bg-gray-100',
                'accent' => 'bg-white',
                'isPremium' => false,
                'textColor' => '#111827',
                'badgeColor' => 'bg-black/40',
                'description' => null,
            ],
            [
                'id' => 'blocks',
                'name' => 'Blocks',
                'preview' => 'bg-gradient-to-b from-purple-500 to-purple-600',
                'accent' => 'bg-gradient-to-r from-pink-400 to-pink-500',
                'isPremium' => false,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'bloom',
                'name' => 'Bloom',
                'preview' => 'bg-gradient-to-br from-red-900 via-purple-900 to-pink-800',
                'accent' => 'bg-gradient-to-r from-blue-600 to-purple-600',
                'isPremium' => true,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'breeze',
                'name' => 'Breeze',
                'preview' => 'bg-gradient-to-br from-pink-300 to-pink-400',
                'accent' => 'bg-gradient-to-r from-pink-200 to-pink-300',
                'isPremium' => true,
                'textColor' => '#111827',
                'badgeColor' => 'bg-black/40',
                'description' => null,
            ],
            [
                'id' => 'lake',
                'name' => 'Lake',
                'preview' => 'bg-gradient-to-b from-slate-900 to-black',
                'accent' => 'bg-slate-800',
                'isPremium' => false,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'mineral',
                'name' => 'Mineral',
                'preview' => 'bg-gradient-to-b from-stone-200 to-stone-300',
                'accent' => 'bg-gradient-to-r from-stone-100 to-stone-200',
                'isPremium' => false,
                'textColor' => '#111827',
                'badgeColor' => 'bg-black/40',
                'description' => null,
            ],
            [
                'id' => 'astrid',
                'name' => 'Astrid',
                'preview' => 'bg-gradient-to-br from-emerald-900 to-slate-900',
                'accent' => 'bg-gradient-to-r from-emerald-400 to-teal-400',
                'isPremium' => true,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'groove',
                'name' => 'Groove',
                'preview' => 'bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500',
                'accent' => 'bg-gradient-to-r from-red-400 to-orange-400',
                'isPremium' => true,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'agate',
                'name' => 'Agate',
                'preview' => 'bg-gradient-to-br from-emerald-600 to-teal-700',
                'accent' => 'bg-gradient-to-r from-lime-400 to-green-400',
                'isPremium' => true,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'twilight',
                'name' => 'Twilight',
                'preview' => 'bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600',
                'accent' => 'bg-gradient-to-r from-pink-300 to-purple-300',
                'isPremium' => true,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'rise',
                'name' => 'Rise',
                'preview' => 'bg-gradient-to-br from-orange-500 to-red-500',
                'accent' => 'bg-gradient-to-r from-yellow-400 to-orange-400',
                'isPremium' => false,
                'textColor' => '#ffffff',
                'badgeColor' => 'bg-black/20',
                'description' => null,
            ],
            [
                'id' => 'grid',
                'name' => 'Grid',
                'preview' => 'bg-gradient-to-br from-lime-400 to-green-500',
                'accent' => 'bg-white',
                'isPremium' => true,
                'textColor' => '#111827',
                'badgeColor' => 'bg-black/40',
                'description' => null,
            ],
        ];

        $themes = array_map(function (array $theme) use ($now) {
            return array_merge($theme, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $themes);

        DB::table('themes')->upsert(
            $themes,
            ['id'],
            ['name', 'preview', 'accent', 'isPremium', 'textColor', 'badgeColor', 'description', 'updated_at']
        );
    }
}
