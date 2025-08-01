/**
 * Password Toggle Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const passwordAddons = document.querySelectorAll('.password-addon');
    
    passwordAddons.forEach(function(addon) {
        addon.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'ri-eye-off-fill align-middle';
            } else {
                passwordInput.type = 'password';
                icon.className = 'ri-eye-fill align-middle';
            }
        });
    });
});
