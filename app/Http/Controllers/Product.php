<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Product extends BaseController{


    function postProductData(Request $request){



        $imageUploadResult = Storage::put('public/products', $request->file,'public');

        if($imageUploadResult){

            $imagesPayload = [
                "name" => $this->getImageName($imageUploadResult),
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')                
            ];

            $imagesResult = DB::table('images')
            ->insert($imagesPayload);

            if($imagesResult){

                $productPayload = [
                    "name" => $request['productName'],
                    "category_id" => $request['category'],
                    "description" => $request['description'],
                    "mrp" => $request['mrp'],
                    "selling_price" => $request['sellingPrice'],
                    "store_id" => $request['storeId'],
                    "image_url"  => $imagesPayload["name"],
                    "in_stock" => true,
                    "is_active" => true,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                    
                ];

                $productsResult = DB::table('products')
                ->insert($productPayload);

                if($productsResult){

                    return response()->json([
                        "status" => "success",
                        "message" => "Successfully posted product data in the server !"
                    ]);  
                }
                else{
                    return response()->json([
                        "status" => "failed",
                        "message" => "Failed to store product data in the server !"
                    ]);  
                }


            }
            else{
                return response()->json([
                    "status" => "failed",
                    "message" => "Failed to store image in the server !"
                ]);
            }

            
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Image upload failed !"
            ]);
        }

    

    }

    function editProductData(Request $request){

        if($request->file == ""){

            $productPayload = [
                'store_id'=> $request['storeId'],
                'category_id'=> $request['categoryId'],
                'name'=> $request['productName'],
                'image_url'=> $request['imageUrl'],
                'description'=> $request['description'],
                'mrp'=> $request['mrp'],
                'selling_price'=> $request['sellingPrice'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $productResult = DB::table('products')
            ->where('id', $request['productId'])
            ->update($productPayload);

            if($productResult){

                return response()->json([
                    "status" => "success",
                    "message" => "Successfully updated data in the server !"
                ]);
            }
            else{

                return response()->json([
                    "status" => "failed",
                    "message" => "Failed to update data in the server !"
                ]);
            }




        }
        else{
            
            $imageUploadResult = Storage::put('public/products', $request->file,'public');
            
            if($imageUploadResult){

                $imagesPayload = [
                    "name" => $this->getImageName($imageUploadResult),
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')                
                ];
    
                $imagesResult = DB::table('images')
                ->insert($imagesPayload);
                
                if($imagesResult){

                    $productPayload = [
                        'store_id'=> $request['storeId'],
                        'category_id'=> $request['categoryId'],
                        'name'=> $request['productName'],
                        'image_url'=> $imagesPayload["name"],
                        'description'=> $request['description'],
                        'mrp'=> $request['mrp'],
                        'selling_price'=> $request['sellingPrice'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
        
                    $productResult = DB::table('products')
                    ->where('id', $request['productId'])
                    ->update($productPayload);
        
                    if($productResult){

                        return response()->json([
                            "status" => "success",
                            "message" => "Successfully updated data in the server !"
                        ]);
                    }
                    else{
        
                        return response()->json([
                            "status" => "failed",
                            "message" => "Failed to update data in the server !"
                        ]);
                    }

                }
                else{
                    return response()->json([
                        "status" => "failed",
                        "message" => "Failed to store image in the server !"
                    ]);
                }



            }
            else{
                return response()->json([
                    "status" => "failed",
                    "message" => "Image upload failed !"
                ]);
            }
            
            
        }

        

    }

    function deleteProductData(Request $request){

        $req =  $request->all();

        $result = DB::table('products')
        ->where('id', $req['id'])
        ->update([
            'is_active' => false
        ]);

        if($result){
            return response()->json([
                "status" => "success",
                "message" => "Successfully deleted data in the server !"
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to delete data in the server !"
            ]);
        }



    }

    function getProductData(Request $request){

        $result = DB::table('products')
        ->where('products.is_active',1)
        ->get();

        return $result;

    }

    function getProductDataByStoreId(Request $request){


        $req = $request->all();

        $result = DB::table('products')
        ->select('products.id','products.name','products.image_url','products.mrp','products.selling_price','categories.id as category_id','categories.name as category_name')
        ->where('products.is_active',1)
        ->where('products.in_stock',1)
        ->where('products.store_id',$req['store_id'])
        ->where('products.category_id',$req['category_id'])
        ->leftjoin('categories','products.category_id','categories.id')
        ->get();

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully got data from the server.",
                "payload" => $result
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to get data from the server."
            ]);
        }



    }

    function getAllProductsDataByStoreId(Request $request){

        $req = $request->all();

        $result = DB::table('products')
        ->select('products.id','products.name','products.image_url','products.mrp','products.selling_price','products.in_stock','categories.id as category_id','categories.name as category_name')
        ->where('products.is_active',1)
        ->where('products.store_id',$req['store_id'])
        ->leftjoin('categories','products.category_id','categories.id')
        ->get();


        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully got product data from the server.",
                "payload" => $result
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "Failed to get product data from the server."
            ]);
        }

    }


    function getProductById(Request $request){

        $req = $request->all();

        $result = DB::table('products')
                    ->where('products.id',$req['id'])
                    ->where('products.is_active',1)
                    ->select('products.id','products.name','products.description','products.mrp','products.selling_price','products.image_url','categories.name as category_name','categories.id as category_id')
                    ->leftjoin('categories','products.category_id','categories.id')
                    ->get();

        
        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully got data from the server.",
                "payload" => $result[0]
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "Failed to get data from the server."
            ]);
        }
        
        
    }

    function searchProduct(Request $request){

        $req = $request->all();

        $result  = DB::table('products')
        ->where('products.name', 'like', "%{$req['search']}%")
        ->where('products.store_id', $req['store_id'])
        ->where('products.is_active',1)
        ->where('products.in_stock',1)
        ->select('products.id','products.name','products.mrp','products.selling_price','products.in_stock','products.image_url','categories.name as category')
        ->leftjoin('categories','products.category_id','categories.id')
        ->get();

        if($result){
            
            return response()->json([
                "status" => "success",
                "message" => "Successfully fetched data from the server",
                "payload" => $result
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to fetch data from the server"
            ]);
        }

    }

    function searchInstockProduct(Request $request){

        $req = $request->all();

        $result  = DB::table('products')
        ->where('products.name', 'like', "%{$req['search']}%")
        ->where('products.store_id', $req['store_id'])
        ->where('products.is_active',1)
        ->where('products.in_stock',1)
        ->select('products.id','products.name','products.mrp','products.selling_price','products.in_stock','products.image_url','categories.name as category')
        ->leftjoin('categories','products.category_id','categories.id')
        ->get();

        if($result){
            
            return response()->json([
                "status" => "success",
                "message" => "Successfully fetched data from the server",
                "payload" => $result
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to fetch data from the server"
            ]);
        }

    }

    function productInstockChange(Request $request){

    
        $productPayload = [
            'in_stock'=> filter_var($request["in_stock"], FILTER_VALIDATE_BOOLEAN),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $productResult = DB::table('products')
        ->where('id', $request['product_id'])
        ->update($productPayload);

        if($productResult){

            return response()->json([
                "status" => "success",
                "message" => "Successfully changed status.",
                "payload" => [
                    "product_id" => $request['product_id'],
                    "in_stock" => $request["in_stock"]
                ]
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "Failed to change status of the product."
            ]);
        }
        
    }

    function processQueryResult($result){

        if($result){
            return response()->json(["status"=>"success"]);
        }
        else{
            return response()->json(["status"=>"failed"]);
        }

    }


    function getImageName($path){

        $pathArr = explode('/',$path);
        return end($pathArr);

    }


}

