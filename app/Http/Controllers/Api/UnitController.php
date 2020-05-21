<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Unit as ResourcesUnit;
use App\Http\Resources\UnitCollection;
use App\Models\Log;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
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
                'model' => 'Unit',
                'name' => 'Unit Listing',
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
            'units' => new UnitCollection(Unit::orderByDesc('id')->get())
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

        $trashed = Unit::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            DB::beginTransaction();
            try {
                $trashed->restore();
                $trashed->update($request->all());

                Log::create([
                    'title' => 'successfully restored',
                    'model' => 'Unit',
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
            $unit = Unit::create($request->all());

            Log::create([
                'title' => 'successfully created',
                'model' => 'Unit',
                'name' => $unit->name,
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
            'message' => $unit->name . ' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully requested',
                'model' => 'Unit',
                'name' => $unit->name,
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
            'unit' => new ResourcesUnit($unit),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            //
        ]);

        DB::beginTransaction();
        try {
            $unit->update($request->all());

            Log::create([
                'title' => 'successfully updated',
                'model' => 'Unit',
                'name' => $unit->name,
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
            'message' => $unit->name . ' successfully updated.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleActive(Unit $unit)
    {
        DB::beginTransaction();
        try {
            $unit->toggleActive();

            Log::create([
                'title' => 'successfully ' . ($unit->active ? 'activated' : 'inactivated'),
                'model' => 'Unit',
                'name' => $unit->name,
                'url' => '/',
                'action' => ($unit->active ? 'active' : 'inactive')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $unit->name . ' successfully ' . ($unit->active ? 'activated' : 'inactivated') . '.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        $name = $unit->name;

        DB::beginTransaction();
        try {
            $unit->delete();

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Unit',
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
            'units.*' => ['required', 'exists:units,id'],
        ]);

        DB::beginTransaction();
        try {
            Unit::destroy($request->units);

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Unit',
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
            'message' => 'units successfully deleted.',
        ], 204);
    }
}
