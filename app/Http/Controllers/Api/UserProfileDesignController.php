<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfileDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class UserProfileDesignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        $wallpaper = $this->getWallpaperThemeUser();

        if (!$wallpaper) {
            return response()->json([
                'ok' => true,
                'profileDesign' => [
                    "themeId"=> "air",
                    "wallpaperId"=> "image",
                    "file"=> "null",
                    "patternId"=> "",
                    "colorId"=> [
                        "id"=> "",
                        "custom"=> ""
                    ]
                ]
            ], 200);
        }

        return response()->json([
            'ok' => true,
            'profileDesign' => $wallpaper
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->upsertProfileDesign($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        return $this->upsertProfileDesign($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }    

    private function getWallpaperThemeUser()
    {
        $user = auth()->user();
        $userId = $user->id;

        $profileDesign = UserProfileDesign::where('user_id', $userId)->first();

        if (!$profileDesign) {
            return null;
        }

        return [            
            "themeId" => $profileDesign->theme_id,
            "wallpaperId" => $profileDesign->wallpaper_type,
            "file" => $profileDesign->wallpaper_file,
            "patternId" => $profileDesign->wallpaper_pattern_type,
            "colorId" => [
                "id" => $profileDesign->wallpaper_color_type,
                "custom" => $profileDesign->wallpaper_color_custom
            ]
            
        ];
    }

    public function upsertProfileDesign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'themeId' => 'required|string|max:150',
            'wallpaperId' => 'required|string|max:150',
            'patternId' => 'nullable|string|max:150',
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'colorId.custom' => 'nullable|string|max:150',
            'colorId.id' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();
        $userId = $user->id;

        $themeId = $request->input('themeId');
        $wallpaperId = $request->input('wallpaperId');
        $patternId = $request->input('patternId') ?? '';
        $file = $request->file('file');
        $colorIdCustom = $request->input('colorId.custom') ?? '';
        $colorIdId = $request->input('colorId.id') ?? '';

        $profileDesign = UserProfileDesign::where('user_id', $userId)->first();
        $isNewRecord = !$profileDesign;

        if ($isNewRecord) {
            $profileDesign = new UserProfileDesign();
            $profileDesign->user_id = $userId;
        }

        if (!empty($file) && !empty($profileDesign->wallpaper_file)) {
            $this->deleteWallpaper($profileDesign->wallpaper_file);
        }

        $profileDesign->theme_id = $themeId;
        $profileDesign->wallpaper_type = $wallpaperId;
        $profileDesign->wallpaper_pattern_type = $patternId;

        if (!empty($file)) {
            $filename = $this->uploadFile($file);
            $profileDesign->wallpaper_file = $filename;
        } elseif ($isNewRecord) {
            $profileDesign->wallpaper_file = null;
        }

        $profileDesign->wallpaper_color_custom = $colorIdCustom;
        $profileDesign->wallpaper_color_type = $colorIdId;
        $profileDesign->save();

        return response()->json([
            'ok' => true,
            'profileDesign' => $this->getWallpaperThemeUser()
        ], $isNewRecord ? 201 : 200);
    }

    private function uploadFile($file)
    {
        $filename = time() . '.webp';

        $wallpaper = Image::read($file)->toWebp(quality: 85);
            
        Storage::disk('public')->put('wallpaper/original_' . $filename, $wallpaper);

        return "original_$filename";
    }

    public function deleteWallpaper()
    {
        $user = auth()->user();
        $userId = $user->id;

        $wallpaper = UserProfileDesign::where('user_id', $userId)->first()->wallpaper_file;

        if ($wallpaper) {
            $wallpaperPath = 'storage/wallpaper/original_' . $wallpaper;
            $this->deleteFromPublicStore($wallpaperPath);
            return response()->json(['message' => 'Wallpaper deleted'], 200);
        }

        return response()->json(['message' => 'No wallpaper found'], 404);
    }

    private function deleteFromPublicStore ($path)
    {
        $imagePath = public_path($path);        
        if (file_exists($imagePath)) {            
            unlink($imagePath);
        }
    }
}
