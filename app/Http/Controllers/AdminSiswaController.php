<?php

namespace App\Http\Controllers;

use App\Models\LmsUser;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminSiswaController extends Controller
{
    protected $gs;

    public function __construct(GoogleSheetService $gs)
    {
        $this->gs = $gs;
    }

    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $query = LmsUser::where(function($q) {
            $q->where('role', 'SISWA')
              ->orWhereNull('role')
              ->orWhere('role', '');
        });

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(kelas) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(telepon) LIKE ?', ["%{$search}%"]);
            });
        }

        $siswas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $totalSiswa = LmsUser::where(function($q) {
            $q->where('role', 'SISWA')->orWhereNull('role')->orWhere('role', '');
        })->count();

        $kelasCounts = LmsUser::where(function($q) {
            $q->where('role', 'SISWA')->orWhereNull('role')->orWhere('role', '');
        })->whereNotNull('kelas')->where('kelas', '!=', '')->distinct('kelas')->count('kelas');

        return view('admin-lms.siswa.index', compact('siswas', 'totalSiswa', 'kelasCounts'));
    }

    /**
     * Show form to create student
     */
    public function create()
    {
        return view('admin-lms.siswa.create');
    }

    /**
     * Store new student and auto-send credentials
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:255',
            'email'   => 'required|email|unique:lms_users,email',
            'kelas'   => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
        ], [
            'nama.required'  => 'Nama siswa wajib diisi.',
            'email.required' => 'Email siswa wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah terdaftar.',
        ]);

        // Generate strong random password (e.g. Tax7382!)
        $randomPass = 'Tax' . rand(1000, 9999) . '!';
        $hashedPass = Hash::make($randomPass);

        $user = LmsUser::create([
            'nama'     => trim($validated['nama']),
            'email'    => strtolower(trim($validated['email'])),
            'password' => $hashedPass,
            'role'     => 'SISWA',
            'kelas'    => $validated['kelas'] ?? 'Brevet Gelombang 1',
            'telepon'  => $validated['telepon'] ?? null,
        ]);

        // Send Email Credential Notification
        $emailSent = false;
        try {
            $loginUrl = route('login');
            $subject = "Pendaftaran Akun LMS Tax Center UIN Sunan Gunung Djati Bandung";
            $messageBody = "Halo {$user->nama},\n\n"
                . "Selamat! Akun Anda untuk LMS Tax Center UIN Sunan Gunung Djati Bandung telah berhasil dibuat.\n\n"
                . "Detail Akses Login Anda:\n"
                . "----------------------------------------\n"
                . "Email    : {$user->email}\n"
                . "Password : {$randomPass}\n"
                . "Kelas    : {$user->kelas}\n"
                . "Tautan   : {$loginUrl}\n"
                . "----------------------------------------\n\n"
                . "Silakan login dan perbarui password Anda di menu Pengaturan Akun.\n\n"
                . "Salam hangat,\nTim Tax Center UIN SGD Bandung";

            Mail::raw($messageBody, function($mail) use ($user, $subject) {
                $mail->to($user->email, $user->nama)
                     ->subject($subject);
            });
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error("[AdminSiswaController] Gagal kirim email akun: " . $e->getMessage());
        }

        // Send WhatsApp Credential Notification
        $waSent = false;
        if (!empty($user->telepon)) {
            $waMessage = "🎉 *PENDAFTARAN AKUN LMS TAX CENTER* 🎉\n\n"
                . "Halo *{$user->nama}*,\n"
                . "Akun LMS Tax Center UIN Sunan Gunung Djati Bandung Anda telah berhasil didaftarkan.\n\n"
                . "🔑 *Detail Login Anda:*\n"
                . "• *Email:* {$user->email}\n"
                . "• *Password:* `{$randomPass}`\n"
                . "• *Kelas:* {$user->kelas}\n"
                . "• *Login:* " . route('login') . "\n\n"
                . "Silakan login dan ubah password Anda di menu Pengaturan Akun. Terima kasih!";

            $waSent = $this->gs->sendWaDirect($user->telepon, $waMessage);
        }

        $deliveryStatus = [];
        if ($emailSent) $deliveryStatus[] = "Email Kredensial";
        if ($waSent) $deliveryStatus[] = "WhatsApp";

        $statusStr = count($deliveryStatus) > 0 
            ? " (Notifikasi terkirim via: " . implode(', ', $deliveryStatus) . ")"
            : " (Peringatan: Cek konfigurasi SMTP / WA).";

        return redirect()->route('admin-lms.siswa.index')->with('success', 
            "Siswa '{$user->nama}' berhasil didaftarkan! Password Sementara: [ {$randomPass} ]" . $statusStr
        );
    }

    /**
     * Reset password for a student and auto-resend notifications
     */
    public function resetPassword(Request $request, $id)
    {
        $user = LmsUser::findOrFail($id);

        $newPass = 'Tax' . rand(1000, 9999) . '!';
        $user->password = Hash::make($newPass);
        $user->save();

        // Re-send Email
        try {
            $loginUrl = route('login');
            $subject = "Pembaruan Password Akun LMS Tax Center";
            $messageBody = "Halo {$user->nama},\n\n"
                . "Password akun LMS Tax Center UIN SGD Anda telah di-reset oleh Admin.\n\n"
                . "Detail Akses Login Baru:\n"
                . "----------------------------------------\n"
                . "Email    : {$user->email}\n"
                . "Password Baru : {$newPass}\n"
                . "Tautan   : {$loginUrl}\n"
                . "----------------------------------------\n\n"
                . "Salam hangat,\nTim Tax Center UIN SGD Bandung";

            Mail::raw($messageBody, function($mail) use ($user, $subject) {
                $mail->to($user->email, $user->nama)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error("[AdminSiswaController] Reset password mail failed: " . $e->getMessage());
        }

        // Re-send WA
        if (!empty($user->telepon)) {
            $waMessage = "🔐 *PEMBARUAN PASSWORD LMS TAX CENTER* 🔐\n\n"
                . "Halo *{$user->nama}*,\n"
                . "Password akun LMS Anda telah di-reset oleh Admin.\n\n"
                . "🔑 *Password Baru Anda:* `{$newPass}`\n"
                . "• *Email:* {$user->email}\n"
                . "• *Login:* " . route('login');

            $this->gs->sendWaDirect($user->telepon, $waMessage);
        }

        return redirect()->route('admin-lms.siswa.index')->with('success', 
            "Password siswa '{$user->nama}' berhasil di-reset menjadi: [ {$newPass} ] dan telah dikirimkan ke email/WA siswa."
        );
    }

    /**
     * Delete a student account
     */
    public function destroy($id)
    {
        $user = LmsUser::findOrFail($id);
        $nama = $user->nama;
        $user->delete();

        return redirect()->route('admin-lms.siswa.index')->with('success', "Akun siswa '{$nama}' telah berhasil dihapus.");
    }
}
