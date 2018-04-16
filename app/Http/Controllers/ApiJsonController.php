<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiJsonController extends Controller
{
    public function index(){
        return view('apijson.index');
    }
}
