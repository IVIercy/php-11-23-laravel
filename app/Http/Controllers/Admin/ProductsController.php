<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('categories')->sortable()->paginate(5);
        return view('admin/products/index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin/products/create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request, ProductsRepositoryContract $repository)
    {
        return $repository->create($request)
            ? redirect()->route('admin.products.index')
            : redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $products = Product::where('id', '!=', $product->id)->get();
        return view('admin/products/edit', compact('product', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = Str::of($data['name'])->slug()->value();

        return $product->updateOrFail($data)
            ? redirect()->route('admin.products.edit', $product)
            : redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->middleware('permission:'.config('permission.permissions.products.delete'));

        if ($product->images()->exists()) {
            $product->images()->update(['id' => null]);
        }

        $product->deleteOrFail();

        return redirect()->route('admin.products.index');
    }
}
