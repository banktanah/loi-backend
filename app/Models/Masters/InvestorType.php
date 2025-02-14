<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Model;

class InvestorType extends Model
{
    protected $table = 'master_investor_type';

    protected $primaryKey = 'investor_type_id';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
