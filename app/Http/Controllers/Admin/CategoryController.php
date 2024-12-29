<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category.category_add');
    }

    public function category_store(StoreCategoryRequest $request)
{
    $category = new Category();
    $category->name = $request->name;
    $category->slug = Str::slug($request->name);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $sizes = [[124, 124]];
        $paths = ['uploads/categories'];
        ImageHelper::generateThumbnails($image, $file_name, $sizes, $paths);

        $category->image = $file_name;
    }

    $category->save();
    return redirect()->route('admin.category.index')->with('status', 'Category has been added successfully');
}
    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category.category-edit', compact('category'));
    }

    public function category_update(UpdateCategoryRequest $request)
    {
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }

            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;

            $sizes = [[124, 124]];
            $paths = ['uploads/categories'];
            ImageHelper::generateThumbnails($image, $file_name, $sizes, $paths);

            $category->image = $file_name;
        }

        $category->save();
        return redirect()->route('admin.category.index')->with('status', 'Category has been updated successfully');
    }


    public function category_delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('admin.category.index')->with('error', 'Category not found.');
        }
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.category.index')->with('status', 'Category has been deleted successfully.');
    }
}
