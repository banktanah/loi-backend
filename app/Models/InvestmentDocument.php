<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentDocument extends Model
{
    protected $table = 'investment_document';

    protected $primaryKey = 'investment_document_id';

    // public $incrementing = false;
    // protected $keyType = 'string';

    // public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'investment_id',
        'document_type',
        'filename',
        'file'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];
}
