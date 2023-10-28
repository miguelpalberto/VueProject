<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved categories',
            'data' => $categories
        ], 200);
    }

}
