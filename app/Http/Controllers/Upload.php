<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class Upload extends Controller
{
    function profile(Request $request){

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $this->deletePhotoProfile();

        $file = $request->file('file');
        $filename = time() . '.webp';

        $image = Image::read($file);

        $width = $image->width();
        $height = $image->height();
        
        // Thumbnail 150x150
        $thumbnail = Image::read($file)
            ->cover(150, 150)
            ->toWebp(quality: 80);
        Storage::disk('public')->put('profile/thumb_' . $filename, $thumbnail);

        // Mediano 400x400
        $medium = Image::read($file)
            ->cover(400, 400)
            ->toWebp(quality: 85);
        Storage::disk('public')->put('profile/medium_' . $filename, $medium);

        // Original redimensionado a un maximo de 1200 pixeles
        if($width > 1200){
            $large = Image::read($file)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->toWebp(quality: 90);
        }

        $pathUrl = Storage::disk('public')->put('profile/'. $filename, $width > 1200 ? $large : $image->toWebp(quality: 90));

        $userId = auth()->id();
        $user = User::find($userId);
        $user->photo = $filename;
        $user->save();

        return response()->json(['image' => $filename], 201);
    }

    function deleteProfile(Request $request){

        $this->deletePhotoProfile();
        return response()->json(['message' => 'Profile picture deleted'], 200);
    }

    function deletePhotoProfile () {

        $userId = auth()->id();
        $user = User::find($userId);

        if($user->photo){
            $photoPath = 'storage/profile/' . $user->photo;
            $photoPath_medium = 'storage/profile/medium_' . $user->photo;
            $photoPath_thumb = 'storage/profile/thumb_' . $user->photo;

            $this->deleteFromPublicStore($photoPath);
            $this->deleteFromPublicStore($photoPath_medium);
            $this->deleteFromPublicStore($photoPath_thumb);
        }

        $user->photo = null;
        $user->save();
    }

    function deleteFromPublicStore ($path){
        $imagePath = public_path($path);        
        if (file_exists($imagePath)) {            
            unlink($imagePath);
        }
    }
}
