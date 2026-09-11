<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            "clint_id"      =>   "required",
            "amount"        =>  "required",
            "bank"          =>  "required",
        ];
    }

    public function messages(): array {
        return [
            "clint_id.required"     =>  "برجاء ادخال المرسل",
            "amount.required"       =>  "برجاء ادخال المبلغ",
            "bank.required"         =>  "برجاء ادخال البنك",
        ];
    }

}
