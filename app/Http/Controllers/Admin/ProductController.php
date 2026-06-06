<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $categoryId = $request->input('category_id');
        $supplierId = $request->input('supplier_id');

        $query = Product::with('supplier')->orderBy('id', 'asc');

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($supplierId)) {
            $query->where('supplier_id', $supplierId);
        }

        $products = $query->paginate(10);
        $categories = Category::all();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.product.index', compact('products', 'categories', 'suppliers'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.product.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'image' => ['nullable', 'image'],
        ]);

        $product = new Product();
        $product->name = $validated['name'];
        $product->price = $validated['price'];
        $product->category_id = $validated['category_id'];
        $product->supplier_id = $validated['supplier_id'];
        $product->requires_installation = $request->has('requires_installation');
        $product->description = $validated['description'] ?? null;
        $product->specifications = $validated['specifications'] ?? null;

        if ($request->hasFile('image')) {
            $product->image = $this->storeImage($request);
        }

        $product->save();

        return redirect('/quantri/san-pham')->with('success', 'Đã thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        session(['url_back' => url()->previous()]);

        $product = Product::find($id);

        if (!$product) {
            return redirect('/quantri/san-pham')->with('error', 'Không tìm thấy sản phẩm!');
        }

        $categories = Category::all();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.product.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'image' => ['nullable', 'image'],
        ]);

        $product = Product::find($id);

        if (!$product) {
            return redirect('/quantri/san-pham')->with('error', 'Không tìm thấy sản phẩm!');
        }

        $product->name = $validated['name'];
        $product->price = $validated['price'];
        $product->category_id = $validated['category_id'];
        $product->supplier_id = $validated['supplier_id'];
        $product->requires_installation = $request->has('requires_installation');
        $product->description = $validated['description'] ?? null;
        $product->specifications = $validated['specifications'] ?? null;

        if ($request->hasFile('image')) {
            if (!empty($product->image)) {
                $oldImagePath = public_path('uploads/products/' . $product->image);

                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $product->image = $this->storeImage($request);
        }

        $product->save();

        return redirect(session('url_back', '/quantri/san-pham'))->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect('/quantri/san-pham')->with('error', 'Không tìm thấy sản phẩm!');
        }

        if ($product->image) {
            $imagePath = public_path('uploads/products/' . $product->image);

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $product->delete();

        return redirect('/quantri/san-pham')->with('success', 'Đã xóa sản phẩm!');
    }

    private function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($originalName);
        $filename = time() . '_' . $safeName . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/products/');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);

        return $filename;
    }
}
