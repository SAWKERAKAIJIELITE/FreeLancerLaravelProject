<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <div class="container text-center mt-5">

        <h3>Verify Your Email 📧</h3>

        <p class="text-muted">
            We sent a verification link to your email.
            Please check your inbox and click the link.
        </p>

        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary">
                Resend Email
            </button>
        </form>

    </div>
</body>

</html>
