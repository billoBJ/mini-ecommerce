<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    // Only created_at exists on this table (it's an append-only log —
    // rows are never updated), and it's filled by the DB's own
    // DEFAULT CURRENT_TIMESTAMP, not by Eloquent.
    public $timestamps = false;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'notes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
