<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Variation as ResourcesVariation;
use App\Http\Resources\VariationCollection;
use App\Models\Log;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariationController extends Controller
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
                'model' => 'Variation',
                'name' => 'Variation Listing',
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
            'variations' => new VariationCollection(Variation::orderByDesc('id')->get())
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
            //
        ]);

        $trashed = Variation::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            DB::beginTransaction();
            try {
                $trashed->restore();
                $trashed->update($request->all());

                Log::create([
                    'title' => 'successfully restored',
                    'model' => 'Variation',
                    'name' => $trashed->name,
                    'url' => '/variation/' . $trashed->id . '/show',
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
            $variation = Variation::create($request->all());

            Log::create([
                'title' => 'successfully created',
                'model' => 'Variation',
                'name' => $variation->name,
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
            'message' => $variation->name . ' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Variation $variation
     * @return \Illuminate\Http\Response
     */
    public function edit(Variation $variation)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully requested',
                'model' => 'Variation',
                'name' => $variation->name,
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
            'variation' => new ResourcesVariation($variation),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Variation $variation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Variation $variation)
    {
        $request->validate([
            //
        ]);

        DB::beginTransaction();
        try {
            $variation->update($request->all());

            Log::create([
                'title' => 'successfully updated',
                'model' => 'Variation',
                'name' => $variation->name,
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
            'message' => $variation->name . ' successfully updated.',
        ], 200);
    }

    /**
     * Toggle continue the specified resource in storage.
     *
     * @param \App\Models\Variation $variation
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleContinue(Variation $variation)
    {
        DB::beginTransaction();
        try {
            $variation->toggleContinue();

            Log::create([
                'title' => 'successfully ' . ($variation->continue ? 'continued' : 'discontinued'),
                'model' => 'Variation',
                'name' => $variation->name,
                'url' => '/',
                'action' => ($variation->continue ? 'continue' : 'discontinue')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $variation->name . ' successfully ' . ($variation->continue ? 'continued' : 'discontinued') . '.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param \App\Models\Variation $variation
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleActive(Variation $variation)
    {
        DB::beginTransaction();
        try {
            $variation->toggleActive();

            Log::create([
                'title' => 'successfully ' . ($variation->active ? 'activated' : 'inactivated'),
                'model' => 'Variation',
                'name' => $variation->name,
                'url' => '/',
                'action' => ($variation->active ? 'active' : 'inactive')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $variation->name . ' successfully ' . ($variation->active ? 'activated' : 'inactivated') . '.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Variation $variation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Variation $variation)
    {
        $name = $variation->name;

        DB::beginTransaction();
        try {
            $variation->delete();

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Variation',
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
            'variations.*' => ['required', 'exists:variations,id'],
        ]);

        DB::beginTransaction();
        try {
            Variation::destroy($request->variations);

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Variation',
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
            'message' => 'variations successfully deleted.',
        ], 204);
    }
}
