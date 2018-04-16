<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RotasController extends Controller
{
    public function index(){
        return view('rotas.index');
    }
}
