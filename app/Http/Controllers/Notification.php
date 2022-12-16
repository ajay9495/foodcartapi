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

    function sendDeliveryNotification($store_id){
        
        return $this->getDeliveryPartnersOfTheStore($store_id);

        $to = "fSGnzR2dTGOf6dGqLOjO0T:APA91bHngVjTZ9l1YrI6KGQeAroYeEet1FgFr3mPWwDYzTJrH_5ykF0N8kYwoYqCIXNXGcV29LKM1eyvYkxOr0AOKMTqmAJMtJ-tjW0C3ffd44rv8viuu5J1EmcsF4MVuieo_p6Jn-BR";
        $notificationArray = [
            "body" => "test body",
            "title" => "test title",
            "subtitle" => "subtitle"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = array('to' => $to, 'notification' => $notificationArray);
        $json_data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);        
        
        // Set headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: key=AAAANoAR07U:APA91bF9IurQzPd2UEfXBR18ezSsCM1G8AAIyHNRABNXQf8ttwXLm2uJiyzpl8_9z_KaHVfZ3yxhaj8TQSnXqlaVl-XfR-_H4xkNAYAK6qje20iP5q-5kORRhggeAjEJyEWXukoZQ1hz',
            'Content-Length: ' . strlen($json_data)
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        
        curl_close($ch);
        
    }




    function getDeliveryPartnersOfTheStore($store_id){

        $result = DB::table("delivery_token")
        ->where('store_id',$store_id)
        ->select('token')
        ->get();

        return $store_id;
    }


}









