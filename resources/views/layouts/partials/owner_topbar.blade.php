<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h2>@yield('page-title', 'Dashboard')</h2>
    </div>
    <div class="topbar-right">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search...">
        </div>
        <div class="notification-badge">
            <i class="fas fa-bell"></i>
            <span class="badge">3</span>
        </div>
        <div class="user-menu" style="position: relative;">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User" class="user-avatar">
            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
            <input type="checkbox" id="userMenuToggle" style="display:none;">
            <label for="userMenuToggle" style="cursor:pointer; margin-left:6px;">
                <i class="fas fa-caret-down"></i>
            </label>
            <div class="dropdown-menu" style="display:none; position:absolute; right:0; top:110%; background:#fff; min-width:140px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-radius:8px; z-index:1000;">
                <a href="{{ route('profile.edit') }}" class="dropdown-item" style="display:block; padding:10px 16px; color:#333; text-decoration:none;">Profile</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="dropdown-item" style="display:block; width:100%; text-align:left; padding:10px 16px; background:none; border:none; color:#e53e3e; cursor:pointer;">Logout</button>
                </form>
            </div>
            <style>
                #userMenuToggle:checked + label + .dropdown-menu {
                    display: block !important;
                }
            </style>
        </div>
    </div>
</div>
