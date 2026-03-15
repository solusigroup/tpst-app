<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Test500Controller extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'OK', 'message' => 'Controller was instantiated and executed successfully']);
    }
}
