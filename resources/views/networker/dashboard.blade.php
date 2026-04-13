<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4" style="max-width: max-content;">

    <h2 class="mb-4">{{ Auth::user()->username }} Networker Dashboard</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div class="text-center">
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
                <a href="{{ e(url('/add-networker?ref=' . Auth::user()->referral_code)) }}" target="_blank"
                    rel="noopener noreferrer" class="btn btn-outline-primary">
                    add networker
                </a>
                <a href="{{ e(url('/add-regular?ref=' . Auth::user()->referral_code)) }}" target="_blank"
                    rel="noopener noreferrer" class="btn btn-outline-primary">
                    add regular user
                </a>
            </div>

            <hr>

            <div>
                <small class="text-muted">Your Referral Link</small>

                <div class="input-group mt-2">
                    <input type="text" id="referralLink" class="form-control"
                        value="{{ e(url('/signup?ref=' . Auth::user()->referral_code)) }}" readonly>

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

    <form method="GET" action="{{ route('signup-requests.index') }}" class="row g-2 mb-4">
        @csrf
        <div class="col">
            <label class="form-label">Username</label>
            <input name="username" placeholder="Username" value="{{ old('username') }}" class="form-control">
        </div>
        {{-- <div class="col">
            <label class="form-label">Email</label>
            <input name="email" placeholder="Email" class="form-control">
        </div> --}}
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
            <input type="date" name="reviewed_at" value="{{ old('reviewed_at') }}" placeholder="Revision date"
                class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Referral Code</label>
            <input name="referral_code" placeholder="Referral Code" value="{{ old('referral_code') }}"
                class="form-control">
        </div>
        <div class="col">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" label="status" data-selected="{{ old('country_id') }}">
                <option value="">All</option>
                <option value="pending">pending</option>
                <option value="accepted">accepted</option>
                <option value="rejected">rejected</option>
            </select>
        </div>
        <div class="col">
            <label class="form-label">Request date</label>
            <input type="date" name="requested_at" value="{{ old('requested_at') }}" placeholder="Request date"
                class="form-control">
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
    <h1 class="h4 mb-0">Signup Requests</h1>
    <span class="badge bg-primary" id="pending-count">{{ $requests->where('status', 'pending')->count() }}</span>

    <table id="signup-requests-table" class="table table-bordered align-middle" style="width: fixed;">
        <thead class="table-light">
            <tr>
                {{-- <th>Name</th> --}}
                <th>Username</th>
                <th>Role</th>
                {{-- <th>Referred By</th>
                <th>Referred By (Role)</th> --}}
                {{-- <th>Email</th> --}}
                <th>Country</th>
                {{-- <th>Referred By Code</th> --}}
                {{-- <th>Referral Code</th> --}}
                {{-- <th>Referral Link</th> --}}
                <th>Status</th>
                <th>Actions</th>
                <th>Reviewed By</th>
                <th>Reviewed Date</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $req)
                <tr id="signup-requests-row-{{ $req->id }}">
                    {{-- <td>{{ $req->first_name }} {{ $req->last_name }}</td> --}}
                    <td class="request-username">{{ $req->username }}</td>
                    <td class="request-role">{{ $req->role }}</td>
                    {{-- <td class="bg-{{ $req->referral()->first() == Auth::user() ? 'warning' : '' }}">
                        {{ $req->referral()->first()?->username }}
                    </td>
                    <td>
                        {{ $req->referral()->first()?->role }}
                    </td> --}}
                    {{-- <td>{{ $req->email }}</td> --}}
                    <td class="request-country">{{ $req->country->name }}</td>
                    {{-- <td>{{ $req->referral()->first()->referral_code }}</td> --}}
                    {{-- <td>{{ $req->user()->first()?->referral_code ?? '-' }}</td> --}}
                    {{-- <td>
                        @if ($req->status->value === 'accepted' && $req->user()->first())
                            <div class="input-group input-group-sm">
                                <input type="text" id="link-{{ $req->id }}" class="form-control"
                                    value="{{ url('/signup?ref=' . $req->user()->first()->referral_code) }}" readonly>
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
                    </script> --}}
                    <td>
                        <span
                            class="badge bg-{{ $req->status->value == 'pending' ? 'warning' : ($req->status->value == 'accepted' ? 'success' : 'danger') }}
                            text-dark request-status">
                            {{ ucfirst($req->status->value) }}
                            @if ($req->status->value == 'rejected')
                                <button type="button" class="btn btn-link btn-sm p-0 ms-2 view-rejection-reason-btn"
                                    data-bs-toggle="modal" data-bs-target="#rejectionReasonModal"
                                    data-request-id="{{ $req->id }}" data-user-name="{{ $req->username }}"
                                    data-role="{{ $req->role }}" data-reason="{{ e($req->rejection_reason) }}"
                                    data-referred-by="{{ $req->referral()->first()?->username }}"
                                    data-reviewed-by="{{ $req->reviewer->username }}"
                                    data-reviewed-at="{{ optional($req->reviewed_at)?->format('Y-m-d H:i') ?? '-' }}">
                                    View reason
                                </button>
                            @endif
                        </span>
                        <div class="modal fade" id="rejectionReasonModal" tabindex="-1"
                            aria-labelledby="rejectionReasonModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header">
                                        <div>
                                            <h5 class="modal-title mb-1" id="rejectionReasonModalLabel">
                                                Rejection Reason
                                            </h5>
                                            <small class="text-muted" id="rejection-request-meta">
                                                Request details
                                            </small>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <div class="fw-semibold text-dark" id="rejection-user-name">-</div>
                                            <div class="text-muted small" id="rejection-role">-</div>
                                        </div>

                                        <div class="p-3 rounded bg-light border">
                                            <div class="small text-muted mb-2">Reason</div>
                                            <div id="rejection-reason-text" class="text-dark"
                                                style="white-space: pre-wrap;">-</div>
                                        </div>

                                        <div class="mt-3 small text-muted">
                                            <div><strong>Referred by:</strong> <span
                                                    id="rejection-referred-by">-</span></div>
                                            <div><strong>Reviewed by:</strong> <span
                                                    id="rejection-reviewed-by">-</span></div>
                                            <div><strong>Reviewed at:</strong> <span
                                                    id="rejection-reviewed-at">-</span></div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="request-actions">
                        @if ($req->status->value == 'pending')
                            <form method="POST" action="/signup-requests/{{ $req->id }}/approve"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Accept</button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm open-reject-modal-btn"
                                data-bs-toggle="modal" data-bs-target="#rejectModal"
                                data-request-id="{{ $req->id }}" data-user-name="{{ $req->username }}">
                                Reject
                            </button>
                            <div class="modal fade" id="rejectModal" tabindex="-1"
                                aria-labelledby="rejectModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel">Reject Signup Request</h5>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <form id="rejectForm" method="POST">
                                            @csrf

                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <div class="fw-semibold" id="reject-user-name">-</div>
                                                    <small class="text-muted">You are about to reject this
                                                        request</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Rejection Reason</label>
                                                    <textarea name="rejection_reason" id="reject-reason-input" class="form-control" rows="4"
                                                        placeholder="Enter reason..." required minlength="5"></textarea>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    data-bs-dismiss="modal">
                                                    Cancel
                                                </button>

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Confirm Reject
                                                </button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                            {{-- <form method="POST" action="/signup-requests/{{ $req->id }}/reject"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form> --}}
                        @endif
                    </td>
                    <td class="request-reviewed-by">{{ $req->reviewer()->first()?->username }}</td>
                    <td class="request-reviewed-at">
                        {{ $req->approved_at?->format('Y-m-d H-i-s') ?? ($req->rejected_at?->format('Y-m-d H-i-s') ?? '-') }}
                    </td>
                    <td class="request-created-at">{{ $req->created_at->format('Y-m-d H-i-s') }}</td>
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
    <script>
        window.AppUser = {
            id: @json(Auth::id()),
            role: @json(Auth::user()->role instanceof \BackedEnum ? Auth::user()->role->value : Auth::user()->role),
        };
        window.csrfToken = @json(csrf_token());
    </script>
    @vite(['resources/js/app.js'])
</body>

</html>
