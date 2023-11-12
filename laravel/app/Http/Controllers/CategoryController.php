<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\VCard;

class CategoryController extends Controller
{
    public function index(){
        return Category::all();
    }


    public function getVCardCategories(VCard $vcard){
        //todo deleted categories wont show up -ver 1o se tem
        return $vcard->categories;
    }

    public function create(CategoryRequest $request){
        $validRequest = $request->validated();

        $category = new Category();
        $category->vcard = $validRequest['vcard'];
        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return $category;
    }


    public function update(Category $category, CategoryRequest $request){
        $validRequest = $request->validated();

        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return $category;
    }

    public function destroy(Category $category){
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted category',
            'data' => $category
        ], 200);
    }
}
