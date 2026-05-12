
<style>
    /* Allow navbar overflow for dropdowns */
    header.navbar,
    .navbar .container-xxl {
        overflow: visible !important;
    }

    /* Shop selector on right side */
    .shop-dropdown-wrapper {
        position: relative;
    }

    .shop-dropdown-menu {
        width: 320px !important;
        max-height: 400px;
        overflow-y: auto;
    }

    .shop-dropdown-menu .dropdown-item {
        padding: 0.65rem 1rem;
    }

    .shop-dropdown-menu .shop-info {
        min-width: 0;
        flex: 1;
    }

    .shop-dropdown-menu .shop-info > div {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Keep activity modal above sticky header and custom backdrops */
    #auditLogModal {
        z-index: 210000;
    }

    #auditLogModal .modal-content {
        pointer-events: auto;
    }
</style>

<header class="navbar navbar-expand-md sticky-top d-print-none" style="z-index: 1100; border: none;">
    <div class="container-xxl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ url('/') }}" class="d-flex align-items-center">
                <span class="navbar-brand-text fs-2 fw-bold text-dark">Cherry Computers</span>
            </a>
        </h1>

        <div class="navbar-nav flex-row order-md-last align-items-center" style="gap: 0.75rem;">

            <div class="nav-item d-flex align-items-center">
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}?t={{ time() }}"
                         alt="{{ Auth::user()->name }}"
                         class="rounded-circle shadow-sm me-2"
                         style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; object-fit: cover;">
                @else
                    <span class="rounded-circle shadow-none me-2 d-inline-block"
                          style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; background-image: url({{ \Laravolt\Avatar\Facade::create(Auth::user()->name)->toBase64() }}); background-size: cover; background-position: center;">
                    </span>
                @endif
                {{-- <div class="d-none d-xl-block">
                    <div class="small text-muted">{{ Auth::user()->name }}</div>
                </div> --}}
            </div>

            @if(!Auth::user()->isEmployee())
            {{-- Notification Bell → opens Audit Log Modal --}}
            <div class="nav-item">
                <button type="button"
                        id="notif-bell-toggle"
                        class="btn btn-ghost-secondary p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent"
                        style="width:40px;height:40px;"
                        aria-label="Recent Activity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                    </svg>
                    @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                    @if($unreadCount > 0)
                    <span class="badge bg-danger badge-pill position-absolute"
                          id="notif-badge"
                          style="top:2px;right:2px;min-width:18px;height:18px;font-size:10px;padding:2px 4px;border-radius:50px;">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                    @endif
                </button>
            </div>

            {{-- Audit Log Modal --}}
            <div class="modal fade" id="auditLogModal" tabindex="-1" aria-labelledby="auditLogModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:540px;">
                    <div class="modal-content shadow-lg" style="border-radius:12px;overflow:hidden;">
                        {{-- Header --}}
                        <div class="modal-header border-bottom d-flex justify-content-between align-items-start" style="background:linear-gradient(135deg,#206bc4 0%,#1a56a0 100%);color:#fff;padding:1rem 1.25rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:34px;height:34px;background:rgba(255,255,255,0.2);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="#fff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                        <rect x="9" y="3" width="6" height="4" rx="2"/>
                                        <line x1="9" y1="12" x2="15" y2="12"/>
                                        <line x1="9" y1="16" x2="12" y2="16"/>
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="modal-title mb-0 fw-semibold" id="auditLogModalLabel" style="color:#fff;font-size:1rem;">Recent Activity</h5>
                                    <div style="font-size:0.72rem;opacity:0.8;">Latest 10 log entries</div>
                                </div>
                            </div>
                            <button type="button" id="auditLogClose" class="d-flex align-items-center justify-content-center border-0 rounded-circle ms-3" style="width:34px;height:34px;background:rgba(255,255,255,0.2);cursor:pointer;flex-shrink:0;" data-bs-dismiss="modal" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2.5" stroke="#fff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="modal-body p-0" id="audit-log-body" style="max-height:420px;overflow-y:auto;">
                            <div class="text-center text-muted py-5">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <span class="small">Loading activity logs...</span>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="modal-footer border-top" style="padding:0.75rem 1.25rem;background:#f8fafc;">
                            <a href="{{ shop_route('logs.index') }}" id="viewAllLogsBtn" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                View all logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="nav-item">
                <form action="{{ shop_route('logout') }}" method="post" class="m-0" style="display: inline;">
                    @csrf
                    <button type="submit" class="border-0 bg-transparent p-0" style="cursor: pointer; width: 40px !important; height: 40px !important; display: inline-flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M9 12h12l-3 -3" />
                            <path d="M18 15l3 -3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@if(!Auth::user()->isEmployee())
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl  = document.getElementById('auditLogModal');
    const bellBtn  = document.getElementById('notif-bell-toggle');
    if (!modalEl || !bellBtn) return;

    // Bootstrap modals behave more reliably when mounted directly under body.
    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.error('Bootstrap modal is not available.');
        return;
    }

    function cleanupModalArtifacts() {
        const visibleModal = document.querySelector('.modal.show');
        if (!visibleModal) {
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        }

        const backdrops = Array.from(document.querySelectorAll('.modal-backdrop'));
        if (!visibleModal && backdrops.length) {
            backdrops.forEach(function (el) { el.remove(); });
            return;
        }

        if (backdrops.length > 1) {
            backdrops.slice(0, -1).forEach(function (el) { el.remove(); });
        }
    }

    function forceCloseModal() {
        try { bsModal.hide(); } catch (e) { console.error('Unable to hide activity modal:', e); }
        setTimeout(cleanupModalArtifacts, 120);
    }

    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, { keyboard: true, backdrop: true });

    // Open on bell click
    bellBtn.addEventListener('click', function () {
        cleanupModalArtifacts();
        try {
            bsModal.show();
        } catch (e) {
            console.error('Unable to open activity modal:', e);
            cleanupModalArtifacts();
        }
    });

    // Close buttons (header X and footer Close)
    ['auditLogClose'].forEach(function (id) {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', forceCloseModal);
    });

    const viewAllLogsBtn = document.getElementById('viewAllLogsBtn');
    if (viewAllLogsBtn) {
        viewAllLogsBtn.addEventListener('click', function (e) {
            const href = viewAllLogsBtn.getAttribute('href');
            if (!href) {
                e.preventDefault();
                forceCloseModal();
                return;
            }

            const currentPath = window.location.pathname.replace(/\/+$/, '');
            const targetPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '');

            if (currentPath === targetPath) {
                e.preventDefault();
                forceCloseModal();
            } else {
                forceCloseModal();
            }
        });
    }

    // Load logs each time modal opens
    modalEl.addEventListener('show.bs.modal', loadAuditLogs);

    // Ensure backdrop is always below this modal so content remains interactive.
    modalEl.addEventListener('shown.bs.modal', function () {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const latestBackdrop = backdrops[backdrops.length - 1];
        if (latestBackdrop) {
            latestBackdrop.style.zIndex = '209000';
        }
        cleanupModalArtifacts();
    });

    // Prevent page lock after close.
    modalEl.addEventListener('hidden.bs.modal', cleanupModalArtifacts);

    function loadAuditLogs() {
        const body = document.getElementById('audit-log-body');
        body.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div><span class="small">Loading activity logs...</span></div>';

        fetch('{{ route("notifications.audit-logs") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.logs || data.logs.length === 0) {
                body.innerHTML = '<div class="text-center text-muted py-5"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" stroke-width="1.5" stroke="#adb5bd" fill="none" class="mb-2 d-block mx-auto"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="9" y1="12" x2="15" y2="12"/></svg><div class="small">No activity logs found.</div></div>';
                return;
            }

            const actionColors = { create: 'success', update: 'warning', delete: 'danger', user_login: 'info', view: 'secondary' };

            let html = '';
            data.logs.forEach(function (log, i) {
                const actionKey = (log.action || '').toLowerCase().replace(' ', '_');
                const color = actionColors[actionKey] || 'secondary';
                const border = i > 0 ? 'border-top' : '';

                html += '<div class="px-3 py-3 ' + border + '" style="border-color:#f1f3f5!important;">';
                html +=   '<div class="d-flex gap-3">';
                html +=     '<div class="flex-shrink-0 mt-1">';
                html +=       '<span class="d-flex align-items-center justify-content-center rounded-circle bg-' + color + '-lt" style="width:32px;height:32px;">';
                html +=         '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" class="text-' + color + '"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/></svg>';
                html +=       '</span>';
                html +=     '</div>';
                html +=     '<div style="flex:1;min-width:0;">';
                html +=       '<div class="d-flex align-items-center justify-content-between gap-2 mb-1">';
                html +=         '<div class="d-flex align-items-center gap-2">';
                html +=           '<span class="badge bg-' + color + '-lt text-' + color + ' text-uppercase" style="font-size:0.65rem;letter-spacing:0.04em;">' + escapeHtml(log.action) + '</span>';
                html +=           '<span class="fw-semibold" style="font-size:0.85rem;">' + escapeHtml(log.model_type) + '</span>';
                html +=         '</div>';
                html +=         '<span class="text-muted" style="font-size:0.72rem;white-space:nowrap;">' + escapeHtml(log.relative) + '</span>';
                html +=       '</div>';
                if (log.description) {
                    html +=   '<div style="font-size:0.82rem;color:#495057;">' + escapeHtml(log.description) + '</div>';
                }
                html +=       '<div class="mt-1 d-flex align-items-center gap-1 text-muted" style="font-size:0.75rem;">';
                html +=         '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>';
                html +=         '<span>' + escapeHtml(log.user) + '</span>';
                html +=         '<span>·</span>';
                html +=         '<span>' + escapeHtml(log.created_at) + '</span>';
                html +=       '</div>';
                html +=     '</div>';
                html +=   '</div>';
                html += '</div>';
            });
            body.innerHTML = html;
        })
        .catch(function () {
            body.innerHTML = '<div class="text-center text-muted py-5 small">Failed to load activity logs.</div>';
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endif
