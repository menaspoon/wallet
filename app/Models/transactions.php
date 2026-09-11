<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clint;
use App\Models\Webhooks;

class Transactions extends Model {

    protected $fillable = [
        "clint_id",
        "webhook_id",
        "payload",
        "reference",
        "amount",
        "bank",
        "transaction_date",
        "metadata",
    ];


    public function clint () {
        return $this->belongsTo(Clint::class);
    }


    public function Webhook () {
        return $this->belongsTo(Webhooks::class);
    }

}
