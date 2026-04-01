<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4" style="max-width: max-content;">

    <h2 class="mb-4">{{ Auth::user()->username }} Admin Dashboard</h2>
    <form method="GET" action="/admin/signup" target="_blank" rel="noopener noreferrer" class="row g-2 mb-4">
        @csrf
        <button class="btn btn-primary">
            add another admin
        </button>
    </form>

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
            <label class="form-label">Username</label>
            <input name="username" placeholder="Username" class="form-control">
        </div>
        <div class="col">
            <label class="form-label">Email</label>
            <input name="email" placeholder="Email" class="form-control">
        </div>
        <div class="col">
            <label class="form-label" for="country_id">Country</label>
            <select name="country_id" id="country_id" data-url="{{ route('metadata.countries') }}"
                data-selected="{{ old('country_id') }}" class="form-select">
                <option value="">Select country</option>
            </select>
            @error('country_id')
                {{-- <div class="invalid-feedback">{{ $errors }}</div> --}}
            @enderror
        </div>
        <div class="col">
            <label class="form-label">Revision date</label>
            <input type="date" name="reviewed_at" placeholder="Revision date" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Ref. Code or Username</label>
            <input name="referral_code" placeholder="Referral Code or Username" class="form-control">
        </div>
        <div class="col">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" label="status">
                <option value="pending">pending</option>
                <option value="accepted">accepted</option>
                <option value="rejected">rejected</option>
                <option value="">Reset</option>
            </select>
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>
    @php
        $oldCountryId = old('country_id');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const countrySelect = document.getElementById('country_id');

            const oldCountryId = @json($oldCountryId);

            try {
                const [countriesResponse] = await Promise.all([
                    fetch(@json(route('metadata.countries'))),
                ]);

                if (!countriesResponse.ok) {
                    throw new Error('Failed to load metadata.');
                }

                const countriesPayload = await countriesResponse.json();

                populateCountryOptions(countrySelect, countriesPayload.data, oldCountryId);

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
    </script>
    <table class="table table-bordered align-middle" style="width: fixed;">
        <thead class="table-light">
            <tr>
                {{-- <th>Name</th> --}}
                <th>Username</th>
                <th>Role</th>
                <th>Referred By</th>
                <th>Email</th>
                <th>Country</th>
                {{-- <th>Referred By Code</th> --}}
                <th>Referral Code</th>
                <th>Referral Link</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Reviewed Date</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $req)
                <tr>
                    {{-- <td>{{ $req->first_name }} {{ $req->last_name }}</td> --}}
                    <td>{{ $req->username }}</td>
                    <td>{{ $req->role }}</td>
                    <td class="bg-{{ $req->referral()->first() == null ? 'warning' : '' }}">
                        <table>
                            <tr>
                                <td>
                                    {{ $req->referral()->first()?->username ?? 'NOT REFERRED' }}/
                                </td>
                                <td>
                                    {{ $req->referral()->first()?->role ?? 'NO Role' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>{{ $req->email }}</td>
                    <td>{{ $req->country->name }}</td>
                    {{-- <td>{{ $req->referral()->first()->referral_code }}</td> --}}
                    <td>{{ $req->user()->first()?->referral_code ?? '-' }}</td>
                    <td>
                        @if ($req->status === 'accepted' && $req->user()->first())
                            <div class="input-group input-group-sm">
                                <input type="text" id="link-{{ $req->id }}" class="form-control"
                                    value="{{ url('/register?ref=' . $req->user()->first()->referral_code) }}"
                                    readonly>
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
                            <form method="POST" action="/admin/signup-requests/{{ $req->id }}/reject"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        @endif
                    </td>
                    <td>{{ $req->approved_at?->format('Y-m-d') ?? ($req->rejected_at?->format('Y-m-d') ?? '-') }}</td>
                    <td>{{ $req->created_at->format('Y-m-d') }}</td>
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
