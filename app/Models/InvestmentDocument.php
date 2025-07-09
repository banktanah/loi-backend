<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentDocument extends Model
{
    /**
     * Nama tabel di database.
     */
    protected $table = 'investment_documents';

    /**
     * Atribut yang bisa diisi secara massal.
     */
    protected $fillable = [
        'investment_id',
        'tipe_dokumen',
        'nama_file_asli',
        'dokumen_url',
        'ukuran_file',
    ];

    /**
     * Relasi: Satu Dokumen dimiliki oleh satu Investment.
     * THE FIX: Sesuaikan foreign key dan owner key pada relasi.
     */
    public function investment()
    {
        // Foreign key di tabel ini adalah 'investment_id'.
        // Owner key (primary key) di tabel 'investments' adalah 'investment_id'.
        return $this->belongsTo(Investment::class, 'investment_id', 'investment_id');
    }
}