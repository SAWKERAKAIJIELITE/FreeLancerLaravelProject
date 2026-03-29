<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
        }

        .card {
            border-radius: 15px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-4">Welcome Back 👋</h3>


        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label>Email</label>

                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label for="remember" class="form-check-label">Remember Me</label>
            </div>
            <button class="btn btn-primary w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
        </div>
        <div class="text-center mt-3">
            <a href="/signup">Create Account</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                {{ $errors->first() }}
            </div>
        @endif
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
