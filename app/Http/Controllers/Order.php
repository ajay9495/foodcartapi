<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

 
class Order extends BaseController{

    function postOrderData(Request $request){


        $request->all();

        $orderDetails = [
            'user_id'=>$request['user_id'],
            'store_id'=>$request['store_id'],
            'status'=>'orderPlaced',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]; 



        $orderId = DB::table('orders')
        ->insertGetId($orderDetails);



        $ordersList = [];
        $orderItem = [];
        $reqOrderArr = $request['orders'];



        for($i = 0; $i < count($reqOrderArr);$i++){

            $orderItem = [
                'order_id'=> $orderId,
                'product_id'=>$reqOrderArr[$i]['id'],
                'quantity'=>$reqOrderArr[$i]['quantity'],
                'selling_price'=>$reqOrderArr[$i]['selling_price'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            array_push($ordersList,$orderItem);
        }



        $result = DB::table('order_details') 
        ->insert($ordersList);

        
        if($result){


            return response()->json([
                "status" => "success",
                "message" => "Successfully posted data in the server and sent notification via app method",
                "payload" => $notifResult
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to put data in the server"
            ]);
        }

    

    }

    function getOrdersData(Request $request){

        $req = $request->all();

        $startDate = date('Y-m-d',strtotime('-5 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $result = DB::table('orders')
        ->where('user_id',$req['user_id'])
        ->whereBetween('orders.created_at',[$startDate,$endDate])
        ->leftjoin('order_details','orders.id','order_details.order_id')
        ->select('orders.status',DB::raw('DATE(orders.created_at) as date'),'order_details.product_id','order_details.order_id','order_details.quantity','order_details.selling_price')
        ->orderBy('orders.id', 'asc')
        ->get()
        ->groupBy('order_id')
        ->map(function($item){
            return [
                'price' => $this->getSum($item),
                'order_id' => $item[0]->order_id,
                'date' => $item[0]->date,
                'status' => $item[0]->status,
            ];
        })
        ->toArray();    
        
        if($result){
            return response()->json([
                "status" => "success",
                "message" => "Successfully fetched data",
                "payload" => array_reverse($result)
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to fetch data from the server.",
            ]);
        }

    }

    function getOrderDetails(Request $request){

        $req = $request->all();

        $result = DB::table('order_details')
        ->where('order_id',$req['order_id'])
        ->leftjoin('products','order_details.product_id','products.id')
        ->select('order_details.quantity','order_details.selling_price','products.name','products.image_url')
        ->get();

        if($result){
            return response()->json([
                "status" =>  "success",
                "message" => "Successfully fetched data from server",
                "payload" => $result
            ]);
        }
        else{
            return response()->json([
                "status" =>  "failed",
                "message" => "Could not fetch data from server"
            ]);
        }

    }

    function getOrderPlacedData(Request $request){


        $startDate = date('Y-m-d',strtotime('-30 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $result = DB::table('orders')
        ->where('orders.status','orderPlaced')
        ->whereBetween('orders.created_at',[$startDate,$endDate])
        ->leftjoin('order_details','orders.id','order_details.order_id')
        ->select(DB::raw('DATE(orders.created_at) as date'),'order_details.product_id','order_details.order_id','order_details.quantity','order_details.selling_price')
        ->get()
        ->groupBy('order_id')
        ->map(function($item){
            return [
                'price' => $this->getSum($item),
                'order_id' => $item[0]->order_id,
                'date' => $item[0]->date,
            ];
        });

        if($result){

            $resultArray = array_values($result->toArray());
            return response()->json([
                "status" => "success",
                "message" => "Successfully got data from the server.",
                "payload" => $resultArray
            ]);

        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Faliled to get data from the server."
            ]);
        }

                        
    }

    function getOrderPlacedDetails(Request $request){

        $req = $request->all();

        $result = DB::table('order_details')
        ->where('order_id',$req['order_id'])
        ->leftjoin('products','order_details.product_id','products.id')
        ->select('order_details.quantity','order_details.selling_price','products.name','products.image_url')
        ->get();
        
        if($result){
            return response()->json([
                "status" =>  "success",
                "message" => "Successfully fetched data from server",
                "payload" => $result
            ]);
        }
        else{
            return response()->json([
                "status" =>  "failed",
                "message" => "Could not fetch data from server"
            ]);
        }

    }

    function setOrderFulfilled(Request $request){

        $req = $request->all();

    
        $result  = DB::table("orders")
        ->where("id",$req["order_id"])
        ->update([
            "status" => "fulfilled"
        ]);


        return response()->json($req);

        if($result){

            $notificationController = app('App\Http\Controllers\Notification');
            $notifResult = $notificationController->sendDeliveryNotification($request['store_id']);

            return response()->json([
                "status" => "success"
            ]);
        }
        else{
            return response()->json([
                "status" => "failed"
            ]);
        }

    }

    function getOrdersToBeCompleted(Request $request){


        $startDate = date('Y-m-d',strtotime('-30 days')).' 01:00:00';
        $endDate = date('Y-m-d').' 23:00:00';

        $result = DB::table('orders')
        ->where('orders.status','!=','orderPlaced')
        ->where('orders.status','!=','completed')
        ->whereBetween('orders.created_at',[$startDate,$endDate])
        ->leftjoin('users','orders.user_id','users.id')
        ->select('orders.status','orders.id as order_id','users.name','users.location','users.address','users.landmark','users.id as user_id')
        ->get();

        if($result){

            $arrayResult = array_values($result->toArray());

            return response()->json([
                "status" => "success",
                "payload" => $arrayResult
            ]);
        }
        else{
            return response()->json([
                "status" => "failed"
            ]);
        }
                   


        

    }

    function acceptDelivery(Request $request){

        $result  = DB::table("orders")
        ->where("id",$request["order_id"])
        ->update([
            "status" => "outForDelivery"
        ]);


        if($result){
            return response()->json([
                "status" => "success"
            ]);
        }
        else{
            return response()->json([
                "status" => "failed"
            ]);
        }

    }

    function completeDelivery(Request $request){

        $result  = DB::table("orders")
        ->where("id",$request["order_id"])
        ->update([
            "status" => "completed"
        ]);

        if($result){
            return response()->json([
                "status" => "success"
            ]);
        }
        else{
            return response()->json([
                "status" => "failed"
            ]);
        }



    }



    
    function getSum($arr){


        $sum = 0;
        $item_price;
        $item_quantity;
        $selling_price = 0;
        $quantity = 0;


        for($i = 0; $i< count($arr);$i++ ){

            $item_price = $arr[$i]->selling_price;
            $quantity = $arr[$i]->quantity;
            $selling_price = $item_price * $quantity;

            $sum = $sum + $selling_price;
        }

        return $sum;
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