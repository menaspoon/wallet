<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Transactions;
use Illuminate\Support\Str;

class TransferControler extends Controller {



    public function  store(StoreTransferRequest $request ) {


        $transactions = Transactions::create([
            'clint_id' => $request->clint_id,
            'amount'    => $request->amount,
            'bank'     => $request->bank,
            'reference' => 'TRF-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'status' => 'pending',
        ]);

        return response()->json([ 
            "success" => true,
            "massege" => "تم انشاء التحويل بنجاح",
            "data"    => $transactions,
        ]);

        

        
    }   




    
}
