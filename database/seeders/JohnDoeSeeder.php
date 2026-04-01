<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProfileDesign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class JohnDoeSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyAssets();

        $user = User::create([
            'name'        => 'John Doe',
            'nickname'    => 'johndoe',
            'email'       => 'john.doe@example.com',
            'password'    => '123456',
            'bio'         => 'Full stack developer, open source enthusiast and coffee addict.',
            'headline'    => 'Software Developer & Designer',
            'website'     => 'https://johndoe.dev',
            'photo'       => '1770439311.webp',
            'is_active'   => true,
            'is_verified' => true,
        ]);

        // Projects
        Project::create([
            'user_id'           => $user->id,
            'name'              => 'Personal Portfolio',
            'short_description' => 'My personal portfolio website built with Next.js and Tailwind CSS.',
            'description'       => 'A fully responsive portfolio showcasing my skills, experience and projects. Built with Next.js 14, Tailwind CSS and deployed on Vercel.',
            'link'              => 'https://tester.org.pe/',
            'location'          => 'Remote',
            'from'              => '2023-01-01 00:00:00',
            'to'                => null,
            'is_enabled'        => true,
            'order'             => 1,
            'click'             => 42,
            'visits'            => 128,
        ]);

        Project::create([
            'user_id'           => $user->id,
            'name'              => 'Task Manager App',
            'short_description' => 'A cross-platform task manager built with Flutter and Firebase.',
            'description'       => 'A productivity app with real-time sync, push notifications and offline support. Available on iOS and Android.',
            'link'              => 'https://github.com/codigoinerte',
            'location'          => 'Remote',
            'from'              => '2022-06-01 00:00:00',
            'to'                => '2023-06-01 00:00:00',
            'is_enabled'        => true,
            'order'             => 2,
            'click'             => 18,
            'visits'            => 74,
        ]);

        // Links
        Link::create([
            'user_id'     => $user->id,
            'category_id' => 6, // GitHub
            'title'       => 'My GitHub',
            'url'         => 'https://github.com/codigoinerte',
            'description' => 'Check out my open source projects and contributions.',
            'is_enabled'  => true,
            'order'       => 1,
            'clicks'      => 35,
            'visits'      => 90,
        ]);

        Link::create([
            'user_id'     => $user->id,
            'category_id' => 5, // LinkedIn
            'title'       => 'LinkedIn Profile',
            'url'         => 'https://www.linkedin.com/in/fredy-martinez-bustamante',
            'description' => 'Connect with me professionally.',
            'is_enabled'  => true,
            'order'       => 2,
            'clicks'      => 22,
            'visits'      => 60,
        ]);

        UserProfileDesign::create([
            'user_id'               => $user->id,
            'theme_id'              => 'lake',
            'wallpaper_type'        => 'image',
            'wallpaper_file'        => 'original_1774761315.webp',
            'wallpaper_color_type'  => '',
            'wallpaper_color_custom' => '',
            'wallpaper_pattern_type' => '',
        ]);
    }

    private function copyAssets(): void
    {
        $assets = database_path('seeders/assets');

        $directories = ['profile', 'wallpaper', 'galery'];
        foreach ($directories as $dir) {
            Storage::disk('public')->makeDirectory($dir);
        }

        foreach (['profile', 'wallpaper'] as $dir) {
            $source = $assets . '/' . $dir;
            if (!is_dir($source)) continue;

            foreach (glob($source . '/*') as $file) {
                $filename = basename($file);
                $destination = $dir . '/' . $filename;
                if (!Storage::disk('public')->exists($destination)) {
                    Storage::disk('public')->put($destination, file_get_contents($file));
                }
            }
        }
    }
}
