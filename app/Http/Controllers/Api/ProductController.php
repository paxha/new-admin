<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ResourcesProduct;
use App\Http\Resources\ProductCollection;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
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
                'model' => 'Product',
                'name' => 'Product Listing',
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
            'products' => new ProductCollection(Product::orderByDesc('id')->get())
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

        $trashed = Product::onlyTrashed()->whereName($request->name)->first();

        if ($trashed) {
            DB::beginTransaction();
            try {
                $trashed->restore();
                $trashed->update($request->all());

                Log::create([
                    'title' => 'successfully restored',
                    'model' => 'Product',
                    'name' => $trashed->name,
                    'url' => '/product/' . $trashed->id . '/show',
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
            $product = Product::create($request->all());

            Log::create([
                'title' => 'successfully created',
                'model' => 'Product',
                'name' => $product->name,
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
            'message' => $product->name . ' successfully created.',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'title' => 'successfully requested',
                'model' => 'Product',
                'name' => $product->name,
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
            'product' => new ResourcesProduct($product),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            //
        ]);

        DB::beginTransaction();
        try {
            $product->update($request->all());

            Log::create([
                'title' => 'successfully updated',
                'model' => 'Product',
                'name' => $product->name,
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
            'message' => $product->name . ' successfully updated.',
        ], 200);
    }

    /**
     * Toggle active the specified resource in storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toggleActive(Product $product)
    {
        DB::beginTransaction();
        try {
            $product->toggleActive();

            Log::create([
                'title' => 'successfully ' . ($product->active ? 'activated' : 'inactivated'),
                'model' => 'Product',
                'name' => $product->name,
                'url' => '/',
                'action' => ($product->active ? 'active' : 'inactive')
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response([
            'message' => $product->name . ' successfully ' . ($product->active ? 'activated' : 'inactivated') . '.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $name = $product->name;

        DB::beginTransaction();
        try {
            $product->delete();

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Product',
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
            'products.*' => ['required', 'exists:products,id'],
        ]);

        DB::beginTransaction();
        try {
            Product::destroy($request->products);

            Log::create([
                'title' => 'successfully deleted',
                'model' => 'Product',
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
            'message' => 'products successfully deleted.',
        ], 204);
    }
}
