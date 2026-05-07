<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    private function resolveCategoryView(string $page): string
    {
        $shopType = active_shop_type();
        $shopType = $shopType ? shop_type_route_key($shopType) : 'tech';
        $shopTypeView = "shop-types.{$shopType}.categories.{$page}";

        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        return "categories.{$page}";
    }

    public function index()
    {
        // Eager-load creator relation to avoid N+1
        $categories = Category::with('creator:id,name')->latest()->get();

        return view($this->resolveCategoryView('index'), [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view($this->resolveCategoryView('create'));
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category has been created!');
    }

    public function show(Category $category)
    {
        return view($this->resolveCategoryView('show'), [
            'category' => $category
        ]);
    }

    public function edit(Category $category)
    {
        return view($this->resolveCategoryView('edit'), [
            'category' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->all());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category has been updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category has been deleted!');
    }
}
