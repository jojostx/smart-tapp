<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionModel extends Model
{
    use HasFactory;

    protected $table = 'sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
    ];

    /**
     * The tenants that owns this session.
     *
     * @var array<int, string>
     */
     public function tenant()
     {
         return $this->belongsTo(config('tenancy.tenant_model'));
     }
}
