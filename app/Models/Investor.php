<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    protected $table = 'investor';

    protected $primaryKey = 'investor_id';

    public $incrementing = false;
    protected $keyType = 'string';

    // public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'investor_id', 
        'investor_type_id', 
        'name', 
        'email', 
        'phone', 
        'address',
        'province',
        'province_id',
        'regency',
        'regency_id',
        'district',
        'district_id',
        'village',
        'village_id',
        'approved_at',
        'approved_by',
        'company_profile'
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
