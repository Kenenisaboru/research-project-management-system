<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'recipient_id',
        'awarded_by',
        'award_name',
        'award_amount',
        'citation',
        'awarded_at',
    ];

    protected $casts = [
        'award_amount' => 'decimal:2',
        'awarded_at'   => 'date',
    ];

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function awardedBy()
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
}