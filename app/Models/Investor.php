<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'investor';
    
    /**
     * The primary key for the model.
     * @var string
     */
    protected $primaryKey = 'investor_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',          // Foreign key to the users table
        'investor_id',      // NIB/KTP (Primary Key)
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
     * Get the user that owns the investor profile.
     */
    public function user()
    {
        // This Investor profile belongs to one User.
        // The foreign key on this table is 'user_id'.
        // The owner's key on the 'users' table is 'id'.
        return $this->belongsTo('User', 'user_id', 'id');
    }
}