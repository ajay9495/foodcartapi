<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Category;
use App\Http\Controllers\Store;
use App\Http\Controllers\Product;
use App\Http\Controllers\Visitor;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Order;
use App\Http\Controllers\Unit;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Feedback;
use App\Http\Controllers\User;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Delivery;
use App\Http\Controllers\StoreKeeper;
use App\Http\Controllers\Notification;


Route::post('postStoreKeeperToken',[Notification::class,'postStoreKeeperToken']);
Route::post('postDeliveryToken',[Notification::class,'postDeliveryToken']);


Route::post('PostStoreData',[Store::class,'PostStoreData']);
Route::post('addUnit',[Unit::class,'addUnit']);
Route::get('getUnitData',[Unit::class,'getUnitData']);

Route::post('postCategoryData',[Category::class,'postCategoryData']);
Route::get('getCategoryData',[Category::class,'getCategoryData']);
Route::post('editCategoryData',[Category::class,'editCategoryData']);
Route::post('deleteCategory',[Category::class,'deleteCategory']);
Route::get('getCategoryById',[Category::class,'getCategoryById']);
Route::get('getCategoryDataByStoreId',[Category::class,'getCategoryDataByStoreId']);

Route::post('postProductData',[Product::class,'postProductData']);
Route::post('editProductData',[Product::class,'editProductData']);
Route::post('deleteProductData',[Product::class,'deleteProductData']);
Route::get('getProductData',[Product::class,'getProductData']);
Route::get('getProductById',[Product::class,'getProductById']);
Route::get('getProductDataByStoreId',[Product::class,'getProductDataByStoreId']);
Route::get('searchProduct',[Product::class,'searchProduct']);
Route::get('getAllProductsDataByStoreId',[Product::class,'getAllProductsDataByStoreId']);
Route::get('productInstockChange',[Product::class,'productInstockChange']);
Route::get('searchInstockProduct',[Product::class,'searchInstockProduct']);

Route::post('postVisitorData',[Visitor::class,'postVisitorData']);

Route::get('getAdminDashboardData',[Dashboard::class,'getAdminDashboardData']);


Route::get('completeDelivery',[Order::class,'completeDelivery']);
Route::get('acceptDelivery',[Order::class,'acceptDelivery']);
Route::get('getOrderDetails',[Order::class,'getOrderDetails']);
Route::get('getOrdersData',[Order::class,'getOrdersData']);
Route::get('getOrderPlacedData',[Order::class,'getOrderPlacedData']);
Route::get('getOrderPlacedDetails',[Order::class,'getOrderPlacedDetails']);
Route::post('postOrderData',[Order::class,'postOrderData']);
Route::post('setOrderFulfilled',[Order::class,'setOrderFulfilled']);
Route::get('getOrdersToBeCompleted',[Order::class,'getOrdersToBeCompleted']);


Route::post('postFeedbackData',[Feedback::class,'postFeedbackData']);

Route::post('RegisterUser',[User::class,'RegisterUser']);
Route::post('LoginUser',[User::class,'LoginUser']);
Route::post('SetToken',[User::class,'SetToken']);
Route::post('postAddressData',[User::class,'postAddressData']);


Route::post('Login',[Admin::class,'Login']);
Route::post('Register',[Admin::class,'Register']);



Route::post('SuperLogin',[SuperAdmin::class,'SuperLogin']);
Route::post('SuperReg',[SuperAdmin::class,'SuperReg']);
Route::get('getApprovalsData',[SuperAdmin::class,'getApprovalsData']);
Route::post('approveAdmin',[SuperAdmin::class,'approveAdmin']);
Route::get('getDashboardData',[SuperAdmin::class,'getDashboardData']);

Route::post('DeliveryLogin',[Delivery::class,'DeliveryLogin']);
Route::post('postDeliveryData',[Delivery::class,'postDeliveryData']);
Route::get('getDeliveryData',[Delivery::class,'getDeliveryData']);
Route::post('deleteDeliveryData',[Delivery::class,'deleteDeliveryData']);

Route::post('StoreKeeperLogin',[StoreKeeper::class,'StoreKeeperLogin']);
Route::post('postStoreKeeperData',[StoreKeeper::class,'postStoreKeeperData']);
Route::get('getStoreKeeperData',[StoreKeeper::class,'getStoreKeeperData']);
Route::post('deleteStoreKeeper',[StoreKeeper::class,'deleteStoreKeeper']);

