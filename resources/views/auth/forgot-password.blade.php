<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <div class="container mt-5" style="max-width: 500px;">

        <h3 class="mb-4 text-center">Forgot Password 🔑</h3>
        <p class="text-muted">
            Enter your email associated with your account to send you a verification link.
        </p>
        <p class="text-muted">
            After that Please check your inbox and click the link.
        </p>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">
                Send Reset Link
            </button>
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>

    </div>
</body>

</html>
