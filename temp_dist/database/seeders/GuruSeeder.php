<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gurus = [
            [
                'email' => 'guru1@taxcenter.local',
                'nama' => 'Dr. Ahmad Wijaya',
                'mapel' => ['Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B', 'Pajak Penghasilan (PPh) Orang Pribadi'],
                'status' => 'active',
                'catatan' => 'Dosen Tetap - KUP & PPh Orang Pribadi',
            ],
            [
                'email' => 'guru2@taxcenter.local',
                'nama' => 'Ibu Siti Nurhaliza',
                'mapel' => ['Pajak Pertambahan Nilai (PPN) dan PPnBM A & B', 'Pajak Pemotongan dan Pemungutan (PPh Pasal 21)'],
                'status' => 'active',
                'catatan' => 'Dosen Tetap - PPN & PPh 21',
            ],
            [
                'email' => 'guru3@taxcenter.local',
                'nama' => 'Bapak Rudi Hermawan',
                'mapel' => ['Pajak Penghasilan (PPh) Badan', 'Akuntansi Perpajakan'],
                'status' => 'active',
                'catatan' => 'Dosen Tetap - PPh Badan & Akuntansi Pajak',
            ],
            [
                'email' => 'guru4@taxcenter.local',
                'nama' => 'Ibu Mira Destiana',
                'mapel' => ['Pemeriksaan dan Penyidikan Pajak', 'Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)'],
                'status' => 'active',
                'catatan' => 'Dosen Tetap - Pemeriksaan & e-SPT',
            ],
            [
                'email' => 'guru5@taxcenter.local',
                'nama' => 'Bapak Hendra Kurniawan',
                'mapel' => ['Tax Planning (Perencanaan Pajak)', 'Ujian Kelulusan / Komprehensif Brevet'],
                'status' => 'active',
                'catatan' => 'Dosen Tetap - Tax Planning & Ujian',
            ],
            [
                'email' => 'demo@taxcenter.local',
                'nama' => 'Demo Teacher',
                'mapel' => [
                    'Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B',
                    'Pajak Penghasilan (PPh) Orang Pribadi',
                    'Pajak Pertambahan Nilai (PPN) dan PPnBM A & B'
                ],
                'status' => 'active',
                'catatan' => 'Akun Demo untuk Testing',
            ],
        ];

        foreach ($gurus as $guru) {
            Guru::updateOrCreate(
                ['email' => $guru['email']],
                $guru
            );
        }

        $this->command->info('GuruSeeder completed: ' . count($gurus) . ' teachers seeded.');
    }
}
