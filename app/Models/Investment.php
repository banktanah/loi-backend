<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    /**
     * Nama tabel di database.
     */
    protected $table = 'investments';

    /**
     * Primary key untuk model ini.
     * THE FIX: Beritahu Eloquent nama primary key yang baru.
     */
    protected $primaryKey = 'investment_id';

    /**
     * Apakah primary key-nya auto-incrementing? (Ya)
     */
    public $incrementing = true;

    /**
     * Tipe data dari primary key. (Untuk casting)
     */
    protected $keyType = 'int';

    /**
     * Atribut yang bisa diisi secara massal.
     * Kita tidak perlu memasukkan 'investment_id' di sini karena auto-increment.
     */
    protected $fillable = [
        'user_id',
        'perolehan_id',
        'site_name',
        'tujuan_pemanfaatan',
        'deskripsi_rencana_proyek',
        'skema_pemanfaatan',
        'status',
        'catatan_verifikator',
        'tanggal_pengajuan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
    ];

    /**
     * Relasi: Satu Investment dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Relasi: Satu Investment bisa memiliki banyak Dokumen.
     * THE FIX: Sesuaikan foreign key dan local key pada relasi.
     */
    public function documents()
    {
        // Foreign key di tabel 'investment_documents' adalah 'investment_id'.
        // Local key (primary key) di tabel 'investments' ini juga adalah 'investment_id'.
        return $this->hasMany(InvestmentDocument::class, 'investment_id', 'investment_id');
    }
}