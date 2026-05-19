<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; 
use App\Models\Category;
use App\Models\Supplier; // ĐÃ SỬA: Thêm model Supplier
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str; 

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');

        $query = Product::orderBy('id', 'asc');

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        $products = $query->paginate(10);
        $categories = Category::all(); 

        return view('admin.product.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all(); // ĐÃ SỬA: Lấy danh sách nhà cung cấp
        return view('admin.product.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id; 
        $product->supplier_id = $request->supplier_id; // ĐÃ SỬA: Lưu ID nhà cung cấp
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = Str::slug($originalName);
            
            $filename = time() . '_' . $safeName . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/products/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);
            $product->image = $filename;
        }

        $product->save();
        return redirect('/quantri/san-pham')->with('success', 'Đã thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        session(['url_back' => url()->previous()]); 
        $product = Product::find($id);
        $categories = Category::all(); 
        $suppliers = Supplier::all(); // ĐÃ SỬA: Lấy danh sách nhà cung cấp truyền ra form sửa
        return view('admin.product.edit', compact('product', 'categories', 'suppliers')); 
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id; 
        $product->supplier_id = $request->supplier_id; // ĐÃ SỬA: Cập nhật ID nhà cung cấp
        $product->description = $request->description;
        $product->specifications = $request->specifications;

        if ($request->hasFile('image')) {
            if (!empty($product->image)) {
                $oldImagePath = public_path('uploads/products/' . $product->image);
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = Str::slug($originalName);
            $filename = time() . '_' . $safeName . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/products/');
            $file->move($destinationPath, $filename);
            $product->image = $filename;
        }

        $product->save();
        return redirect(session('url_back', '/quantri/san-pham'))->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product->image) {
            $imagePath = public_path('uploads/products/' . $product->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $product->delete();
        return redirect('/quantri/san-pham')->with('success', 'Đã xóa sản phẩm!');
    }
}