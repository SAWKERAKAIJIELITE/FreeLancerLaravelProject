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
        <div class="card shadow p-4 mx-auto" style="max-width: 700px;">
            <h3 class="text-center mb-4">Create Account 🚀</h3>

            <form method="POST" action="/register">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>First Name</label>
                        <input name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Middle Name</label>
                        <input name="middle_name" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Last Name</label>
                        <input name="last_name" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Country</label>
                        <select name="country" class="form-control" required>
                            <option value="USA">USA</option>
                            <option value="CAN">Canada</option>
                            <option value="MEX">Mexico</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Language</label>
                        <select name="language" class="form-control" required>
                            <option value="English">English</option>
                            <option value="Spanish">Spanish</option>
                            <option value="French">French</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Country Code</label>
                        <select name="country_code" class="form-control" required>
                            <option value="+1">+1</option>
                            <option value="+44">+44</option>
                            <option value="+52">+52</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Phone</label>
                        <input name="phone" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
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
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="regular">Regular</option>
                        <option value="educator">Educator</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Referral Code (optional)</label>
                    {{-- <input type="text" name="referral_input" placeholder="Referral Code or Username" class="form-control"
                        value="{{ old('referral_input',$referralCode ?? '') }}"> --}}
                    @if (!empty($referralCode))
                        <input class="form-control" type="text" name="referral_input" value="{{ $referralCode }}"
                            readonly>
                    @else
                        <input class="form-control" type="text" name="referral_input"
                            value="{{ old('referral_input') }}" placeholder="Referral Code or Username">
                    @endif
                    @error('referral_input')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="terms_accepted" class="form-check-input" required>
                    <label class="form-check-label">I agree to terms</label>
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
    </script>
</body>

</html>
