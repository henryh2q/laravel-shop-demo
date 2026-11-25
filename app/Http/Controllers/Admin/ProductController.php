<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->query('q'), fn ($q, $term) => $q->where('sku', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['is_active' => true]), 'categories' => Category::orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('sort_order')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:40', Rule::unique('products')->ignore($product)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'name.en' => ['required', 'string', 'max:160'],
            'name.ja' => ['nullable', 'string', 'max:160'],
            'name.vi' => ['nullable', 'string', 'max:160'],
            'description.*' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0.5', 'max:100000'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        return [
            ...$data,
            'slug' => $product?->slug ?? Str::slug($data['name']['en']).'-'.Str::lower(Str::random(4)),
            'price' => (int) round($data['price'] * 100),
            'name' => array_filter($data['name']),
            'description' => array_filter($data['description'] ?? []),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
