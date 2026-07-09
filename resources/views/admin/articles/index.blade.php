@extends('admin.layout')
@section('page-title', 'Kelola Artikel')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
    .page-header h1 { font-size:22px; font-weight:800; color:#1A3365; }
    .btn-primary {
        display:inline-flex; align-items:center; gap:8px;
        background:#FFBB00; color:#1A3365; font-weight:700; font-size:13px;
        padding:10px 20px; border-radius:10px; text-decoration:none;
        transition:all 0.2s; border:none; cursor:pointer;
    }
    .btn-primary:hover { background:#FFD000; transform:translateY(-1px); }
    .card { background:white; border-radius:16px; overflow:hidden; box-shadow:0 1px 8px rgba(0,0,0,0.07); }
    table { width:100%; border-collapse:collapse; }
    thead { background:#f8fafc; }
    th { padding:12px 16px; text-align:left; font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.1em; }
    td { padding:14px 16px; border-top:1px solid #f1f5f9; font-size:13px; vertical-align:middle; }
    tr:hover td { background:#fafbff; }
    .badge { display:inline-block; padding:3px 10px; border-radius:99px; font-size:10px; font-weight:700; }
    .badge-published { background:#d1fae5; color:#065f46; }
    .badge-draft { background:#f1f5f9; color:#94a3b8; }
    .badge-cat { background:#eff6ff; color:#1d4ed8; }
    .action-btns { display:flex; gap:6px; }
    .btn-sm {
        display:inline-flex; align-items:center; gap:5px;
        padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600;
        text-decoration:none; border:none; cursor:pointer; transition:all 0.2s;
    }
    .btn-edit { background:#eff6ff; color:#1d4ed8; }
    .btn-edit:hover { background:#dbeafe; }
    .btn-delete { background:#fee2e2; color:#dc2626; }
    .btn-delete:hover { background:#fecaca; }
    .btn-toggle-on { background:#d1fae5; color:#065f46; }
    .btn-toggle-on:hover { background:#a7f3d0; }
    .btn-toggle-off { background:#fef3c7; color:#92400e; }
    .btn-toggle-off:hover { background:#fde68a; }
    .cover-thumb { width:52px; height:36px; object-fit:cover; border-radius:6px; }
    .empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
    .empty-state i { font-size:40px; margin-bottom:12px; display:block; }
</style>

<div class="page-header" style="flex-wrap: wrap; gap: 15px;">
    <div>
        <h1>📰 Kelola Artikel</h1>
        <p style="color:#64748b; font-size:13px; margin-top:2px;">Total: {{ $articles->total() }} artikel</p>
    </div>
    <a href="{{ route('admin.articles.create') }}" class="btn-primary" style="white-space:nowrap;">
        <i class="fas fa-plus"></i> Tulis Artikel Baru
    </a>
</div>

<div class="card" style="overflow-x: auto; min-width: 100%;">
    @if($articles->count())
    <table>
        <thead>
            <tr>
                <th>Cover</th>
                <th>Judul Artikel</th>
                <th>Kategori</th>
                <th>Penulis</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>
                <td>
                    @if($article->cover_image)
                        <img src="{{ asset('storage/'.$article->cover_image) }}" alt="" class="cover-thumb">
                    @else
                        <div style="width:52px;height:36px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-image" style="color:#cbd5e1;font-size:14px;"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <div style="font-weight:600; color:#1e293b; max-width:280px;">{{ Str::limit($article->title, 55) }}</div>
                    <div style="color:#94a3b8; font-size:11px; margin-top:2px;">/artikel/{{ $article->slug }}</div>
                </td>
                <td><span class="badge badge-cat">{{ $article->category }}</span></td>
                <td style="color:#64748b;">{{ $article->author_name }}</td>
                <td>
                    @if($article->is_published)
                        <span class="badge badge-published"><i class="fas fa-circle" style="font-size:6px;"></i> Published</span>
                    @else
                        <span class="badge badge-draft">Draft</span>
                    @endif
                </td>
                <td style="color:#64748b; white-space:nowrap;">
                    {{ $article->published_at ? $article->published_at->format('d M Y') : '-' }}
                </td>
                <td>
                    <div class="action-btns">
                        <!-- Toggle publish -->
                        <form action="{{ route('admin.articles.toggle', $article) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-sm {{ $article->is_published ? 'btn-toggle-on' : 'btn-toggle-off' }}"
                                title="{{ $article->is_published ? 'Jadikan Draft' : 'Publish' }}">
                                <i class="fas {{ $article->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </button>
                        </form>
                        <!-- Edit -->
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn-sm btn-edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <!-- Hapus -->
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                              onsubmit="return confirm('Yakin hapus artikel ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:16px 20px; border-top:1px solid #f1f5f9;">
        {{ $articles->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <p style="font-weight:600; color:#475569;">Belum ada artikel</p>
        <p style="font-size:12px; margin-top:4px;">Mulai tulis artikel pertama kamu!</p>
        <a href="{{ route('admin.articles.create') }}" class="btn-primary" style="margin-top:16px; display:inline-flex;">
            <i class="fas fa-plus"></i> Tulis Sekarang
        </a>
    </div>
    @endif
</div>
@endsection
