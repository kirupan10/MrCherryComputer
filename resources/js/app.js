import './bootstrap';


// Disable mouse wheel changing number input values globally
document.addEventListener('wheel', function(e) {
    const activeEl = document.activeElement;
    if (activeEl && activeEl.tagName === 'INPUT' && activeEl.type === 'number') {
        e.preventDefault();
    }
}, { passive: false, capture: true });

// ESC and close button support for all modals
function enableGlobalModalClose() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            // Find all open modals
            document.querySelectorAll('.modal.show').forEach(function(modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalEl).modal('hide');
                } else {
                    modalEl.style.display = 'none';
                    modalEl.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            });
        }
    });

    // Bind close buttons
    document.querySelectorAll('.modal .btn-close').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalEl = btn.closest('.modal');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalEl).modal('hide');
                } else {
                    modalEl.style.display = 'none';
                    modalEl.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', enableGlobalModalClose);
