<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Image;

class PhotoController extends Controller
{
    public function restaurant_permit($slug, Request $request)
    {
        $file_path = "permit/";
        if ($request->has('size')) {
            $size = $request->get('size');
            
            if ($size == "medium") {
                $file_path = "permit/medium/";
            } else if ($size == "thumbnail") {
                $file_path = "permit/thumbnail/";
            }
        }

        $image = Storage::disk('local')->get($file_path.$slug);
        return response()->make($image, 200, ['Content-Type' => 'Image']);
    }

    public function restaurant_menu($slug, Request $request)
    {
        $file_path = "menu/";
        if ($request->has('size')) {
            $size = $request->get('size');
            
            if ($size == "medium") {
                $file_path = "menu/medium/";
            } else if ($size == "thumbnail") {
                $file_path = "menu/thumbnail/";
            }
        }

        $image = Storage::disk('local')->get($file_path.$slug);
        return response()->make($image, 200, ['Content-Type' => 'Image']);
    }

    public function restaurant_image($slug, Request $request)
    {
        $file_path = "restaurant/";
        if ($request->has('size')) {
            $size = $request->get('size');
            
            if ($size == "medium") {
                $file_path = "restaurant/medium/";
            } else if ($size == "thumbnail") {
                $file_path = "restaurant/thumbnail/";
            }
        }

        $image = Storage::disk('local')->get($file_path.$slug);
        return response()->make($image, 200, ['Content-Type' => 'Image']);
    }

    public function restaurant_report($slug, Request $request)
    {
        $file_path = "report/";
        if ($request->has('size')) {
            $size = $request->get('size');
            
            if ($size == "medium") {
                $file_path = "report/medium/";
            } else if ($size == "thumbnail") {
                $file_path = "report/thumbnail/";
            }
        }

        $image = Storage::disk('local')->get($file_path.$slug);
        return response()->make($image, 200, ['Content-Type' => 'Image']);
    }
}
