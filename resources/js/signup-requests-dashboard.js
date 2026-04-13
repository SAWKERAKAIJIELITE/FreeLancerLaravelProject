console.log('Echo exists:', !!window.Echo);
console.log('User:', window.AppUser);

function escapeHtml(value)
{
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function renderActions(event)
{
    if (event.status === 'pending')
    {
        return `
            <form method="POST" action="/signup-requests/${event.id}/approve" class="d-inline">
                <input type="hidden" name="_token" value="${window.csrfToken}">
                <button class="btn btn-success btn-sm">Accept</button>
            </form>

            <button
                type="button"
                class="btn btn-danger btn-sm open-reject-modal-btn"
                data-bs-toggle="modal" data-bs-target="#rejectModal"
                data-request-id="${event.id}"
                data-user-name="${escapeHtml(event.username ?? '-')}"
            >
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
                        <form id="rejectForm" method="POST" action="/signup-requests/${event.id}/reject">
                        <input type="hidden" name="_token" value="${window.csrfToken}">
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
                                    Cancel </button>
                            <button type="submit" class="btn btn-danger btn-sm">
                            Confirm Reject </button>
                </div>
                    </form>
                                    </div>
                                </div>
                            </div>
        `;
    }

    return ``;
}

function referredByAdmin(event)
{
    if (window.AppUser.role === 'super_admin')
    {
        return `<td class="bg-${window.AppUser && event.referred_by === window.AppUser.username ? 'warning' : ''} request-referred-by">${event.referred_by ?? '-'}</td>
                <td class="request-referred-by-role">${event.referred_by_role ?? '-'}</td>`;
    }

    return '';
}

function ensureRow(event)
{
    const tbody = document.querySelector('#signup-requests-table tbody');
    let row = document.getElementById(`signup-request-row-${event.id}`);
    console.log(`query selector ${tbody}:`);
    console.log(`Ensuring row for event ID ${event.id}:`, row);

    if (!row)
    {
        row = document.createElement('tr');
        row.id = `signup-request-row-${event.id}`;
        row.innerHTML = `
            <td class="request-username">${event.username ?? '-'}</td>
            <td class="request-role">${event.role ?? '-'}</td>
            ${referredByAdmin(event)}
            <td class="request-country">${event.country ?? '-'}</td>
            <td class="request-referral-code">${event.referral_code ?? '-'}</td>
            <td>
                <span class="badge bg-warning text-dark request-status">
                    ${formatStatus(event.status ?? 'pending')}
                </span>
            </td>
            <td class="request-actions">${renderActions(event)}</td>
            <td class="request-reviewed-by">${event.reviewed_by ?? '-'}</td>
            <td class="request-reviewed-at">${event.reviewed_at ?? '-'}</td>
            <td class="request-created-at">${event.created_at ?? '-'}</td>
        `;

        tbody.prepend(row);
    }

    return row;
}

function formatStatus(status)
{
    if (!status) return '-';
    return status.charAt(0).toUpperCase() + status.slice(1);
}

function renderStatusCell(event)
{
    if (event.status === 'pending')
    {
        return `<span class="badge bg-warning text-dark">Pending</span>`;
    }

    if (event.status === 'approved')
    {
        return `<span class="badge bg-success">Approved</span>`;
    }

    if (event.status === 'rejected')
    {
        return `
            <span class="badge bg-danger">Rejected</span>
            <button
                type="button"
                class="btn btn-link btn-sm p-0 ms-2 view-rejection-reason-btn"
                data-bs-toggle="modal"
                data-bs-target="#rejectionReasonModal"
                data-request-id="${event.id}"
                data-user-name="${escapeHtml(event.username ?? '-')}"
                data-role="${escapeHtml(event.role ?? '-')}"
                data-reason="${escapeHtml(event.rejection_reason ?? 'No reason provided.')}"
                data-referred-by="${escapeHtml(event.referred_by ?? 'Unknown')}"
                data-reviewed-by="${escapeHtml(event.reviewed_by ?? 'Unknown')}"
                data-reviewed-at="${escapeHtml(event.reviewed_at ?? '-')}"
            >
                View reason
            </button>
        `;
    }
    return `<span class="badge bg-secondary">${escapeHtml(event.status)}</span>`;
}

function updateReviewedRow(event)
{
    const row = document.getElementById(`signup-request-row-${event.id}`);
    console.log(`updating row for event ID ${event.id}:`, row);
    if (!row) return;

    const statusCell = row.querySelector('.request-status');
    const reviewedAtCell = row.querySelector('.request-reviewed-at');
    const reviewedByCell = row.querySelector('.request-reviewed-by');
    const actionsCell = row.querySelector('.request-actions');

    if (statusCell)
    {
        statusCell.innerHTML = renderStatusCell(event);
    }
    if (reviewedAtCell)
    {
        reviewedAtCell.textContent = event.reviewed_at;
    }
    if (reviewedByCell)
    {
        reviewedByCell.textContent = event.reviewed_by ?? '-';
    }
    if (actionsCell)
    {
        actionsCell.innerHTML = renderActions(event);
    }
}

function bindRejectionReasonModal()
{
    const modal = document.getElementById('rejectionReasonModal');

    if (!modal || modal.dataset.bound === '1')
    {
        return;
    }
    modal.dataset.bound = '1'

    modal.addEventListener('show.bs.modal', function (event)
    {
        console.log('Binding rejection reason modal...');
        const button = event.relatedTarget;

        if (!button)
        {
            return;
        }

        document.getElementById('rejection-user-name').textContent =
            button.getAttribute('data-user-name') || '-';

        document.getElementById('rejection-role').textContent =
            button.getAttribute('data-role') || '-';

        document.getElementById('rejection-reason-text').textContent =
            button.getAttribute('data-reason') || 'No reason provided.';

        document.getElementById('rejection-referred-by').textContent =
            button.getAttribute('data-referred-by') || '-';

        document.getElementById('rejection-reviewed-by').textContent =
            button.getAttribute('data-reviewed-by') || '-';

        document.getElementById('rejection-reviewed-at').textContent =
            button.getAttribute('data-reviewed-at') || '-';

        document.getElementById('rejection-request-meta').textContent =
            `Request #${button.getAttribute('data-request-id') || '-'}`;
    });

    // modal.dataset.bound = '0';
}

function bindRejectModal()
{
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');

    if (!modal || !form || modal.dataset.bound === '1') return;
    modal.dataset.bound = '1';

    modal.addEventListener('show.bs.modal', function (event)
    {
        console.log('Binding reject modal...');
        // time.sleep(10000);
        const button = event.relatedTarget;

        if (!button) return;

        const requestId = button.getAttribute('data-request-id');
        const fullName = button.getAttribute('data-user-name');

        // Set form action dynamically
        // form.action = `/signup-requests/${requestId}/reject`;

        // Fill UI
        document.getElementById('reject-user-name').textContent = fullName || '-';

        // Reset textarea
        document.getElementById('reject-reason-input').value = '';

        // const modal = new bootstrap.Modal(modal);
        // modal.show();

        const textarea = document.getElementById('reject-reason-input');

        textarea.addEventListener('input', () =>
        {
            console.log(textarea.value.length);
        });
    });
    // modal.dataset.bound = '0';

    form.addEventListener('submit', function (e)
    {
        const value = document.getElementById('reject-reason-input').value.trim();

        if (!value)
        {
            e.preventDefault();
            alert('Please enter a rejection reason.');
        }
    });
}

document.addEventListener('DOMContentLoaded', () =>
{
    bindRejectionReasonModal();
    bindRejectModal();
    // console.log(window.csrfToken)

    if (!window.Echo)
    {
        console.error('Echo is not available.');
        return;
    }

    if (!window.AppUser)
    {
        console.error('AppUser is not defined.');
        return;
    }

    const role = window.AppUser.role;
    const userId = window.AppUser.id;

    if (role === 'super_admin')
    {
        window.Echo.private('super-admin.signup-requests')
            .subscribed(() =>
            {
                console.log('Subscribed to admin.signup-requests');
            })
            .error((error) =>
            {
                console.error('Admin channel subscription error:', error);
            })
            .listen('.signup-request.created', (event) =>
            {
                console.log('received create event', event);
                ensureRow(event);
            })
            .listen('.signup-request.reviewed', (event) =>
            {
                console.log('received reviewed event', event);
                updateReviewedRow(event);
            });
    }

    if (role === 'networker')
    {
        console.log(`Subscribing to networker.${userId}.signup-requests...`);
        window.Echo.private(`networker.${userId}.signup-requests`)
            .subscribed(() =>
            {
                console.log(`Subscribed to networker.${userId}.signup-requests`);
            })
            .error((error) =>
            {
                console.error('Networker channel subscription error:', error);
            })
            .listen('.signup-request.created', (event) =>
            {
                console.log('received create event', event);
                ensureRow(event);
            })
            .listen('.signup-request.reviewed', (event) =>
            {
                console.log('received reviewed event', event);
                updateReviewedRow(event);
            });
    }
});
