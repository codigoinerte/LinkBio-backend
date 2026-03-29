<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LandingController extends Controller
{
    public function index(Request $request){

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userExists = DB::table('users')
                        ->where('nickname', $request->slug)
                        ->where('is_active', true)
                        ->first();

        $themeUser = DB::table('users')
                        ->select(
                            
                            'users.name', 'users.photo','users.headline', 'users.nickname', 'users.bio', 'users.website', 'users.is_verified',
                            'user_designs.theme_id', 'user_designs.wallpaper_type', 'user_designs.wallpaper_file', 'user_designs.wallpaper_color_type', 'user_designs.wallpaper_color_custom', 'user_designs.wallpaper_pattern_type',

                        )
                        ->join('user_designs', 'users.id', '=', 'user_designs.user_id')
                        ->where('users.nickname', $request->slug)
                        ->where('users.is_active', true)
                        ->first();

        if(empty($userExists->name)){
            return response()->json([
                "ok" => false,
                "message" => "Usuario no encontrado"
            ], 404);
        }

        $themeUserArray = json_decode(json_encode($themeUser), true);

        $linksUser = DB::table('links')
                        ->select('links.id', 'links.title', 'links.description', 'links.url', 
                                 'categories.name', 'categories.code', 'categories.icon', 'categories.url as category_url', 'categories.image', 'categories.description as category_description')
                        ->join('categories', 'links.category_id', '=', 'categories.id')
                        ->join('users', 'links.user_id', '=', 'users.id')
                        ->where('users.nickname', $request->slug)
                        ->where('links.is_enabled', true)
                        ->orderBy('links.order', 'asc')
                        ->get();

        $projectsUser = DB::table('projects')
                        ->select( 'projects.id', 'projects.name', 'projects.description', 'projects.short_description', 'projects.link', 'projects.location', 'projects.from', 'projects.to')
                        ->join('users', 'projects.user_id', '=', 'users.id')
                        ->where('users.nickname', $request->slug)
                        ->where('projects.is_enabled', true)
                        ->orderBy('projects.order', 'asc')
                        ->get();

        $projectsUser->each(function(object $project) {
                        $project->galery = DB::table('galery_models')
                            ->select('id', 'origin_id', 'name', 'image_path')
                            ->where('origin_id', $project->id)
                            ->where('origin', 'projects')
                            ->get();
                        });

        if(empty($themeUser->name)){
            return response()->json([
                "ok" => true,
                "data" => [
                    "name"=> $userExists->name,
                    "photo"=> null,
                    "headline"=> "Title of the profile",
                    "nickname"=> $userExists->nickname,
                    "bio"=> $userExists->bio,
                    "website"=> $userExists->website,
                    "is_verified"=> $userExists->is_verified,
                    "theme_id"=> "air",
                    "wallpaper_type"=> "image",
                    "wallpaper_file"=> "",
                    "wallpaper_color_type"=> "",
                    "wallpaper_color_custom"=> "",
                    "wallpaper_pattern_type"=> "",
                    "links"=> [],
                    "projects"=> []
                ]
            ], 201);
        }
       

        return response()->json([
            "ok" => true,
            "data" => [
                ...$themeUserArray,
                'links' => $linksUser,
                'projects' => $projectsUser
            ],
        ], 200);

    }
}
