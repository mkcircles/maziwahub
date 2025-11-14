<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MilkCollectionCenter;

class AdminController extends Controller
{
    public function index(){
        return view('ADMIN.dashboard');
    }

    public function milkCenters(){
        $milkCenters = MilkCollectionCenter::paginate(10);
        return view('ADMIN.milkCenters', compact('milkCenters'));
    }
}
