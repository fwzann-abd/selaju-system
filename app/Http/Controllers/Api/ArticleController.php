<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('category')
            ->where('status', true)
            ->orderBy('created_at', 'desc');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category)->orWhere('id', $category);
            });
        }

        $limit = $request->query('limit');
        if ($limit) {
            $query->limit((int) $limit);
        }

        $articles = $query->get()->map(function ($article) {
            return $this->transformArticle($article, false);
        });

        return response()->json([
            'data' => $articles,
        ]);
    }

    public function show($slugOrId)
    {
        $article = Article::with('category')
            ->where('slug', $slugOrId)
            ->orWhere('id', $slugOrId)
            ->firstOrFail();

        return response()->json([
            'data' => $this->transformArticle($article, true),
        ]);
    }

    protected function transformArticle(Article $article, bool $includeContent)
    {
        $excerpt = $article->excerpt ?: Str::limit(strip_tags($article->content ?? ''), 140);

        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'excerpt' => $excerpt,
            'content' => $includeContent ? $article->content : null,
            'featured_image' => $article->featured_image,
            'image_url' => $this->resolveImageUrl($article->featured_image),
            'category' => $article->category ? [
                'id' => $article->category->id,
                'name' => $article->category->name,
                'slug' => $article->category->slug,
            ] : null,
            'created_at' => $article->created_at,
        ];
    }

    protected function resolveImageUrl(?string $image)
    {
        if (! $image) {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        if (Str::startsWith($image, '/')) {
            return url($image);
        }

        return url('/storage/'.$image);
    }
}
