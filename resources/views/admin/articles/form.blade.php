@extends('admin.layout')
@section('page-title', isset($article) ? 'Edit Artikel' : 'Tulis Artikel Baru')

@section('content')
<style>
    .form-card { background:white; border-radius:16px; padding:32px; box-shadow:0 1px 8px rgba(0,0,0,0.07); }
    .form-grid { display:grid; grid-template-columns:1fr 340px; gap:24px; }
    .form-group { margin-bottom:20px; }
    label { display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em; }
    input[type=text], input[type=file], textarea, select {
        width:100%; padding:10px 14px; border:1.5px solid #e2e8f0;
        border-radius:10px; font-size:13px; font-family:'Inter',sans-serif;
        transition:border 0.2s; background:white; color:#1e293b;
    }
    input:focus, textarea:focus, select:focus { outline:none; border-color:#1A3365; box-shadow:0 0 0 3px rgba(26,51,101,0.08); }
    textarea { resize:vertical; line-height:1.7; }
    .toggle-wrap { display:flex; align-items:center; gap:12px; }
    .toggle { position:relative; width:44px; height:24px; }
    .toggle input { opacity:0; width:0; height:0; }
    .slider {
        position:absolute; inset:0; background:#e2e8f0; border-radius:99px;
        cursor:pointer; transition:0.2s;
    }
    .slider:before {
        content:''; position:absolute; height:18px; width:18px;
        left:3px; bottom:3px; background:white; border-radius:50%; transition:0.2s;
        box-shadow:0 1px 4px rgba(0,0,0,0.15);
    }
    input:checked + .slider { background:#FFBB00; }
    input:checked + .slider:before { transform:translateX(20px); }
    .cover-preview { width:100%; aspect-ratio:16/9; object-fit:cover; border-radius:10px; margin-top:8px; display:block; }
    .btn-back { display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; margin-bottom:20px; }
    .btn-back:hover { color:#1A3365; }
    .btn-save {
        width:100%; padding:13px; background:#FFBB00; color:#1A3365;
        font-weight:700; font-size:14px; border:none; border-radius:12px;
        cursor:pointer; transition:all 0.2s; margin-top:8px;
    }
    .btn-save:hover { background:#FFD000; transform:translateY(-1px); }
    .section-title { font-size:13px; font-weight:700; color:#1A3365; margin-bottom:16px; padding-bottom:8px; border-bottom:2px solid #FFBB00; display:inline-block; }
    .hint { font-size:11px; color:#94a3b8; margin-top:4px; }
    .error-msg { color:#dc2626; font-size:11px; margin-top:4px; }
</style>

<a href="{{ route('admin.articles.index') }}" class="btn-back">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
</a>

<div class="form-card">
    <h1 style="font-size:20px; font-weight:800; color:#1A3365; margin-bottom:24px;">
        {{ isset($article) ? '✏️ Edit Artikel' : '📝 Tulis Artikel Baru' }}
    </h1>

    <form action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @isset($article) @method('PUT') @endisset

        <div class="form-grid">
            <!-- Left: Konten -->
            <div>
                <div class="section-title">Konten Artikel</div>

                <div class="form-group">
                    <label>Judul Artikel *</label>
                    <input type="text" name="title" value="{{ old('title', $article->title ?? '') }}"
                           placeholder="Tulis judul artikel yang menarik..." required>
                    @error('title') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Ringkasan (Excerpt)</label>
                    <textarea name="excerpt" rows="2"
                              placeholder="Ringkasan singkat artikel (tampil di halaman utama)...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                    <div class="hint">Maksimal 500 karakter. Tampil sebagai preview di landing page.</div>
                    @error('excerpt') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Isi Artikel *</label>
                    <textarea name="body" rows="16" id="articleBody"
                              placeholder="Tulis isi artikel lengkap di sini...
&#10;Kamu bisa pakai format HTML sederhana, contoh:
&#10;&lt;b&gt;Teks tebal&lt;/b&gt;
&#10;&lt;br&gt; untuk baris baru
&#10;&lt;ul&gt;&lt;li&gt;Poin pertama&lt;/li&gt;&lt;/ul&gt; untuk daftar"
                              required>{{ old('body', $article->body ?? '') }}</textarea>
                    @error('body') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Right: Setting -->
            <div>
                <div class="section-title">Pengaturan</div>

                <!-- Publish toggle -->
                <div class="form-group">
                    <label>Status Publikasi</label>
                    <div class="toggle-wrap">
                        <label class="toggle">
                            <input type="checkbox" name="is_published" value="1"
                                   {{ old('is_published', isset($article) ? $article->is_published : false) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span style="font-size:13px; font-weight:500;">Publish sekarang</span>
                    </div>
                    <div class="hint">Artikel hanya tampil di website jika statusnya Published.</div>
                </div>

                <!-- Kategori -->
                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $article->category ?? 'Berita') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Penulis -->
                <div class="form-group">
                    <label>Nama Penulis</label>
                    <input type="text" name="author_name"
                           value="{{ old('author_name', $article->author_name ?? 'Admin Tax Center') }}"
                           placeholder="Admin Tax Center">
                </div>

                <!-- Cover Image -->
                <div class="form-group">
                    <label>Foto Cover</label>
                    <input type="file" name="cover_image" accept="image/*"
                           onchange="previewImage(this)">
                    <div class="hint">JPG/PNG/WebP, maks 3MB. Rasio 16:9 direkomendasikan.</div>
                    @error('cover_image') <span class="error-msg">{{ $message }}</span> @enderror

                    @isset($article)
                        @if($article->cover_image)
                            <img src="{{ asset('storage/'.$article->cover_image) }}"
                                 alt="Cover" class="cover-preview" id="coverPreview">
                        @else
                            <img id="coverPreview" class="cover-preview" style="display:none;">
                        @endif
                    @else
                        <img id="coverPreview" class="cover-preview" style="display:none;">
                    @endisset
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    {{ isset($article) ? 'Simpan Perubahan' : 'Terbitkan Artikel' }}
                </button>

                @isset($article)
                <a href="{{ route('artikel.show', $article->slug) }}" target="_blank"
                   style="display:block; text-align:center; margin-top:12px; font-size:12px; color:#64748b; text-decoration:none;">
                    <i class="fas fa-external-link-alt"></i> Lihat di website
                </a>
                @endisset
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('coverPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
