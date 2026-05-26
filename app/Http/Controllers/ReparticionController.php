<?php

namespace App\Http\Controllers;

use App\Models\Reparticion;
use Illuminate\Http\Request;

class ReparticionController extends Controller
{
    //
    function getReparticion(){
        return Reparticion::all();
    }
}
