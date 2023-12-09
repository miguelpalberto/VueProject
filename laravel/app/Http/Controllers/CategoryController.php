<?php

namespace App\Http\Controllers;

use App\Models\VCard;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }
    
    public function getVCardCategories(VCard $vcard, Request $request){
        $this->authorize('getVCardCategories', $vcard);

        $queryable = Category::query()->where('vcard', $vcard->phone_number)->orderBy('name', 'asc');

        $filterByType = $request->query('type');
        $filterByName = $request->query('name');

        if ($filterByType) {
            $types = ['D', 'C'];
            if (in_array($filterByType, $types))
            {
                $queryable->where('type', $filterByType);
            }
        }

        if ($filterByName) {
            $queryable->where(function ($query) use ($filterByName){
                $query->where('name', 'like', "%{$filterByName }%");
            });
        }

        return CategoryResource::collection($queryable->get());
    }

    public function store(CategoryRequest $request){
        $validRequest = $request->validated();

        if ($request->user()->username != $validRequest['vcard']) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create categories for your own vCard'
            ], 403);
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


    public function update(Category $category, UpdateCategoryRequest $request){
        $validRequest = $request->validated();

        $category->type = $validRequest['type'];
        $category->name = $validRequest['name'];
        $category->custom_options = $validRequest['custom_options'] ?? null;
        $category->custom_data = $validRequest['custom_data'] ?? null;
        $category->save();

        return new CategoryResource($category);
    }

    public function destroy(Category $category){
        $category->transactions()->update(['category_id' => null]);	
        $category->delete();

        return response()->noContent();
    }
}
