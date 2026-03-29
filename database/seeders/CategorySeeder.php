<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1,  'name' => 'YouTube',           'code' => 'youtube',   'icon' => 'Youtube',     'url' => 'https://www.youtube.com/',      'image' => null, 'description' => 'Plataforma de videos para creadores de contenido y canales personales.'],
            ['id' => 2,  'name' => 'Facebook',           'code' => 'facebook',  'icon' => 'Facebook',    'url' => 'https://www.facebook.com/',     'image' => null, 'description' => 'Red social para compartir publicaciones, enlaces y comunidades.'],
            ['id' => 3,  'name' => 'Instagram',          'code' => 'instagram', 'icon' => 'Instagram',   'url' => 'https://www.instagram.com/',    'image' => null, 'description' => 'Red social visual enfocada en fotos, reels e historias.'],
            ['id' => 4,  'name' => 'Twitter / X',        'code' => 'twitter',   'icon' => 'Twitter',     'url' => 'https://x.com/',               'image' => null, 'description' => 'Plataforma de microblogging para opiniones, noticias y enlaces.'],
            ['id' => 5,  'name' => 'LinkedIn',           'code' => 'linkedin',  'icon' => 'Linkedin',    'url' => 'https://www.linkedin.com/feed/', 'image' => null, 'description' => 'Red profesional para networking y contenido laboral.'],
            ['id' => 6,  'name' => 'GitHub',             'code' => 'github',    'icon' => 'Github',      'url' => 'https://github.com/',           'image' => null, 'description' => 'Plataforma para alojar y compartir proyectos de código.'],
            ['id' => 7,  'name' => 'TikTok',             'code' => 'tiktok',    'icon' => 'Tiktok',      'url' => 'https://www.tiktok.com/',       'image' => null, 'description' => 'Red social de videos cortos y contenido viral.'],
            ['id' => 8,  'name' => 'Twitch',             'code' => 'twitch',    'icon' => 'Twitch',      'url' => 'https://www.twitch.tv/',        'image' => null, 'description' => 'Plataforma de transmisiones en vivo y streaming.'],
            ['id' => 9,  'name' => 'Sitio Web',          'code' => 'website',   'icon' => 'Globe',       'url' => '',                             'image' => null, 'description' => 'Página web personal, portafolio o sitio corporativo.'],
            ['id' => 10, 'name' => 'Correo Electrónico', 'code' => null,        'icon' => 'Mail',        'url' => '',                             'image' => null, 'description' => 'Contacto directo mediante correo electrónico.'],
            ['id' => 11, 'name' => 'Spotify',            'code' => 'spotify',   'icon' => 'AudioLines',  'url' => '',                             'image' => null, 'description' => 'Plataforma de música y podcasts.'],
            ['id' => 12, 'name' => 'Otro Enlace',        'code' => null,        'icon' => 'Link',        'url' => '',                             'image' => null, 'description' => 'Enlace personalizado a cualquier recurso externo.'],
        ];

        DB::table('categories')->upsert(
            $categories,
            ['id'],
            ['name', 'code', 'icon', 'url', 'image', 'description']
        );
    }
}
