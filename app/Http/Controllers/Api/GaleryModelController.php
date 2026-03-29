<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GaleryModel;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class GaleryModelController extends Controller
{
    public function procesar($table_origin = "", $id = "", $files = [])
    {
        if(count($files) == 0) return null;
        
        try {
            foreach($files as $file){
                $filename = $this->saveStorageImage($file);
                $this->saveToDatabase($table_origin, $id, $filename);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function saveStorageImage ($file){
        $randomStr = str()->random(5);
        $filename = time() . $randomStr . '.webp';

        $image = Image::read($file);

        $width = $image->width();
        $height = $image->height();
        
        // Thumbnail 150x150
        $thumbnail = Image::read($file)
            ->cover(150, 150)
            ->toWebp(quality: 80);
        Storage::disk('public')->put('galery/thumb_' . $filename, $thumbnail);

        // Mediano 400x400
        $medium = Image::read($file)
            ->cover(400, 400)
            ->toWebp(quality: 85);
        Storage::disk('public')->put('galery/medium_' . $filename, $medium);

        // Original redimensionado a un maximo de 1200 pixeles
        if($width > 1200){
            $large = Image::read($file)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->toWebp(quality: 90);
        }

        Storage::disk('public')->put('galery/'. $filename, $width > 1200 ? $large : $image->toWebp(quality: 90));

        return $filename;
    }

    public function deleteStorageImage($filename){
        $galeryImageOriginal = 'storage/galery/' . $filename;
        $galeryImageMedium = 'storage/galery/medium_' . $filename;
        $galeryImageThumb = 'storage/galery/thumb_' . $filename;

        $imagePath = public_path($galeryImageOriginal);        
        if (file_exists($imagePath)) {            
            unlink($imagePath);
        }

        $imagePath = public_path($galeryImageMedium);        
        if (file_exists($imagePath)) {            
            unlink($imagePath);
        }

        $imagePath = public_path($galeryImageThumb);        
        if (file_exists($imagePath)) {            
            unlink($imagePath);
        }
    }

    public function saveToDatabase($table_origin, $id, $filename){
        $galery = new GaleryModel();
        $galery->name = $filename;
        $galery->origin = $table_origin;
        $galery->origin_id = $id;        
        $galery->image_path = $filename;
        $galery->save();
    }

    //method public to delete image by id, it will be used in the controller that uses this class
    public function deleteImageById($id){
                
        if(empty($id)) return null;

        $galery = GaleryModel::find($id);
        
        if($galery){
            $this->deleteStorageImage($galery->image_path);
            $galery->delete();
        }

        return response()->json(['message' => 'Image deleted'], 200);
    }

    public function deleteImageBydTableOrigin($table_origin, $id_origin){
        

        if(empty($table_origin) || empty($id_origin)) return null;

        $galeries = GaleryModel::where('origin', $table_origin)->where('origin_id', $id_origin)->get();

        foreach($galeries as $galery){
            $this->deleteStorageImage($galery->image_path);
            $galery->delete();
        }
    }
}
