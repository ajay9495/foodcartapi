<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Category extends BaseController{


    function postCategoryData(Request $request){


        $imageUploadResult = Storage::put('public/categories', $request->file,'public');


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

                $categoryPayload = [
                    "name" => $request['categoryName'],
                    "store_id" => $request['storeId'],
                    "image_url"  => $imagesPayload["name"],
                    "is_active" => true,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                ];

                $categoryResult = DB::table('categories')
                ->insert($categoryPayload);

                if($categoryResult){

                    return response()->json([
                        "status" => "success",
                        "message" => "Successfully posted data in the server.",
                        "payload" => $categoryResult
                    ]);
                }
                else{

                    return response()->json([
                        "status" => "failed",
                        "message" => "Failed to post data in the server !"
                    ]);
                }

            }
            else{
                return response()->json([
                    "status" => "failed",
                    "message" => "Failed to post image in the server !"
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

    function getCategoryDataByStoreId(Request $request){

        $req = $request->all();

        $result = DB::table('categories')
                    ->select('id','name','image_url')
                    ->where('store_id',$req['store_id'])
                    ->where('is_active',1)
                    ->get();

        if($result){
            return response()->json([
                "status" => "success",
                "message" => "Request successful",
                "payload" => $result
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to get category data from the server."
            ]);
        }
    }

    function editCategoryData(Request $request){

        if($request->file == ""){

            $categoryPayload = [
                'store_id'=> $request["storeId"],
                'name'=> $request["categoryName"],
                'image_url'=> $request["imageUrl"],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = DB::table('categories')
            ->where('id',$request["categoryId"])
            ->update($categoryPayload);

            return $this->processQueryResult($result);

        }
        else{

            $imageUploadResult = Storage::put('public/categories', $request->file,'public');

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

                    $categoryPayload = [
                        'store_id'=> $request["storeId"],
                        'name'=> $request["categoryName"],
                        'image_url'=> $imagesPayload['name'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
        
                    $categoryResult = DB::table('categories')
                    ->where('id',$request["categoryId"])
                    ->update($categoryPayload);
        
                    if($categoryResult){

                        return response()->json([
                            "status" => "success",
                            "message" => "Successfully posted data in the server.",
                            "payload" => $categoryResult
                        ]);
                    }
                    else{

                        return response()->json([
                            "status" => "failed",
                            "message" => "Failed to post data in the server !"
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

    function deleteCategory(Request $request){

        $req = $request->all();

        $result = DB::table('categories')
        ->where('id',$req['id'])
        ->update([
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully deleted category from the database."
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "failed to delete category from the database."
            ]);
        }

    }

    function getCategoryById(Request $request){

        $req = $request->all();

        $result = DB::table('categories')
        ->where('id',$req['id'])
        ->where('is_active',1)
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



