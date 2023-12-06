<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\VCard;

class CategoryController extends Controller
{
    public function index(){
        return Category::all();
    }

    public function getVCardCategories(VCard $vcard){
        return CategoryResource::collection($vcard->categories);
    }

    public function store(CategoryRequest $request){
        $validRequest = $request->validated();


        //check if category combination exists
        if (Category::where('vcard', $validRequest['vcard'])->where('type', $validRequest['type'])->where('name', $validRequest['name'])->exists()) {
            $typeToPrettyString = $validRequest['type'] === 'C' ? 'Credit' : 'Debit';
            return response()->json([
                'errors' => [
                    'name' => [
                        "The category '" . $validRequest['name'] . "' with the type '" . $typeToPrettyString . "' already exists for this vCard."
                    ]
                ]
            ], 422);
        }


        $category = new Category();
        $category->vcard = $validRequest['vcard'];
        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return new CategoryResource($category);
    }


    public function update(Category $category, CategoryRequest $request){
        $validRequest = $request->validated();

        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return new CategoryResource($category);
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
