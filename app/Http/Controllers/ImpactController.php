<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;

class ImpactController extends Controller
{
    public function impact(){

        $return = Input::all();

        return response()->json($return);
    }
}
