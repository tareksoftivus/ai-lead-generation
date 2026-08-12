<?php

namespace App\Modules\Contacts\Http\Requests;

use App\Rules\RecaptchaV2Valid;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'terms_accepted' => 'accepted',
            'g-recaptcha-response' => [new RecaptchaV2Valid],
        ];
    }
}
