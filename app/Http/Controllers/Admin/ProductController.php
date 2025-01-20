<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Coupon;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{


    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.product.index', compact('products'));
    }
    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product.product_add', compact('categories', 'brands'));
    }
    public function product_store(StoreProductRequest $request)
    {
        $product = new Product();

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $sizes = [
                [540, 689],
                [104, 104]
            ];
            $paths = [
                'uploads/products',
                'uploads/products/thumbnails'
            ];
            ImageHelper::generateThumbnails($image, $imageName, $sizes, $paths);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        $gallery_images = "";
        $counter = 1;
        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);

                if ($gcheck) {
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;


                    $sizes = [
                        [540, 689],
                        [104, 104]
                    ];
                    $paths = [
                        'uploads/products',
                        'uploads/products/thumbnails'
                    ];

                    ImageHelper::generateThumbnails($file, $gfileName, $sizes, $paths);

                    array_push($gallery_arr, $gfileName);
                    $counter++;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();

        return redirect()->route('admin.product.index')->with('status', 'Product has been added successfully!');
    }
    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(UpdateProductRequest $request)
    {
        $product = Product::findOrFail($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;


        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }

            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;

            $sizes = [[540, 689], [104, 104]];
            $paths = ['uploads/products', 'uploads/products/thumbnails'];
            ImageHelper::generateThumbnails($image, $file_name, $sizes, $paths);

            $product->image = $file_name;
        }


        if ($request->hasFile('images')) {

            if ($product->images) {
                foreach (explode(',', $product->images) as $oldImage) {
                    if (File::exists(public_path('uploads/products') . '/' . $oldImage)) {
                        File::delete(public_path('uploads/products') . '/' . $oldImage);
                    }
                }
            }

            $gallery_arr = [];
            $counter = 1;
            foreach ($request->file('images') as $file) {
                $file_extention = $file->extension();
                $file_name = Carbon::now()->timestamp . '-' . $counter . '.' . $file_extention;

                $sizes = [[540, 689], [104, 104]];
                $paths = ['uploads/products', 'uploads/products/thumbnails'];
                ImageHelper::generateThumbnails($file, $file_name, $sizes, $paths);

                $gallery_arr[] = $file_name;
                $counter++;
            }

            $product->images = implode(',', $gallery_arr);
        }

        $product->save();

        return redirect()->route('admin.product.index')->with('status', 'Product has been updated successfully!');
    }

    public function product_show($id)
{

    $product = Product::with(['category', 'brand'])->findOrFail($id);
    return view('admin.product.product_show', compact('product'));
}
public function product_delete($id)
{

    $product = Product::findOrFail($id);

    if ($product->image && File::exists(public_path('uploads/products/' . $product->image))) {
        File::delete(public_path('uploads/products/' . $product->image));
    }

    if ($product->images) {
        foreach (explode(',', $product->images) as $galleryImage) {
            if (File::exists(public_path('uploads/products/' . $galleryImage))) {
                File::delete(public_path('uploads/products/' . $galleryImage));
            }
        }
    }
    $product->delete();
    return redirect()->route('admin.product.index')->with('status', 'Product has been deleted successfully!');
}
public function coupons(){
    $coupons=Coupon::orderBy('expiry_date','DESC')->paginate(12);
    return view('admin.coupon.index',compact('coupons'));
}
public function coupon_add(){
    return view('admin.coupon.coupon_add');
}

public function coupon_store(Request $request){
    $request->validate([
        'code'=>'required',
        'type'=>'required',
        'value'=>'required|numeric',
        'cart_value'=>'required|numeric',
        'expiry_date'=>'required|date'
    ]);
    $coupon=new Coupon();
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();

    return redirect()->route('admin.coupons')->with('Coupon has been added successfully!');
}
public function coupon_edit ($id){
    $coupon=Coupon::find($id);
    return view('admin.coupon.coupon-edit',compact('coupon'));
}
public function coupon_update(Request $request){
    $request->validate([
        'code'=>'required',
        'type'=>'required',
        'value'=>'required|numeric',
        'cart_value'=>'required|numeric',
        'expiry_date'=>'required|date'
    ]);
    $coupon= Coupon::find($request->id);
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();
    return redirect()->route('admin.coupons')->with('Coupon has been Updated successfully!');


}


}
