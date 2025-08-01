/**
 * Theme Layout Configuration
 */

'use strict';

var LayoutThemeApp = function () {
    
    var initLayout = function() {
        // Initialize layout configurations
        var layout = localStorage.getItem('data-layout') || 'vertical';
        var topbar = localStorage.getItem('data-topbar') || 'light';
        var sidebar = localStorage.getItem('data-sidebar') || 'dark';
        var sidebarSize = localStorage.getItem('data-sidebar-size') || 'lg';
        
        // Apply configurations
        if (document.documentElement) {
            document.documentElement.setAttribute('data-layout', layout);
            document.documentElement.setAttribute('data-topbar', topbar);
            document.documentElement.setAttribute('data-sidebar', sidebar);
            document.documentElement.setAttribute('data-sidebar-size', sidebarSize);
        }
    };
    
    var initSwitcher = function() {
        // Theme switcher functionality (if needed)
        console.log('Layout initialized');
    };
    
    return {
        init: function () {
            initLayout();
            initSwitcher();
        }
    };
}();

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    LayoutThemeApp.init();
});

// Export for global use
window.LayoutThemeApp = LayoutThemeApp;
