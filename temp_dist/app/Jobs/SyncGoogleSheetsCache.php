<?php
 
namespace App\Jobs;
 
use App\Services\GoogleSheetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
 
class SyncGoogleSheetsCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
    /**
     * Jumlah detik pekerjaan dapat berjalan sebelum time out.
     *
     * @var int
     */
    public $timeout = 120;
 
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }
 
    /**
     * Execute the job.
     */
    public function handle(GoogleSheetService $gs): void
    {
        Log::info("[SyncGoogleSheetsCache] Memulai sinkronisasi cache di background...");
        
        try {
            $gs->clearAllCache();
            Log::info("[SyncGoogleSheetsCache] Sinkronisasi cache selesai dengan sukses.");
        } catch (\Exception $e) {
            Log::error("[SyncGoogleSheetsCache] Gagal menyinkronkan cache: " . $e->getMessage());
            throw $e;
        }
    }
}
