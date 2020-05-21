<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Attribute as ResourcesAttribute;
use App\Http\Resources\AttributeCollection;
use App\Models\Attribute;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
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
                'model' => 'Attribute',
                'name' => 'Attribute Listing',
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
            'attributes' => new AttributeCollection(Attribute::orderByDesc('id')->get())
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
            'type' => ['nullable', 'in:text,number,image'],
            'categories.*' => ['required', 'exists:categories,id'],
            'units.*' => ['nullable', 'exists:units,id'],
        ]);

        $trashed = Attribute::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            DB::beginTransaction();
            try {
                $trashed->restore();
                $trashed->update($request->all());
                $trashed->categories()->sync($request->categories);
                $trashed->units()->sync($request->units);

                Log::create([
                    'title' => 'successfully restored',
                    'model' => 'Attribute',
                    'name' => $trashed->name,
                    'url' => '/category/' . $trashed->id . '/show',
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
            $attribute = Attribute::create($request->all());
            $attribute->categories()->sync($request->categories);
            $attribute->units()->sync($request->units);

            Log::create([
                'title' => 'successfully created',
                'model' => 'Attribute',
                'name' => $attribute->name,
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
            'message' => $attribute->name . ' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully requested',
                'model' => 'Attribute',
                'name' => $attribute->name,
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
            'attribute' => new ResourcesAttribute($attribute),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:text,number,image'],
            'categories.*' => ['nullable', 'exists:categories,id'],
            'units.*' => ['nullable', 'exists:units,id'],
        ]);

        DB::beginTransaction();
        try {
            $attribute->update($request->all());
            $attribute->categories()->sync($request->categories);
            $attribute->units()->sync($request->units);

            Log::create([
                'title' => 'successfully updated',
                'model' => 'Attribute',
                'name' => $attribute->name,
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
            'message' => $attribute->name . ' successfully updated.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param \App\Models\Attribute $attribute
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleActive(Attribute $attribute)
    {
        DB::beginTransaction();
        try {
            $attribute->toggleActive();

            Log::create([
                'title' => 'successfully ' . ($attribute->active ? 'activated' : 'inactivated'),
                'model' => 'Attribute',
                'name' => $attribute->name,
                'url' => '/',
                'action' => ($attribute->active ? 'active' : 'inactive')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $attribute->name . ' successfully ' . ($attribute->active ? 'activated' : 'inactivated') . '.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $name = $attribute->name;

        DB::beginTransaction();
        try {
            $attribute->delete();

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Attribute',
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
            'attributes.*' => ['required', 'exists:attributes,id'],
        ]);

        DB::beginTransaction();
        try {
            Attribute::destroy($request->get('attributes'));

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Attribute',
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
            'message' => 'attributes successfully deleted.',
        ], 204);
    }
}
