<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    private function resolveCategoryView(string $page): string
    {
        return "categories.{$page}";
    }

    public function index()
    {
        // Eager-load creator relation to avoid N+1
        $categories = Category::with(['creator:id,name', 'products'])->latest()->get();

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
            ->to(shop_route('categories.index'))
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
            ->to(shop_route('categories.index'))
            ->with('success', 'Category has been updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->to(shop_route('categories.index'))
            ->with('success', 'Category has been deleted!');
    }
}
