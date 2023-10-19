<?php

namespace App\Http\Controllers;

use App\Models\DefaultCategory;
use Illuminate\Http\Request;

class DefaultCategoryController extends Controller
{
    public function index()
    {
        $defaultCategories = DefaultCategory::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved default categories',
            'data' => $defaultCategories
        ], 200);
    }
}
