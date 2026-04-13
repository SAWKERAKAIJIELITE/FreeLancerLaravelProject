<?php

namespace App\Http\Requests;

use App\Enums\SignupRequestStatus;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\Country;
use App\Models\Language;

class StoreAccountRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
        // return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:2|max:100|regex:/^[\pL\s\.\,\-\']+$/u',
            'middle_name' => 'nullable|string|min:2|max:100|regex:/^[\pL\s\.\,\-\']+$/u',
            'last_name' => 'required|string|min:2|max:100|regex:/^[\pL\s\.\,\-\']+$/u',
            'birthdate' => 'required|date|before:-10 years',
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('is_active', true),
            ],
            'language_id' => [
                'required',
                'integer',
                Rule::exists('languages', 'id')->where('is_active', true),
            ],
            'phone_country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('is_active', true),
            ],
            'phone' => 'required|string|numeric',
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                'unique:users,email',
                Rule::unique('account_requests', 'email')
                    ->where(fn($query) => $query->where('status', SignupRequestStatus::Pending)),
            ],
            'username' => [
                'required',
                'alpha_num',
                'min:3',
                'max:50',
                'unique:users,username',
                Rule::unique('account_requests', 'username')
                    ->where(fn($query) => $query->where('status', SignupRequestStatus::Pending)),
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'terms_accepted' => 'required|accepted',
            'role' => ["required", Rule::in(UserRole::values())],
            'referral_input' => 'required|string|max:50|exists:users,referral_code',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name may contain only letters, spaces, commas, dots, apostrophes, and hyphens.',
            'middle_name.regex' => 'Middle name may contain only letters, spaces, commas, dots, apostrophes, and hyphens.',
            'last_name.regex' => 'Last name may contain only letters, spaces, commas, dots, apostrophes, and hyphens.',
            'phone.regex' => 'Phone number must contain digits only.',
            'email.unique' => 'This email is already used by an existing account or a pending signup request.',
            'username.unique' => 'This username is already used by an existing account or a pending signup request.',
            'terms.accepted' => 'You must agree to the terms of use.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => $this->normalizeName($this->first_name),
            'middle_name' => $this->normalizeName($this->middle_name),
            'last_name' => $this->normalizeName($this->last_name),
            'email' => $this->email ? mb_strtolower(trim($this->email)) : null,
            'username' => $this->username ? trim($this->username) : null,
            'phone' => $this->phone ? preg_replace('/\s+/', '', $this->phone) : null,
            // 'referral_code' => $this->referral_code ? strtoupper(trim($this->referral_code)) : null,
        ]);
    }

    private function normalizeName(?string $value): ?string
    {
        return $value !== null ? trim($value) : null;
    }
}
