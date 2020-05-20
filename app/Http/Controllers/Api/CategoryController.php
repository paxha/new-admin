<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category as ResourcesCategory;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::create([
            'title' => 'successfully loaded',
            'model' => 'Category',
            'name' => 'Category Listing',
            'url' => '/category/',
            'action' => 'listing'
        ]);
        return response([
            'categories' => new CategoryCollection(Category::root()->orderByDesc('id')->get())
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $trashed = Category::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            $trashed->restore();
            $trashed->update($request->all());

            return response([
                'message' => $trashed->name.' successfully restored.',
            ], 200);
        }

        $category = Category::create($request->all());

        Log::create([
            'title' => 'successfully created',
            'model' => 'Category',
            'name' => $category->name,
            'url' => '/category/'.$category->id .'/show',
            'action' => 'create'
        ]);

        return response([
            'message' => $category->name.' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        Log::create([
            'title' => 'successfully requested',
            'model' => 'Category',
            'name' => $category->name,
            'url' => '/category/'.$category->id .'/show',
            'action' => 'request'
        ]);
        
        return response([
            'category' => new ResourcesCategory($category),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $category->update($request->all());

        Log::create([
            'title' => 'successfully updated',
            'model' => 'Category',
            'name' => $category->name,
            'url' => '/category/'.$category->id .'/show',
            'action' => 'update'
        ]);

        return response([
            'message' => $category->name.' successfully updated.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param Module $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive(Category $category)
    {
        $category->toggleActive();

        Log::create([
            'title' => 'successfully ' . ($category->active ? 'activated' : 'inactivated'),
            'model' => 'Category',
            'name' => $category->name,
            'url' => '/category/'.$category->id .'/show',
            'action' => ($category->active ? 'active' : 'inactive')
        ]);

        return response([
            'message' => $category->name.' successfully '.($category->active ? 'activated' : 'inactivated').'.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $name = $category->name;

        $category->delete();

        Log::create([
            'title' => 'successfully deleted',
            'model' => 'Category',
            'name' => $name,
            'url' => '/category',
            'action' => 'delete'
        ]);

        return response([
            'message' => $name.' successfully deleted.',
        ], 204);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMany(Request $request)
    {
        $request->validate([
            'categories.*' => ['required', 'exists:categories,id'],
        ]);

        Category::destroy($request->categories);

        Log::create([
            'title' => 'successfully deleted',
            'model' => 'Category',
            'name' => 'Bulk Delete',
            'url' => '/category',
            'action' => 'delete'
        ]);

        return response([
            'message' => 'Modules successfully deleted.',
        ], 204);
    }
}
