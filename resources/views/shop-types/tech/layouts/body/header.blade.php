
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
                          style="width: 32px !important; height: 32px !important; min-width: 32px !important; min-height: 32px !important; max-width: 32px !important; max-height: 32px !important; background-image: url({{ Avatar::create(Auth::user()->name)->toBase64() }}); background-size: cover; background-position: center;">
                    </span>
                @endif
                {{-- <div class="d-none d-xl-block">
                    <div class="small text-muted">{{ Auth::user()->name }}</div>
                </div> --}}
            </div>

            @if(!Auth::user()->isEmployee())
            {{-- Notification Bell --}}
            <div class="nav-item dropdown" id="notif-dropdown-wrapper">
                <a href="#" class="nav-link p-0 position-relative d-flex align-items-center justify-content-center"
                   id="notif-bell-toggle"
                   style="width:40px;height:40px;"
                   data-bs-toggle="dropdown"
                   aria-expanded="false"
                   aria-label="Notifications">
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
                    @else
                    <span class="badge bg-danger badge-pill position-absolute d-none"
                          id="notif-badge"
                          style="top:2px;right:2px;min-width:18px;height:18px;font-size:10px;padding:2px 4px;border-radius:50px;">
                        0
                    </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow" id="notif-dropdown"
                     style="width:340px;max-height:420px;overflow-y:auto;padding:0;">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <span class="fw-semibold" style="font-size:0.9rem;">Price Update Alerts</span>
                        <button class="btn btn-sm btn-link p-0 text-muted" id="notif-mark-all-read" style="font-size:0.8rem;">
                            Mark all read
                        </button>
                    </div>
                    <div id="notif-list" style="min-height:60px;">
                        <div class="text-center text-muted py-4 small">Loading...</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="nav-item">
                <form action="{{ route('logout') }}" method="post" class="m-0" style="display: inline;">
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
    const bell       = document.getElementById('notif-bell-toggle');
    const badge      = document.getElementById('notif-badge');
    const list       = document.getElementById('notif-list');
    const markAllBtn = document.getElementById('notif-mark-all-read');
    let loaded       = false;

    if (!bell) return;

    bell.addEventListener('shown.bs.dropdown', function () {
        if (loaded) return;
        loaded = true;
        loadNotifications();
    });

    // Bootstrap may not be ready on first render; also handle click fallback
    bell.addEventListener('click', function () {
        if (!loaded) {
            setTimeout(function () {
                if (!loaded) { loaded = true; loadNotifications(); }
            }, 150);
        }
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('{{ route("notifications.mark-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(function () {
                badge.classList.add('d-none');
                document.querySelectorAll('.notif-item.unread').forEach(function (el) {
                    el.classList.remove('unread');
                    el.style.background = '';
                });
            });
        });
    }

    function loadNotifications() {
        fetch('{{ route("notifications.index") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            // Update badge
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }

            if (!data.notifications || data.notifications.length === 0) {
                list.innerHTML = '<div class="text-center text-muted py-4 small">No price update notifications</div>';
                return;
            }

            let html = '';
            data.notifications.forEach(function (n) {
                const d = n.data;
                const isUnread = !n.read_at;
                const bg = isUnread ? 'background:#f0f7ff;' : '';
                const dot = isUnread ? '<span class="badge bg-blue-lt ms-1" style="font-size:9px;padding:2px 5px;">NEW</span>' : '';
                const oldS = d.old_selling_price !== null && d.old_selling_price !== undefined ? 'LKR ' + parseFloat(d.old_selling_price).toFixed(2) : '-';
                const newS = 'LKR ' + parseFloat(d.new_selling_price).toFixed(2);

                html += '<div class="notif-item px-3 py-2 border-bottom ' + (isUnread ? 'unread' : '') + '" style="' + bg + 'font-size:0.83rem;">';
                html +=   '<div class="d-flex align-items-start justify-content-between gap-2">';
                html +=     '<div style="flex:1;min-width:0;">';
                html +=       '<div class="fw-semibold text-truncate">' + escapeHtml(d.product_name) + ' <span class="text-muted fw-normal">(' + escapeHtml(d.product_code) + ')</span>' + dot + '</div>';
                html +=       '<div class="text-muted" style="font-size:0.78rem;">by <strong>' + escapeHtml(d.updated_by) + '</strong></div>';
                html +=       '<div class="mt-1">Selling: <span class="text-muted text-decoration-line-through">' + oldS + '</span> → <span class="text-success fw-semibold">' + newS + '</span></div>';
                html +=     '</div>';
                html +=     '<div class="text-muted text-end flex-shrink-0" style="font-size:0.75rem;white-space:nowrap;">' + n.created_at + '</div>';
                html +=   '</div>';
                html += '</div>';
            });
            list.innerHTML = html;
        })
        .catch(function () {
            list.innerHTML = '<div class="text-center text-muted py-4 small">Failed to load notifications</div>';
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    // Poll badge count every 60 seconds (lightweight)
    setInterval(function () {
        fetch('{{ route("notifications.index") }}', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.classList.remove('d-none');
                loaded = false; // force reload on next open
            } else {
                badge.classList.add('d-none');
            }
        });
    }, 60000);
});
</script>
@endif
