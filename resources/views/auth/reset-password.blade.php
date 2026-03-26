<!DOCTYPE html>
<html>

<head>
    <title>Resetting Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-success w-100">
                Reset Password
            </button>
        </form>

    </div>
</body>

</html>
