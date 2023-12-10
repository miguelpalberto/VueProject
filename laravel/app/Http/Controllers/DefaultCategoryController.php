<?php

namespace App\Http\Controllers;

use App\Http\Requests\DefaultCategoryRequest;
use App\Models\DefaultCategory;
use Illuminate\Http\Request;

class DefaultCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DefaultCategory::class, 'defaultCategory');
    }

    public function index(Request $request)
    {
        $queryable = DefaultCategory::query()->orderBy('name', 'asc');

        $filterByType = $request->query('type');
        $filterByName = $request->query('name');

        if ($filterByType) {
            $types = ['D', 'C'];
            if (in_array($filterByType, $types))
            {
                $queryable->where('type', $filterByType);
            }
        }

        // if ($filterByName) {
        //     $queryable->where('name', 'like', '%' . $filterByName . '%');
        // }
        if ($filterByName) {
            $queryable->where(function ($query) use ($filterByName){
                $query->where('name', 'like', "%{$filterByName }%");
            });
        }

        //return $queryable->get();
        return $queryable->paginate(10);
    }

    public function store(DefaultCategoryRequest $request)
    {
        $validRequest = $request->validated();

        $defaultCategory = new DefaultCategory();
        $defaultCategory->type = $validRequest['type'];
        $defaultCategory->name = $validRequest['name'];
        $defaultCategory->custom_options = $validRequest['custom_options'] ?? null;
        $defaultCategory->custom_data = $validRequest['custom_data'] ?? null;
        $defaultCategory->save();

        return $defaultCategory;
    }

    public function update(DefaultCategory $defaultCategory, DefaultCategoryRequest $request)
    {
        $validRequest = $request->validated();

        $defaultCategory->type = $validRequest['type'];
        $defaultCategory->name = $validRequest['name'];
        $defaultCategory->custom_options = $validRequest['custom_options'] ?? null;
        $defaultCategory->custom_data = $validRequest['custom_data'] ?? null;
        $defaultCategory->save();

        return $defaultCategory;
    }

    public function destroy(DefaultCategory $defaultCategory)
    {
        $defaultCategory->delete();
        return response()->noContent();
    }
}
