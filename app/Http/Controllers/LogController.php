<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log; 

class LogController extends Controller
{
    public function index()
    {
        
        $logs = Log::orderBy('created_at', 'desc')->paginate(20);

        
        return view('logs.index', compact('logs'));
    }
}
