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
    


}









