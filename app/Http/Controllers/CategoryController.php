<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $defaultCategories = Category::where('is_default', true)->get();

        $customCategories = Category::where('user_id', $request->user()->id)->get();

        return Inertia::render('categories/Index', [
            'defaultCategories' => $defaultCategories,
            'customCategories' => $customCategories,
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'icon' => $request->icon ?? 'tag',
            'type' => $request->type,
            'is_default' => false,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تمت إضافة التصنيف بنجاح']);

        return to_route('categories.index');
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        if ($category->is_default || $category->user_id !== $request->user()->id) {
            abort(403);
        }

        $category->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم تحديث التصنيف بنجاح']);

        return to_route('categories.index');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->is_default || $category->user_id !== $request->user()->id) {
            abort(403);
        }

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم حذف التصنيف بنجاح']);

        return to_route('categories.index');
    }
}
