<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Delivery extends BaseController{

    function DeliveryLogin(Request $request){


        $payload = [
            'password' => $request[0]['value'],
            'phone'=> $request[1]['value'],
            'store_id' => $request[2]['value']
        ];



        $result =  DB::table('delivery')
        ->select('id','password','name','is_active','store_id')   
        ->where('phone',$payload['phone'])
        ->get();



        if(count($result) > 0){

            $dbPassword = $result[0]->password;
            $dbStoreId = $result[0]->store_id;
            $userPassword = $payload['password'];
            $userStoreId = $payload['store_id'];

            if($dbPassword ==  $userPassword){

                if($dbStoreId == $userStoreId){

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
                        "message" => "Store ID is invalid"
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

    function Register(Request $request){

        $req = $request->all();

        $payload = [

            'name'=> $req[0]['value'],
            'email'=> $req[1]['value'],
            'phone'=> $req[2]['value'],
            'password' => $req[3]['value'],
            'store_id' => '',
            'is_active'=> false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        ];

        $result = DB::table('admin')
                    ->insertGetId($payload);

        if($result){

            $responsePayload = [
                "admin_id" => $result,
                "status"   => "success"
            ];

            return response()->json($responsePayload);
        }
        else{

            $responsePayload = [
                "status"   => "failed"
            ];
            
            return response()->json($responsePayload);
        }


        


    }



    function deleteDeliveryData(Request $request){

        $payload  = [
            "is_active" => false,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result  =  DB::table('delivery')
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


    function postDeliveryData(Request $request){


        $payload = [
            "name" => $request["name"],
            "phone" => $request["phone"],
            "password" => $request["password"],
            'store_id' => $request["storeId"],
            'is_active'=> true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];



        $result  = DB::table('delivery')
        ->insert($payload);


        if($result){
            
            return response()->json([
                "status" => "success",
                "message" => "Successfully posted data in the server."
            ]);
        }
        else{

            return reponse()->json([
                "status" => "failed",
                "message" => "Failed to post data in the server."
            ]);
        }

    }

    function getDeliveryData(Request $request){


        $result = DB::table("delivery")
        ->where('is_active',true)
        ->where('store_id',$request["storeId"])
        ->select('id','name')
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



}