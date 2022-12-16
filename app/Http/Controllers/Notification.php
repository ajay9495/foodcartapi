<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Notification extends BaseController{

    function postStoreKeeperToken(Request $request){
        
        $req = $request->all(); 

        $payload = [

            'store_keeper_id'=> $req['store_keeper_id'],
            'store_id' => $req['store_id'],
            'token' => $req['token'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('store_keeper_token')
        ->insert($payload);

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "successfully posted data"
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "failed to post data"
            ]);
        }
      


    }

    function postDeliveryToken(Request $request){

        $req = $request->all(); 

        $payload = [

            'delivery_id'=> $req['delivery_id'],
            'store_id' => $req['store_id'],
            'token' => $req['token'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('delivery_token')
        ->insert($payload);

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "successfully posted data"
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "failed to post data"
            ]);
        }


    }
    
    function postUserToken(Request $request){

        $req = $request->all(); 

        $payload = [

            'user_id'=> $req['user_id'],
            'store_id' => $req['store_id'],
            'token' => $req['token'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = DB::table('user_token')
        ->insert($payload);

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "successfully posted data"
            ]);
        }
        else{

            return response()->json([
                "status" => "failed",
                "message" => "failed to post data"
            ]);
        }

        
    }

    function sendDeliveryNotification(Request $request){

        return response()->json([
            "status" => "success",
            "message" => "successfully posted data"
        ]);
    }

}









