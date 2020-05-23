<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category as ResourcesCategory;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
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
                'model' => 'Category',
                'name' => 'Category Listing',
                'url' => '/category/',
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
            'categories' => new CategoryCollection(Category::root()->orderByDesc('id')->get())
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
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $trashed = Category::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            DB::beginTransaction();
            try {
                $trashed->restore();
                $trashed->update($request->all());

                Log::create([
                    'title' => 'successfully restored',
                    'model' => 'Category',
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
            $category = Category::create($request->all());

            Log::create([
                'title' => 'successfully created',
                'model' => 'Category',
                'name' => $category->name,
                'url' => '/category/' . $category->id . '/show',
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
            'message' => $category->name . ' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully requested',
                'model' => 'Category',
                'name' => $category->name,
                'url' => '/category/' . $category->id . '/show',
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
            'category' => new ResourcesCategory($category),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ]);

        if (!$request->parent_id) {
            $request['parent_id'] = null;
        }

        DB::beginTransaction();
        try {
            $category->update($request->all());

            Log::create([
                'title' => 'successfully updated',
                'model' => 'Category',
                'name' => $category->name,
                'url' => '/category/' . $category->id . '/show',
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
            'message' => $category->name . ' successfully updated.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleActive(Category $category)
    {
        DB::beginTransaction();
        try {
            $category->toggleActive();

            Log::create([
                'title' => 'successfully ' . ($category->active ? 'activated' : 'inactivated'),
                'model' => 'Category',
                'name' => $category->name,
                'url' => '/category/' . $category->id . '/show',
                'action' => ($category->active ? 'active' : 'inactive')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $category->name . ' successfully ' . ($category->active ? 'activated' : 'inactivated') . '.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $name = $category->name;

        DB::beginTransaction();
        try {
            $category->delete();

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Category',
                'name' => $name,
                'url' => '/category',
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
            'categories.*' => ['required', 'exists:categories,id'],
        ]);

        DB::beginTransaction();
        try {
            Category::destroy($request->categories);

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Category',
                'name' => 'Bulk Delete',
                'url' => '/category',
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
            'message' => 'Categories successfully deleted.',
        ], 204);
    }
}
