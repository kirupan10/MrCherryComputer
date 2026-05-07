/**
 * Restaurant Modal Utilities
 * Provides universal modal close functionality for all restaurant views
 *
 * Features:
 * - ESC key to close any open modal
 * - X button (btn-close) closes modal
 * - Cancel/Close buttons close modal
 * - Backdrop click closes modal
 * - Programmatic close via closeModal() function
 */

(function() {
    'use strict';

    /**
     * Initialize modal close functionality when DOM is ready
     */
    function initModalCloseFunctionality() {
        const modals = document.querySelectorAll('.modal');

        modals.forEach(modal => {
            // Ensure modal has proper attributes
            if (!modal.hasAttribute('data-bs-keyboard')) {
                modal.setAttribute('data-bs-keyboard', 'true');
            }
            if (!modal.hasAttribute('data-bs-backdrop')) {
                modal.setAttribute('data-bs-backdrop', 'true');
            }

            // X button and close button handlers
            const closeButtons = modal.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]');
            closeButtons.forEach(btn => {
                // Add aria-label if missing
                if (btn.classList.contains('btn-close') && !btn.hasAttribute('aria-label')) {
                    btn.setAttribute('aria-label', 'Close');
                }

                // Ensure data-bs-dismiss is set
                if (!btn.hasAttribute('data-bs-dismiss')) {
                    btn.setAttribute('data-bs-dismiss', 'modal');
                }
            });

            // Backdrop click handler
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        // Global ESC key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    closeModal(openModal.id);
                }
            }
        });
    }

    /**
     * Close a modal by ID
     * @param {string} modalId - The ID of the modal to close
     */
    window.closeModal = function(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;

        // Try using Bootstrap Modal API first
        if (window.bootstrap && window.bootstrap.Modal) {
            const modalInstance = window.bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            } else {
                // If no instance exists, create one and hide
                const modal = new window.bootstrap.Modal(modalEl);
                modal.hide();
            }
        } else {
            // Fallback: Manual hide
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');

            // Remove backdrop if exists
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }

            // Remove modal-open class from body
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
    };

    /**
     * Open a modal by ID
     * @param {string} modalId - The ID of the modal to open
     */
    window.openModal = function(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;

        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            // Fallback: Manual show
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
            modalEl.setAttribute('aria-modal', 'true');

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);

            // Add modal-open class to body
            document.body.classList.add('modal-open');
        }
    };

    /**
     * Toggle modal visibility
     * @param {string} modalId - The ID of the modal to toggle
     */
    window.toggleModal = function(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;

        if (modalEl.classList.contains('show')) {
            closeModal(modalId);
        } else {
            openModal(modalId);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModalCloseFunctionality);
    } else {
        initModalCloseFunctionality();
    }

    // Re-initialize when new content is loaded (e.g., via Livewire)
    document.addEventListener('livewire:load', initModalCloseFunctionality);
    document.addEventListener('livewire:update', initModalCloseFunctionality);
})();
