<?php

namespace App\Http\Controllers;

use App\Http\Requests\DefaultCategoryRequest;
use App\Models\DefaultCategory;

class DefaultCategoryController extends Controller
{
    public function index()
    {
        return DefaultCategory::all();
    }

    public function store(DefaultCategoryRequest $request)
    {
        $validRequest = $request->validated();

        $defaultCategory = new DefaultCategory();
        $defaultCategory->type = $validRequest['type'];
        $defaultCategory->name = $validRequest['name'];
        $defaultCategory->custom_options = $validRequest['custom_options'];
        $defaultCategory->custom_data = $validRequest['custom_data'];
        $defaultCategory->save();

        return $defaultCategory;
    }

    public function update(DefaultCategory $defaultCategory, DefaultCategoryRequest $request)
    {
        $validRequest = $request->validated();

        $defaultCategory->type = $validRequest['type'];
        $defaultCategory->name = $validRequest['name'];
        $defaultCategory->custom_options = $validRequest['customOptions'];
        $defaultCategory->custom_data = $validRequest['custom_data'];
        $defaultCategory->save();

        return $defaultCategory;
    }

    public function destroy(DefaultCategory $defaultCategory)
    {
        $defaultCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted default category',
            'data' => $defaultCategory
        ], 200);
    }
}
