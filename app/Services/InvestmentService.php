<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentDocument;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Notifications\InvestmentSubmitted;

class InvestmentService
{
    /**
     * Menyimpan data minat investasi baru beserta dokumen-dokumennya.
     *
     * @param array $data Data investasi dari request.
     * @param array $files File-file yang di-upload.
     * @return Investment
     * @throws Exception
     */
     public function createInvestment(array $data, array $files): Investment
    {
        $user = Auth::user();
        if (!$user) {
            throw new Exception("Authentication required.", 401);
        }

        // Kita gunakan DB::transaction agar bisa menangkap objek investment
        // dan mengirimkannya ke notifikasi setelah commit berhasil.
        $investment = null;
        DB::transaction(function () use ($user, $data, $files, &$investment) {
            $investmentData = $data;
            $investmentData['user_id'] = $user->id;
            $investmentData['status'] = 'Diajukan';
            $investmentData['tanggal_pengajuan'] = now();

            $investment = Investment::create($investmentData);

            foreach ($files as $tipe_dokumen => $file) {
                if (!is_file($file)) continue;
                $path = $file->store('investments/' . $investment->investment_id, 'public');
                InvestmentDocument::create([
                    'investment_id' => $investment->investment_id,
                    'tipe_dokumen' => $tipe_dokumen,
                    'nama_file_asli' => $file->getClientOriginalName(),
                    'dokumen_url' => Storage::disk('public')->url($path),
                    'ukuran_file' => $file->getSize(),
                ]);
            }
        });

        // Jika transaksi gagal, $investment akan tetap null dan error akan dilempar
        if (!$investment) {
            throw new Exception("Failed to create investment record.", 500);
        }

        // THE NEW STEP: Kirim notifikasi setelah semua data berhasil disimpan
        try {
            // Eager load relasi investor untuk digunakan di email (mengambil nama)
            $user->load('investor'); 
            $user->notify(new InvestmentSubmitted($investment, $user));
            Log::info("Investment submission email sent for investment ID: " . $investment->investment_id);
        } catch (Exception $e) {
            // Jika pengiriman email gagal, jangan gagalkan seluruh proses.
            // Cukup catat errornya.
            Log::error("Failed to send investment submission email for investment ID: {$investment->investment_id}. Error: " . $e->getMessage());
        }
        
        // Kembalikan data investment beserta dokumennya
        return $investment->load('documents');
    }

    /**
     * Mengambil daftar investasi milik user yang sedang login.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserInvestments()
    {
        $user_id = Auth::id();
        return Investment::where('user_id', $user_id)
            ->with('documents') // Eager load documents
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
    }

    /**
     * Mengambil detail satu investasi.
     *
     * @param int $id
     * @return Investment
     * @throws Exception
     */
    public function getInvestmentDetail(int $id): Investment
    {
        $investment = Investment::with('documents')->find($id);

        if (!$investment) {
            throw new Exception("Investment not found.", 404);
        }

        // Otorisasi: Pastikan user hanya bisa melihat investasinya sendiri (kecuali admin)
        if (Auth::user()->role !== 'admin' && $investment->user_id !== Auth::id()) {
            throw new Exception("You are not authorized to view this investment.", 403);
        }

        return $investment;
    }
}