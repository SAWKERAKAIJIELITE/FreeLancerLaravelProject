<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #36b9cc, #4e73df);
            height: 100vh;
        }

        .card {
            border-radius: 15px;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 1000px;">
            <h3 class="text-center mb-4">Adding New {{ ucfirst($referral_role) }}</h3>

            <form method="POST" action="{{ route('add-member') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control"
                            required>
                        @error('first_name')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="form-control">
                        @error('middle_name')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control"
                            required>
                        @error('last_name')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="form-control"
                            required>
                        @error('birthdate')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="country_id">Country</label>
                        <select name="country_id" id="country_id" data-url="{{ route('metadata.countries') }}"
                            data-selected="{{ old('country_id') }}" class="form-select" required>
                            <option value="">Select country</option>
                            {{-- <option value="USA">USA</option>
                            <option value="CAN">Canada</option>
                            <option value="MEX">Mexico</option> --}}
                        </select>
                        @error('country_id')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="language_id">Language</label>
                        <select name="language_id" id="language_id" data-url="{{ route('metadata.languages') }}"
                            data-selected="{{ old('language_id') }}" class="form-select" required>
                            <option value="">Select language</option>
                            {{-- <option value="English">English</option>
                            <option value="Spanish">Spanish</option>
                            <option value="French">French</option> --}}
                        </select>
                        @error('language_id')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label" for="phone_country_id">Country Code</label>
                        <select name="phone_country_id" id="phone_country_id"
                            data-url="{{ route('metadata.phone-countries') }}"
                            data-selected="{{ old('phone_country_id') }}" class="form-control" required>
                            <option value="">Select country code</option>
                            {{-- <option value="+1">+1</option>
                            <option value="+44">+44</option>
                            <option value="+52">+52</option> --}}
                        </select>
                        @error('phone_country_id')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" class="form-control" required>
                        @error('phone')
                            {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    @error('email')
                        {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input name="username" value="{{ old('username') }}" class="form-control" required>
                    @error('username')
                        {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" name="role" class="form-control" required readonly
                        value="{{ $referral_role }}">
                    @error('role')
                        {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Referral Code</label>
                    <input class="form-control" type="text" name="referral_input" value="{{ $referralCode }}"
                        readonly>

                    @error('referral_input')
                        {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="terms_accepted" class="form-check-input" required>
                    <label class="form-check-label">I agree to terms</label>
                    @error('terms_accepted')
                        {{-- <div class="invalid-feedback d-block">{{ $errors }}</div> --}}
                    @enderror
                </div>
                <button class="btn btn-success w-100">Submit</button>
            </form>
            <div class="text-center mt-3">
                <a href="/login">Already have an account?</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    @php
        $oldCountryId = old('country_id');
        $oldLanguageId = old('language_id');
        $oldPhoneCountryId = old('phone_country_id');
    @endphp
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        document.addEventListener('DOMContentLoaded', async () => {
            const countrySelect = document.getElementById('country_id');
            const languageSelect = document.getElementById('language_id');
            const phoneCountrySelect = document.getElementById('phone_country_id');

            const oldCountryId = @json($oldCountryId);
            const oldLanguageId = @json($oldLanguageId);
            const oldPhoneCountryId = @json($oldPhoneCountryId);

            try {
                const [countriesResponse, languagesResponse, phoneCountriesResponse] = await Promise.all([
                    fetch(@json(route('metadata.countries'))),
                    fetch(@json(route('metadata.languages'))),
                    fetch(@json(route('metadata.phone-countries'))),
                ]);

                if (!countriesResponse.ok || !languagesResponse.ok || !phoneCountriesResponse.ok) {
                    throw new Error('Failed to load metadata.');
                }

                const countriesPayload = await countriesResponse.json();
                const languagesPayload = await languagesResponse.json();
                const phoneCountriesPayload = await phoneCountriesResponse.json();

                populateCountryOptions(countrySelect, countriesPayload.data, oldCountryId);
                populateLanguageOptions(languageSelect, languagesPayload.data, oldLanguageId);
                populatePhoneCountryOptions(phoneCountrySelect, phoneCountriesPayload.data, oldPhoneCountryId);

                if (!oldPhoneCountryId && oldCountryId) {
                    phoneCountrySelect.value = oldCountryId;
                }

                countrySelect.addEventListener('change', () => {
                    if (!phoneCountrySelect.value) {
                        phoneCountrySelect.value = countrySelect.value;
                    }
                });
            } catch (error) {
                console.error(error);
            }
        });

        function populateCountryOptions(select, countries, selectedValue = null) {
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = `${country.flag_emoji ?? ''} ${country.name}`.trim();

                if (String(selectedValue) === String(country.id)) {
                    option.selected = true;
                }

                select.appendChild(option);
            });
        }

        function populateLanguageOptions(select, languages, selectedValue = null) {
            languages.forEach(language => {
                const option = document.createElement('option');
                option.value = language.id;
                option.textContent = language.native_name ?
                    `${language.name} (${language.native_name})` :
                    language.name;

                if (String(selectedValue) === String(language.id)) {
                    option.selected = true;
                }

                select.appendChild(option);
            });
        }

        function populatePhoneCountryOptions(select, countries, selectedValue = null) {
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = `${country.flag_emoji ?? ''} ${country.name} (+${country.phone_code})`.trim();

                if (String(selectedValue) === String(country.id)) {
                    option.selected = true;
                }

                select.appendChild(option);
            });
        }
    </script>
</body>

</html>
