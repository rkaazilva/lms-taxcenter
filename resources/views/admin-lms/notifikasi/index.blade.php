@extends('layouts.main')

@section('title', 'Kirim Notifikasi Broadcast')
@section('page_title', 'Broadcast Notifikasi')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .hover-scale {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-scale:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Header Section with Purple/Fuchsia Gradient Banner -->
    <div class="bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-3xl p-6 md:p-8 mb-8 shadow-lg text-white relative overflow-hidden border border-violet-400/20 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-violet-400 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-white/20 backdrop-blur-md text-white border border-white/10">
                    <i class="fas fa-bullhorn"></i>
                </span>
                <span class="text-xs font-bold text-violet-100 uppercase tracking-widest">Notification Centre</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold leading-tight font-sans">Kirim Pengumuman & Broadcast</h2>
            <p class="text-violet-100 text-xs md:text-sm mt-1">Kirim pesan sistem in-app ke portal LMS pengguna atau teruskan pesan ke WhatsApp Group secara massal.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
        <div>
            <p class="text-rose-900 font-semibold text-xs">Terjadi kesalahan input:</p>
            <ul class="text-rose-700 text-[11px] list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <p class="text-emerald-800 font-semibold text-xs">Berhasil! {{ session('success') }}</p>
    </div>
    @endif

    @if(session('warning'))
    <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-100 flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
        <div>
            <p class="text-amber-900 font-semibold text-xs">Peringatan!</p>
            <p class="text-amber-800 text-[11px] mt-0.5">{{ session('warning') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3">
        <i class="fas fa-times-circle text-rose-500"></i>
        <p class="text-rose-800 font-semibold text-xs">Gagal! {{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Section -->
        <div class="lg:col-span-1">
            <div class="glass-card p-6 md:p-8 rounded-3xl shadow-sm">
                <h3 class="font-bold text-md text-violet-950 mb-4 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-violet-600"></i> Kirim Notifikasi Baru
                </h3>
                
                <form action="{{ route('admin-lms.notifikasi.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Target -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Target Penerima</label>
                        <select name="target" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-150" required>
                            <option value="ALL">Semua Pengguna</option>
                            <option value="SISWA">Siswa Sahaja</option>
                            <option value="GURU">Guru/Tutor Sahaja</option>
                        </select>
                    </div>

                    <!-- Judul -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Judul Notifikasi</label>
                        <input type="text" name="title" placeholder="Tuliskan judul singkat..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-150" required>
                    </div>

                    <!-- Isi -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Isi Pengumuman</label>
                        <textarea name="message" rows="5" placeholder="Tuliskan isi pengumuman lengkap..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-150" required></textarea>
                    </div>

                    <!-- Tautan Opsional -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tautan Opsional (Link)</label>
                        <input type="text" name="link" placeholder="Contoh: /siswa/dashboard" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-150">
                        <span class="text-[9px] text-gray-400 block mt-1">Gunakan path relatif portal LMS (contoh: `/siswa/dashboard`) atau URL penuh.</span>
                    </div>

                    <!-- Send WA Checkbox -->
                    <div class="p-3.5 rounded-2xl bg-violet-50/50 border border-violet-100/50 flex items-start gap-3">
                        <input type="checkbox" name="send_wa" id="send_wa" class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-gray-300 mt-0.5" value="1">
                        <div>
                            <label for="send_wa" class="block text-[11px] font-bold text-violet-950 cursor-pointer">Kirim ke WhatsApp Group</label>
                            <p class="text-[9px] text-violet-600/75 mt-0.5 leading-relaxed">Kirim pengumuman ini ke WhatsApp grup kelas via Fonnte API secara langsung.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-700 hover:to-fuchsia-700 text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md shadow-violet-500/10 hover:shadow-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane text-[10px]"></i>
                        <span>Kirim Broadcast</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- History Section -->
        <div class="lg:col-span-2">
            <div class="glass-card p-6 md:p-8 rounded-3xl shadow-sm">
                <h3 class="font-bold text-md text-violet-950 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-history text-violet-600"></i> Riwayat Pengiriman
                    </span>
                    <span class="text-[10px] text-gray-400 font-normal">Menampilkan list pesan sistem</span>
                </h3>

                <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50/75 text-gray-500 font-bold border-b border-slate-100">
                                <th class="p-4 w-[15%]">Target</th>
                                <th class="p-4 w-[25%]">Judul</th>
                                <th class="p-4 w-[35%]">Isi Notifikasi</th>
                                <th class="p-4 w-[10%]">Link</th>
                                <th class="p-4 w-[15%]">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($notifications as $notif)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 whitespace-nowrap">
                                    @if($notif->email === 'ALL')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-purple-50 text-purple-700 text-[9px] font-semibold border border-purple-100">Semua</span>
                                    @elseif($notif->email === 'SISWA')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-[9px] font-semibold border border-blue-100">Siswa</span>
                                    @elseif($notif->email === 'GURU' || $notif->email === 'TUTOR')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[9px] font-semibold border border-emerald-100">Guru</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 text-gray-700 text-[9px] font-semibold border border-gray-100 truncate max-w-[80px]" title="{{ $notif->email }}">{{ $notif->email }}</span>
                                    @endif
                                </td>
                                <td class="p-4 font-bold text-slate-800">
                                    <div class="truncate max-w-[150px]" title="{{ $notif->title }}">{{ $notif->title }}</div>
                                </td>
                                <td class="p-4 text-slate-500 leading-relaxed font-medium">
                                    <div class="line-clamp-2 max-w-[220px]" title="{{ $notif->message }}">{{ $notif->message }}</div>
                                </td>
                                <td class="p-4">
                                    @if($notif->link)
                                        <a href="{{ $notif->link }}" target="_blank" class="inline-flex items-center gap-1 text-violet-600 hover:text-violet-800 transition font-bold" title="{{ $notif->link }}">
                                            <span>Buka</span>
                                            <i class="fas fa-external-link-alt text-[8px]"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="p-4 whitespace-nowrap text-slate-400 font-medium">
                                    {{ $notif->created_at ? $notif->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 italic">
                                    <i class="fas fa-bell-slash text-2xl mb-2 block"></i> Belum ada notifikasi terkirim.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
