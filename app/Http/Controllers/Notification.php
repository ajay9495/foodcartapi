<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Notification extends BaseController{

    function postFCMtoken(Request $request){
        
        $req = $request->all(); 

        return response()->json([
            "status" => "success"
        ]);


    }
    


}









