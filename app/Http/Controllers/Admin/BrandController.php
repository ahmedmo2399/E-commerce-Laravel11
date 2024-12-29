<?php
namespace App\Http\Controllers\Admin;
use App\Http\Requests\StoreBrandRequest;
use App\Models\Brand;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.brand-add');
    }

    public function store(StoreBrandRequest $request)
{
    $brand = new Brand();
    $brand->name = $request->name;
    $brand->slug = Str::slug($request->name);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;

        $sizes = [[124, 124]];
        $paths = ['uploads/brands'];
        ImageHelper::generateThumbnails($image, $file_name, $sizes, $paths);

        $brand->image = $file_name;
    }

    $brand->save();
    return redirect()->route('admin.brand.index')->with('status', 'Brand has been added successfully');
}


    public function edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand.brand-edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request)
{
    // dd($request->all());s
    $brand = Brand::find($request->id);

    $brand->name = $request->name;
    $brand->slug = Str::slug($request->name);

    if ($request->hasFile('image')) {
        // Delete the existing image if it exists
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }

        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;

        $sizes = [[124, 124]];
        $paths = ['uploads/brands'];
        ImageHelper::generateThumbnails($image, $file_name, $sizes, $paths);

        $brand->image = $file_name;
    }

    $brand->save();
    return redirect()->route('admin.brand.index')->with('status', 'Brand has been updated successfully');
}


    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }

        $brand->delete();
        return redirect()->route('admin.brand.delete')->with('status', 'Brand has been deleted successfully');
    }
}
