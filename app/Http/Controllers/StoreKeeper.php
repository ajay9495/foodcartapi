<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreKeeper extends BaseController{

    function StoreKeeperLogin(Request $request){

        
        $payload = [
            'phone'=> $request[1]['value'],
            'password' => $request[0]['value']
        ];


        $result =  DB::table('store-keeper')
        ->select('id','password','name','is_active','store_id')   
        ->where('phone',$payload['phone'])
        ->get();


        if(count($result) > 0){

            $dbPassword = $result[0]->password;
            $userPassword = $payload['password'];

            if($dbPassword ==  $userPassword){

                if($result[0]->is_active){

                    return response()->json([
                        "status" => "success",
                        "message" => "User is Active",
                        "payload" => $result[0]
                    ]);
                }
                else{
                    return response()->json([
                        "status" => "failed",
                        "message" => "User is inActive"
                    ]);
                }
            }
            else{
                return response()->json([
                    "status" => "failed",
                    "message" => "Password did not match"
                ]);
            }
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "User not found"
            ]);
        }     
    }


    function postStoreKeeperData(Request $request){

        $payload = [
            "name" => $request["name"],
            "phone" => $request["phone"],
            "password" => $request["password"],
            'store_id' => $request["storeId"],
            'is_active'=> true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result  = DB::table('store-keeper')
        ->insert($payload);

        if($result){
            return response()->json([
                "status" => "success"
            ]);
        }
        else{
            return reponse()->json([
                "status" => "failed"
            ]);
        }

        return response()->json($result);
    }

    function getStoreKeeperData(Request $request){

        $result = DB::table("store-keeper")
        ->where('is_active',true)
        ->where('store_id',$request["storeId"])
        ->select('id','name')
        ->get();

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully got data",
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

    function deleteStoreKeeper(Request $request){

        $payload  = [
            "is_active" => false,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result  =  DB::table("store-keeper")
        ->where('id',$request["id"])
        ->update($payload);

        if($result){
            return response()->json([
                "status"=> "success"
            ]);
        }
        else{
            return response()->json([
                "status"=> "failed"
            ]);
        }
    }


}