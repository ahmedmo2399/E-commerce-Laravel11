<?php

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ShopController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'product_details'])->name('shop.product.details');
Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[UserController::class,'index'])->name('user.index');
});
Route::middleware(['auth',AuthAdmin::class])->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');

   Route::get('/admin/brands', [BrandController::class, 'index'])->name('admin.brand.index');
Route::get('/admin/brand/add', [BrandController::class, 'create'])->name('admin.brand.add');
Route::post('/admin/brand/store', [BrandController::class, 'store'])->name('admin.brand.store');
Route::get('/admin/brand/edit/{id}', [BrandController::class, 'edit'])->name('admin.brand.edit');
Route::put('/admin/brand/update', [BrandController::class, 'update'])->name('admin.brand.update');
Route::delete('/admin/brand/{id}/delete', [BrandController::class, 'destroy'])->name('admin.brand.delete');




    Route::get('/categories', [CategoryController::class, 'categories'])->name('admin.category.index');
    Route::get('/category/add', [CategoryController::class, 'category_add'])->name('admin.category.add');
    Route::post('/category/store', [CategoryController::class, 'category_store'])->name('admin.category.store');
    Route::get('/category/edit/{id}', [CategoryController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/category/update', [CategoryController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/category/{id}/delete', [CategoryController::class, 'category_delete'])->name('admin.category.delete');



    Route::get('/admin/products',[ProductController::class,'products'])->name('admin.product.index');
    Route::get('/admin/product/add',[ProductController::class,'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store',[ProductController::class,'product_store'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}', [ProductController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update/{id}', [ProductController::class, 'product_update'])->name('admin.product.update');
    Route::get('admin/product/{id}/show', [ProductController::class, 'product_show'])->name('admin.product.show');
    Route::delete('admin/product/{id}', [ProductController::class, 'product_delete'])->name('admin.product.delete');

});

