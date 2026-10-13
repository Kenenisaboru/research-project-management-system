<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
        'ip_address',
        'user_agent',
        'request_method',
        'request_url',
        'diff_json',
    ];

    protected $casts = [
        'diff_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

