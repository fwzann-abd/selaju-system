<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::orderBy('name')->get();

        return view('admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.article-categories.create');
    }

    public function store(Request $request)
    {
        // Placeholder: implement saving logic later
        return redirect()->route('admin.article-categories.index')->with('success', 'Category saved (stub).');
    }

    public function show($id)
    {
        return view('admin.article-categories.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('admin.article-categories.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        // Placeholder
        return redirect()->route('admin.article-categories.index')->with('success', 'Category updated (stub).');
    }

    public function destroy($id)
    {
        // Placeholder
        return redirect()->route('admin.article-categories.index')->with('success', 'Category deleted (stub).');
    }
}
