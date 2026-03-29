<!DOCTYPE html>
<html>

<head>
    <title>Resetting Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <div class="container mt-5" style="max-width: 500px;">

        <h3 class="mb-4 text-center">Reset Password 🔒</h3>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="{{ $email }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>New Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <div class="input-group">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                        required>
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePassword('password_confirmation', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-success w-100">
                Reset Password
            </button>
        </form>

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
