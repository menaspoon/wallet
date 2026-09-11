<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transactions;

class Clint extends Model {
    use HasFactory;
    protected $fillable = [
        "name",
        "internal_referance",
    ];



    public function transactions() {
        return $this->hasMany(Transactions::class);
    }

}
