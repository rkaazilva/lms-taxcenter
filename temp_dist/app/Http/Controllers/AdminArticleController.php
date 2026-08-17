<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminArticleController extends Controller
{
    // Daftar semua artikel
    public function index()
    {
        $articles = Article::latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    // Form tambah artikel
    public function create()
    {
        $categories = ['Berita', 'Edukasi', 'Kebijakan', 'Pengumuman'];
        return view('admin.articles.form', compact('categories'));
    }

    // Simpan artikel baru
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string',
            'excerpt'     => 'nullable|string|max:500',
            'body'        => 'required|string',
            'author_name' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->only(['title', 'category', 'excerpt', 'body', 'author_name']);
        $data['slug']        = Str::slug($request->title) . '-' . time();
        $data['author_name'] = $request->author_name ?: 'Admin Tax Center';
        $data['is_published'] = $request->has('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')
                         ->with('success', 'Artikel berhasil ditambahkan!');
    }

    // Form edit artikel
    public function edit(Article $article)
    {
        $categories = ['Berita', 'Edukasi', 'Kebijakan', 'Pengumuman'];
        return view('admin.articles.form', compact('article', 'categories'));
    }

    // Update artikel
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string',
            'excerpt'     => 'nullable|string|max:500',
            'body'        => 'required|string',
            'author_name' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data = $request->only(['title', 'category', 'excerpt', 'body', 'author_name']);
        $data['author_name'] = $request->author_name ?: 'Admin Tax Center';
        $was_published = $article->is_published;
        $data['is_published'] = $request->has('is_published');

        if ($data['is_published'] && !$was_published) {
            $data['published_at'] = now();
        } elseif (!$data['is_published']) {
            $data['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
                         ->with('success', 'Artikel berhasil diperbarui!');
    }

    // Hapus artikel
    public function destroy(Article $article)
    {
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }
        $article->delete();

        return redirect()->route('admin.articles.index')
                         ->with('success', 'Artikel berhasil dihapus.');
    }

    // Toggle publish/draft
    public function togglePublish(Article $article)
    {
        $article->is_published = !$article->is_published;
        $article->published_at = $article->is_published ? now() : null;
        $article->save();

        $status = $article->is_published ? 'dipublish' : 'dijadikan draft';
        return back()->with('success', "Artikel berhasil {$status}!");
    }
}
