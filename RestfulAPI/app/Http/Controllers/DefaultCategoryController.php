<?php

namespace App\Http\Controllers;

use App\Http\Requests\DefaultCategoryRequest;
use App\Models\DefaultCategory;

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

    public function store(DefaultCategoryRequest $request)
    {
        $validRequest = $request->validated();

        $defaultCategory = new DefaultCategory();
        $defaultCategory->type = $validRequest['type'];
        $defaultCategory->name = $validRequest['name'];
        $defaultCategory->customOptions = $validRequest['customOptions'];
        $defaultCategory->customData = $validRequest['custom_data'];
        $defaultCategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully created default category',
            'data' => $defaultCategory
        ], 201);
    }

    public function update(DefaultCategory $defaultCategory, DefaultCategoryRequest $request)
    {
        $validRequest = $request->validated();

        $defaultCategory->type = $validRequest['type'];
        $defaultCategory->name = $validRequest['name'];
        $defaultCategory->customOptions = $validRequest['customOptions'];
        $defaultCategory->customData = $validRequest['custom_data'];
        $defaultCategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated default category',
            'data' => $defaultCategory
        ], 200);
    }

    public function delete(DefaultCategory $defaultCategory)
    {
        $defaultCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted default category',
            'data' => $defaultCategory
        ], 200);
    }
}
