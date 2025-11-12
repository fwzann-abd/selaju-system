<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArticleCategory;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('category')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = ArticleCategory::orderBy('name')->get();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|uuid|exists:article_categories,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'sometimes|boolean',
        ]);

        $slug = Str::slug($data['title']);
        $base = $slug;
        $i = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $article = Article::create(array_merge($data, ['slug' => $slug]));

        return redirect()->route('admin.articles.index')->with('success', 'Article created.');
    }

    public function show($id)
    {
        $article = Article::find($id);
        return view('admin.articles.show', compact('article'));
    }

    public function edit($id)
    {
        $article = Article::find($id);
        $categories = ArticleCategory::orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|uuid|exists:article_categories,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'sometimes|boolean',
        ]);

        // update slug if title changed
        if (($data['title'] ?? null) && $data['title'] !== $article->title) {
            $slug = Str::slug($data['title']);
            $base = $slug;
            $i = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated.');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Article deleted.');
    }
}

