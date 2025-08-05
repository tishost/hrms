<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="fas fa-rocket"></i>
            <span>AdminPro</span>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
            <a href="{{ route('owner.dashboard') }}">
                <span class="menu-icon">🏠</span>
                Dashboard
            </a>
        </li>
        <li class="has-submenu {{ request()->routeIs('owner.property.*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="submenu-toggle">
                <span class="menu-icon">🏢</span>
                My Property
                <span class="submenu-arrow">▼</span>
            </a>
            <ul class="submenu">
                <li class="{{ request()->routeIs('owner.property.index') ? 'active' : '' }}">
                    <a href="{{ route('owner.property.index') }}">📋 Property List</a>
                </li>
                <li class="{{ request()->routeIs('owner.property.create') ? 'active' : '' }}">
                    <a href="{{ route('owner.property.create') }}">➕ Add Property</a>
                </li>
            </ul>
        </li>
        <li class="has-submenu {{ request()->routeIs('owner.units.*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="submenu-toggle">
                <span class="menu-icon">🏬</span>
                Units
                <span class="submenu-arrow">▼</span>
            </a>
            <ul class="submenu">
                <li class="{{ request()->routeIs('owner.units.index') ? 'active' : '' }}">
                    <a href="{{ route('owner.units.index') }}">🏢 Unit List</a>
                </li>
            </ul>
        </li>
        <li class="has-submenu {{ request()->routeIs('owner.tenants.*') || request()->routeIs('owner.checkouts.*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="submenu-toggle">
                <span class="menu-icon">👥</span>
                Tenants
                <span class="submenu-arrow">▼</span>
            </a>
            <ul class="submenu">
                <li class="{{ request()->routeIs('owner.tenants.index') ? 'active' : '' }}">
                    <a href="{{ route('owner.tenants.index') }}">👥 Tenant List</a>
                </li>
                <li class="{{ request()->routeIs('owner.tenants.create') ? 'active' : '' }}">
                    <a href="{{ route('owner.tenants.create') }}">➕ Add Tenant</a>
                </li>
                <li class="{{ request()->routeIs('owner.checkouts.*') ? 'active' : '' }}">
                    <a href="{{ route('owner.checkouts.index') }}">🚪 Checkouts</a>
                </li>
            </ul>
        </li>
        <li class="has-submenu {{ request()->routeIs('owner.rent_payments.*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="submenu-toggle">
                <span class="menu-icon">💳</span>
                Billing
                <span class="submenu-arrow">▼</span>
            </a>
            <ul class="submenu">
                <li class="{{ request()->routeIs('owner.invoices.index') ? 'active' : '' }}">
                    <a href="{{ route('owner.invoices.index') }}">💰 Rent Collection</a>
                </li>
            </ul>
        </li>

        <li class="has-submenu {{ request()->routeIs('owner.subscription.*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="submenu-toggle">
                <span class="menu-icon">📦</span>
                Subscription
                <span class="submenu-arrow">▼</span>
            </a>
            <ul class="submenu">
                <li class="{{ request()->routeIs('owner.subscription.current') ? 'active' : '' }}">
                    <a href="{{ route('owner.subscription.current') }}">📊 Current Plan</a>
                </li>
                <li class="{{ request()->routeIs('owner.subscription.plans') ? 'active' : '' }}">
                    <a href="{{ route('owner.subscription.plans') }}">🛒 Available Plans</a>
                </li>
                <li class="{{ request()->routeIs('owner.subscription.billing') ? 'active' : '' }}">
                    <a href="{{ route('owner.subscription.billing') }}">💳 Billing History</a>
                </li>
                <li class="{{ request()->routeIs('owner.subscription.payment') ? 'active' : '' }}">
                    <a href="{{ route('owner.subscription.payment') }}">💸 Payment Methods</a>
                </li>
            </ul>
        </li>

        <li class="has-submenu {{ request()->routeIs('owner.settings.*') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="submenu-toggle">
                <span class="menu-icon">⚙️</span>
                Settings
                <span class="submenu-arrow">▼</span>
            </a>
            <ul class="submenu">
                <li class="{{ request()->routeIs('owner.settings.backup') ? 'active' : '' }}">
                    <a href="{{ route('owner.settings.backup') }}">💾 Backup Settings</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('owner.backups.*') ? 'active' : '' }}">
            <a href="{{ route('owner.backups.index') }}">
                <span class="menu-icon">💾</span>
                My Backups
            </a>
        </li>
    </ul>
</div>
<script>
document.querySelectorAll('.submenu-toggle').forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        const parent = this.closest('.has-submenu');
        parent.classList.toggle('open');
    });
});
</script>
