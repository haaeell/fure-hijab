<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Setting;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('kategori');

        $articles = Article::published()
            ->when($category, fn($q) => $q->where('category', $category))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $featured = Article::published()
            ->orderByDesc('view_count')
            ->first();

        return view('user.articles.index', compact('articles', 'featured', 'category'));
    }

    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();
        $article->incrementView();

        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $storeName = Setting::getValue('store_name', 'FURE');

        return view('user.articles.show', compact('article', 'related', 'storeName'));
    }
}
