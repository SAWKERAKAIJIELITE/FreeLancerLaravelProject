<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

    <h2 class="mb-4">Admin Dashboard</h2>

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

                    <button class="btn btn-primary" type="button" onclick="copyToClipboard('referralLink', this)">
                        Copy Link
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Copy Script --}}
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

    <form method="GET" action="/admin/signup-requests" class="row g-2 mb-4">
        @csrf
        <div class="col">
            <input name="username" placeholder="Username" class="form-control">
        </div>
        <div class="col">
            <input name="email" placeholder="Email" class="form-control">
        </div>
        <div class="col">
            <input name="country" placeholder="Country" class="form-control">
        </div>
        <div class="col">
            <input type="date" name="birthdate" class="form-control">
        </div>
        <div class="col">
            <input name="referral_code" placeholder="Referral Code or Username" class="form-control">
        </div>
        <div class="col">
            {{-- <label>status</label> --}}
            <select name="status" class="form-control" label="status">
                <option value="pending">pending</option>
                <option value="accepted">accepted</option>
                <option value="rejected">rejected</option>
                <option value="">Reset</option>
            </select>
        </div>

        <div class="col">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                {{-- <th>Name</th> --}}
                <th>Username</th>
                <th>Referred By</th>
                <th>Email</th>
                <th>Country</th>
                {{-- <th>Referred By Code</th> --}}
                <th>Referral Code</th>
                <th>Referral Link</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $req)
                <tr>
                    {{-- <td>{{ $req->first_name }} {{ $req->last_name }}</td> --}}
                    <td>{{ $req->username }}</td>
                    <td>{{ $req->referral()->first()->username }}</td>
                    <td>{{ $req->email }}</td>
                    <td>{{ $req->country }}</td>
                    {{-- <td>{{ $req->referral()->first()->referral_code }}</td> --}}
                    <td>{{ $req->user()->first()?->referral_code ?? '-' }}</td>
                    <td>
                        @if ($req->status === 'accepted' && $req->user()->first())
                            <div class="input-group input-group-sm">
                                <input type="text" id="link-{{ $req->id }}" class="form-control"
                                    value="{{ url('/register?ref=' . $req->user()->first()->referral_code) }}" readonly>
                                <button class="btn btn-outline-primary"
                                    onclick="copyRowLink('link-{{ $req->id }}', this)">
                                    Copy
                                </button>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <script>
                        function copyRowLink(elementId, button) {
                            const input = document.getElementById(elementId);

                            input.select();
                            input.setSelectionRange(0, 99999);

                            navigator.clipboard.writeText(input.value).then(() => {
                                const original = button.innerText;

                                button.innerText = 'Copied ✔️';
                                button.classList.remove('btn-outline-primary');
                                button.classList.add('btn-success');

                                setTimeout(() => {
                                    button.innerText = original;
                                    button.classList.remove('btn-success');
                                    button.classList.add('btn-outline-primary');
                                }, 1200);
                            });
                        }
                    </script>
                    <td>
                        <span
                            class="badge bg-{{ $req->status == 'pending' ? 'warning' : ($req->status == 'accepted' ? 'success' : 'danger') }}
                            text-dark">
                            {{ ucfirst($req->status) }}
                        </span>
                    </td>
                    <td>
                        @if ($req->status == 'pending')
                            <form method="POST" action="/admin/signup-requests/{{ $req->id }}/approve"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Accept</button>
                            </form>
                            <form method="POST" action="/admin/signup-requests/{{ $req->id }}/reject" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $requests->appends(request()->query())->links() }}
    </div>

    <form method="POST" action="/logout" class="mt-3">
        @csrf
        <button class="btn btn-secondary">Logout</button>
    </form>
</body>

</html>
