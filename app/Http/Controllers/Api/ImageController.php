<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        return response([
            'url' => asset($request->file('file')->store('images'))
        ]);
    }

    public function remove(Request $request)
    {
        Storage::delete($request->get('url'));
    }
}
