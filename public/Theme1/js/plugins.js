/**
 * Main Application Plugins
 */

// Basic plugin initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Plugins loaded');
    
    // Initialize tooltips if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Auto-focus on first error field
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        firstError.focus();
    }
});
