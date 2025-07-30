document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dashboard functionality

    // 1. Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('ownerSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            localStorage.setItem('sidebarState', sidebar.classList.contains('show') ? 'expanded' : 'collapsed');
        });

        // Check localStorage for saved state
        if (window.innerWidth >= 992) {
            const savedState = localStorage.getItem('sidebarState');
            if (savedState === 'collapsed') {
                sidebar.classList.remove('show');
            }
        }
    }

    // 2. Initialize Charts
    initializeCharts();

    // 3. Notification Badge Animation
    animateNotificationBadge();

    // 4. Active Menu Item Highlighting
    highlightActiveMenu();
});

function initializeCharts() {
    // Line Chart - Website Visitors
    const lineCtx = document.getElementById('visitorChart');
    if (lineCtx) {
        const visitorData = JSON.parse(lineCtx.dataset.visitors || '[]');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Visitors',
                    data: visitorData,
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: getChartOptions('Website Visitors')
        });
    }

    // Donut Chart - Customer Segments
    const donutCtx = document.getElementById('customerChart');
    if (donutCtx) {
        const segmentData = JSON.parse(donutCtx.dataset.segments || '[]');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['North America', 'Europe', 'Asia', 'South America', 'Africa', 'Oceania'],
                datasets: [{
                    data: segmentData,
                    backgroundColor: [
                        '#4361ee',
                        '#3f37c9',
                        '#4895ef',
                        '#4cc9f0',
                        '#f72585',
                        '#7209b7'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                cutout: '70%'
            }
        });
    }
}

function getChartOptions(title) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#2c3e50',
                titleFont: {
                    weight: 'bold'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    };
}

function animateNotificationBadge() {
    const badge = document.querySelector('.notification-badge .badge');
    if (badge) {
        // Pulse animation for new notifications
        badge.style.animation = 'pulse 2s infinite';

        // Click handler for notifications
        document.querySelector('.notification-badge').addEventListener('click', function() {
            badge.style.animation = 'none';
            setTimeout(() => {
                badge.style.animation = 'pulse 2s infinite';
            }, 100);
        });
    }
}

function highlightActiveMenu() {
    // Automatically expand parent menu if child is active
    const activeSubmenuItems = document.querySelectorAll('.list-unstyled .active');
    activeSubmenuItems.forEach(item => {
        const parentMenu = item.closest('.collapse');
        if (parentMenu) {
            parentMenu.classList.add('show');
            const parentToggle = document.querySelector(`[href="#${parentMenu.id}"]`);
            if (parentToggle) {
                parentToggle.classList.add('bg-light', 'fw-bold', 'text-primary');
                parentToggle.setAttribute('aria-expanded', 'true');
            }
        }
    });

    // Subscription submenu functionality
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.has-submenu');
            const submenu = parent.querySelector('.submenu');
            const arrow = this.querySelector('.submenu-arrow');

            // Close other submenus
            document.querySelectorAll('.has-submenu').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('open');
                    const otherArrow = item.querySelector('.submenu-arrow');
                    if (otherArrow) {
                        otherArrow.style.transform = 'rotate(0deg)';
                    }
                }
            });

            // Toggle current submenu
            parent.classList.toggle('open');
            if (arrow) {
                arrow.style.transform = parent.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        });
    });
}

// Add to your CSS or create animations.css
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
