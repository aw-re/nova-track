// Common JavaScript functions for CPMS

/**
 * Initialize DataTables for tables with the 'datatable' class
 */
function initializeDataTables() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            },
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        });
    }
}

/**
 * Initialize Select2 for select elements with the 'select2' class
 */
function initializeSelect2() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
}

/**
 * Initialize date pickers for input elements with the 'datepicker' class
 */
function initializeDatepickers() {
    if (typeof $.fn.datepicker !== 'undefined') {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }
}

/**
 * Initialize tooltips for elements with the 'data-bs-toggle="tooltip"' attribute
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize popovers for elements with the 'data-bs-toggle="popover"' attribute
 */
function initializePopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Auto-hide alerts after a specified time
 * @param {number} timeout - Time in milliseconds before hiding alerts
 */
function autoHideAlerts(timeout = 5000) {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, timeout);
}

/**
 * Toggle sidebar visibility on mobile devices
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

/**
 * Initialize all common UI components
 */
function initializeUI() {
    initializeDataTables();
    initializeSelect2();
    initializeDatepickers();
    initializeTooltips();
    initializePopovers();
    autoHideAlerts();
}

// Initialize UI when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeUI();
});
