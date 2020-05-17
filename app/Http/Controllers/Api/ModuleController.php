<?php

namespace App\Http\Controllers\Api;

use Lararole\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Lararole\Http\Resources\ModuleCollection;

class ModuleController extends Controller
{
    public function index()
    {
        return response([
            'modules' => new ModuleCollection(Module::root()->whereActive(true)->orderByDesc('id')->get()),
        ], 200);
    }
}
