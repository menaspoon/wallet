<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transactions;

class Webhooks extends Model {
    protected $fillable = [
        "clint_id",
        "bank",
        "payload",
        "reference",
        "amount",
        "transaction_date",
        "metadata",
        "notes",
        "status",
        "error_message",
        "processed_at",
    ];


    public function transactions() {
        return $this->hasMany(Transactions::class);
    }

}
