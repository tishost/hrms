// Owner Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuToggle = document.createElement('button');
    mobileMenuToggle.className = 'mobile-menu-toggle';
    mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.appendChild(mobileMenuToggle);

    const sidebar = document.querySelector('.sidebar');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    mobileMenuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // Submenu functionality
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.has-submenu');
            parent.classList.toggle('open');
        });
    });

    // Form input error handling
    document.querySelectorAll('.form-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var errorDiv = this.parentElement.querySelector('.input-error');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        });
    });
    
    // Toast auto-hide logic
    let toast = document.querySelector('.toast');
    if (toast) {
        setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () {
                toast.style.display = 'none';
            }, 500);
        }, 4000); // 4 second por hide hobe
    }
}); 