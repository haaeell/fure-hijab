<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderByDesc('id')->get();
        $categories = Article::$categories;
        return view('articles.index', compact('articles', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'category'         => 'required|string',
            'author'           => 'nullable|string|max:100',
            'tags'             => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords'    => 'nullable|string|max:255',
            'read_time'        => 'nullable|integer|min:1|max:60',
            'is_published'     => 'nullable',
        ]);

        $data['slug']         = $this->uniqueSlug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['tags']         = $this->parseTags($request->input('tags'));

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->back()->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'category'         => 'required|string',
            'author'           => 'nullable|string|max:100',
            'tags'             => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords'    => 'nullable|string|max:255',
            'read_time'        => 'nullable|integer|min:1|max:60',
            'is_published'     => 'nullable',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && !$article->is_published) {
            $data['published_at'] = now();
        }
        $data['tags'] = $this->parseTags($request->input('tags'));

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->back()->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete();

        return redirect()->back()->with('success', 'Artikel berhasil dihapus.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096']);
        $path = $request->file('image')->store('articles/content', 'public');
        return response()->json(['url' => asset('storage/' . $path)]);
    }

    public function togglePublish(Article $article)
    {
        $article->update([
            'is_published' => !$article->is_published,
            'published_at' => !$article->is_published ? now() : $article->published_at,
        ]);

        return response()->json(['is_published' => $article->is_published]);
    }

    private function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    private function parseTags(?string $raw): ?array
    {
        if (!$raw) return null;
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
