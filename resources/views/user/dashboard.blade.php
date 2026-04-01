<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

    <h2 class="mb-4">User Dashboard</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div class="text-end">
                    <small class="text-muted">Your Referral Code</small><br>

                    <div class="input-group input-group-sm mt-1">
                        <input type="text" id="referralCode" class="form-control"
                            value="{{ e(Auth::user()->referral_code) }}" readonly>

                        <button class="btn btn-outline-primary" type="button"
                            onclick="copyToClipboard('referralCode', this)">
                            Copy
                        </button>
                    </div>
                </div>
            </div>

            <hr>

            <div>
                <small class="text-muted">Your Referral Link</small>

                <div class="input-group mt-2">
                    <input type="text" id="referralLink" class="form-control"
                        value="{{ e(url('/register?ref=' . Auth::user()->referral_code)) }}" readonly>
                    <a href="{{ e(url('/register?ref=' . Auth::user()->referral_code)) }}" target="_blank"
                        rel="noopener noreferrer" class="btn btn-outline-primary">
                        Open Link</a>
                    <button class="btn btn-primary" type="button" onclick="copyToClipboard('referralLink', this)">
                        Copy Link
                    </button>
                </div>
            </div>

        </div>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Country</th>
                <th>Referral Code</th>
                {{-- <th>Referral Link</th> --}}
                <th>Reviewed Date</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $req)
                <tr>
                    <td>{{ $req->username }}</td>
                    <td>{{ $req->email }}</td>
                    <td>{{ $req->country->name }}</td>
                    <td>{{ $req->referral_code }}</td>
                    <td>{{ $req->approved_at?->format('Y-m-d') ?? ($req->rejected_at?->format('Y-m-d') ?? '-') }}</td>
                    <td>{{ $req->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $requests->appends(request()->query())->links() }}
    </div>

    <form method="POST" action="/logout">
        @csrf
        <button class="btn btn-secondary mt-3">Logout</button>
    </form>
    <script>
        function copyToClipboard(elementId, button) {
            const input = document.getElementById(elementId);

            input.select();
            input.setSelectionRange(0, 99999); // mobile support

            navigator.clipboard.writeText(input.value).then(() => {
                const originalText = button.innerText;
                button.innerText = 'Copied ✔️';
                button.classList.remove('btn-primary', 'btn-outline-primary');
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerText = originalText;
                    button.classList.remove('btn-success');
                    if (elementId === 'referralLink') {
                        button.classList.add('btn-primary');
                    } else {
                        button.classList.add('btn-outline-primary');
                    }
                }, 1500);
            });
        }
    </script>
</body>

</html>
