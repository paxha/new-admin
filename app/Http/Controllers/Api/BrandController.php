<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Brand as ResourcesBrand;
use App\Http\Resources\BrandCollection;
use App\Models\Brand;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully loaded',
                'model' => 'Brand',
                'name' => 'Brand Listing',
                'url' => '/',
                'action' => 'listing',
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'brands' => new BrandCollection(Brand::orderByDesc('id')->get())
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'iso2' => ['nullable', 'string', 'max:2'],
            'logo' => ['nullable', 'string', 'max:191'],
            'cover' => ['nullable', 'string', 'max:191'],
            'meta_title' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $trashed = Brand::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            DB::beginTransaction();
            try {
                $trashed->restore();
                $trashed->update($request->all());

                Log::create([
                    'title' => 'successfully restored',
                    'model' => 'Brand',
                    'name' => $trashed->name,
                    'url' => '/brand/' . $trashed->id . '/show',
                    'action' => 'create'
                ]);

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                return response([
                    'message' => $exception->getMessage(),
                ], 500);
            }

            return response([
                'message' => $trashed->name . ' successfully restored.',
            ], 200);
        }

        DB::beginTransaction();
        try {
            $brand = Brand::create($request->all());

            Log::create([
                'title' => 'successfully created',
                'model' => 'Brand',
                'name' => $brand->name,
                'url' => '/',
                'action' => 'create'
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $brand->name . ' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully requested',
                'model' => 'Brand',
                'name' => $brand->name,
                'url' => '/',
                'action' => 'request'
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 200);
        }

        return response([
            'brand' => new ResourcesBrand($brand),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'iso2' => ['nullable', 'string', 'max:2'],
            'logo' => ['nullable', 'string', 'max:191'],
            'cover' => ['nullable', 'string', 'max:191'],
            'meta_title' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $brand->update($request->all());

            Log::create([
                'title' => 'successfully updated',
                'model' => 'Brand',
                'name' => $brand->name,
                'url' => '/',
                'action' => 'update'
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $brand->name . ' successfully updated.',
        ], 200);
    }

    /**
     * Toggle popular the specified resource in storage.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function togglePopular(Brand $brand)
    {
        DB::beginTransaction();
        try {
            $brand->togglePopular();

            Log::create([
                'title' => 'successfully ' . ($brand->popular ? 'marked popular' : 'marked unpopular'),
                'model' => 'Brand',
                'name' => $brand->name,
                'url' => '/',
                'action' => ($brand->popular ? 'popular' : 'unpopular')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $brand->name . ' successfully ' . ($brand->popular ? 'marked popular' : 'marked unpopular') . '.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleActive(Brand $brand)
    {
        DB::beginTransaction();
        try {
            $brand->toggleActive();

            Log::create([
                'title' => 'successfully ' . ($brand->active ? 'activated' : 'inactivated'),
                'model' => 'Brand',
                'name' => $brand->name,
                'url' => '/',
                'action' => ($brand->active ? 'active' : 'inactive')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $brand->name . ' successfully ' . ($brand->active ? 'activated' : 'inactivated') . '.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $name = $brand->name;

        DB::beginTransaction();
        try {
            $brand->delete();

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Brand',
                'name' => $name,
                'url' => '/',
                'action' => 'delete'
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $name . ' successfully deleted.',
        ], 204);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroyMany(Request $request)
    {
        $request->validate([
            'brands.*' => ['required', 'exists:brands,id'],
        ]);

        DB::beginTransaction();
        try {
            Brand::destroy($request->brands);

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Brand',
                'name' => 'Bulk Delete',
                'url' => '/',
                'action' => 'delete'
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => 'brands successfully deleted.',
        ], 204);
    }
}
