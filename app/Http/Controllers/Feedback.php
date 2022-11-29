<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Feedback extends BaseController{

    
 
    function postFeedbackData(Request $request){

        $req = $request->all();

        $feedback = [
            'user_id'=>  $req['user_id'],
            'feedback'=> $req['feedback'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];


        $result = DB::table('feedbacks')
        ->insert($feedback);

        if($result){

            return response()->json([
                "status" => "success",
                "message" => "Successfully posted feedback in the server."
            ]);
        }
        else{
            return response()->json([
                "status" => "failed",
                "message" => "Failed to post feedback in the server."
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

