<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // Public: daftar semua artikel
    public function index(Request $request)
    {
        $category = $request->query('kategori');
        $search   = $request->query('cari');

        $query = Article::published()->latest('published_at');

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $articles   = $query->paginate(9)->withQueryString();
        $categories = ['Berita', 'Edukasi', 'Kebijakan', 'Pengumuman'];

        return view('artikel.index', compact('articles', 'categories', 'category', 'search'));
    }

    // Public: detail artikel
    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        // Artikel terkait (kategori sama, max 4 untuk sidebar)
        $related = Article::published()
                        ->where('category', $article->category)
                        ->where('id', '!=', $article->id)
                        ->latest('published_at')
                        ->take(4)
                        ->get();

        // Artikel lainnya jika related kurang dari 4
        if ($related->count() < 4) {
            $moreIds = $related->pluck('id')->push($article->id)->toArray();
            $more = Article::published()
                ->whereNotIn('id', $moreIds)
                ->latest('published_at')
                ->take(4 - $related->count())
                ->get();
            $related = $related->merge($more);
        }

        return view('artikel.show', compact('article', 'related'));
    }
}
