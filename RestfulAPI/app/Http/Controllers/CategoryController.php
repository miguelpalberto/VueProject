<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\VCard;

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


    public function getByVCard(VCard $vcard){
        $categories = Category::where('vcard', $vcard->phone_number)->get();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved categories',
            'data' => $categories
        ], 200);
    }

    public function createByVCard(VCard $vcard, CategoryRequest $request){
        $validRequest = $request->validated();

        $category = new Category();
        $category->vcard = $vcard->phone_number;
        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully created category',
            'data' => $category
        ], 201);
    }

    public function updateByVCard(VCard $vcard, Category $category, CategoryRequest $request){
        $validRequest = $request->validated();

        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated category',
            'data' => $category
        ], 200);
    }

    public function deleteByVCard(VCard $vcard, Category $category){
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted category',
            'data' => $category
        ], 200);
    }
}
