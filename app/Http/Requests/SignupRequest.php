<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SignupRequest extends StoreAccountRequestRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $old_rules = parent::rules();
        $old_rules['referral_input'] = 'nullable|string|max:50|exists:users,referral_code';
        $old_rules['role'] = 'required|in:regular';
        $old_rules['resubmitted_from_id'] = 'nullable|integer|exists:account_requests,id';
        return $old_rules;
    }
}
