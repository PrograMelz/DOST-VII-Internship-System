<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @php
        $systemSettings = \App\Models\SystemSetting::query()->first();
        $primaryColor = optional($systemSettings)->primary_color ?? '#2E86C1';
        $secondaryColor = optional($systemSettings)->secondary_color ?? '#1B4F72';
        $buttonColor = optional($systemSettings)->button_color ?? '#3498DB';
        $headingTextColor = optional($systemSettings)->heading_text_color ?? '#333333';
        $bodyTextColor = optional($systemSettings)->body_text_color ?? '#333333';
    @endphp
    @if ($systemSettings && $systemSettings->system_logo)
        <link rel="icon" href="{{ asset($systemSettings->system_logo) }}">
    @endif
    <title>{{ $systemSettings->system_short_name ?? 'Internship System' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
            --button-color: {{ $buttonColor }};
            --heading-text-color: {{ $headingTextColor }};
            --body-text-color: {{ $bodyTextColor }};
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--secondary-color);
            color: var(--body-text-color);
        }

        /* App Shell */
        .app-shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .main-panel {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* Navbar Styles (content panel only) */
        .admin-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color) 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
        }

        .admin-navbar .navbar-brand {
            font-size: 20px;
            font-weight: 700;
            color: white !important;
        }

        .user-info-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar-small {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 16px;
        }

        .user-details {
            color: white;
        }

        .user-details .user-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-details .user-email {
            font-size: 12px;
            opacity: 0.8;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 0;
            overflow-y: auto;
            box-shadow: 3px 0 8px rgba(0, 0, 0, 0.15);
            height: 100vh;
            transition: width 0.2s ease, box-shadow 0.2s ease;
        }

        .sidebar-top {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px 14px 16px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0;
            min-width: 0;
            flex: 1;
            text-decoration: none;
            color: var(--heading-text-color);
        }

        .sidebar-brand:hover {
            color: var(--heading-text-color);
        }

        .sidebar-brand img {
            height: 34px;
            width: auto;
        }

        .sidebar-brand .brand-text {
            font-weight: 800;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-header {
            display: none;
        }

        .sidebar-collapse-btn {
            border: 1px solid #e9ecef;
            background: white;
            color: #666;
            border-radius: 8px;
            width: 40px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .sidebar-collapse-btn:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu .menu-item {
            margin: 0;
        }

        .sidebar-menu .menu-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .sidebar-menu .menu-link:hover {
            background:var(--secondary-color);
            color: var(--button-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-menu .menu-link.active {
            background:var(--secondary-color);
            color: var(--button-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-menu .menu-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        body.sidebar-collapsed .admin-sidebar {
            width: 76px;
            padding: 0 0 1rem 0;
        }

        body.sidebar-collapsed .sidebar-header {
            justify-content: center;
        }

        body.sidebar-collapsed .sidebar-top {
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 14px 16px 14px 16px;
        }

        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            flex: 0;
        }

        body.sidebar-collapsed .sidebar-collapse-btn {
            width: 44px;
        }

        body.sidebar-collapsed .sidebar-brand .brand-text {
            display: none;
        }

        body.sidebar-collapsed .sidebar-menu .menu-link {
            justify-content: center;
            gap: 0;
        }

        body.sidebar-collapsed .sidebar-menu .menu-link span {
            display: none;
        }

        body.sidebar-collapsed .sidebar-menu .menu-link i {
            width: auto;
        }

        /* Content Area */
        .admin-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            height: 100%;
            color: var(--body-text-color);
        }

        .admin-content .text-white {
            color: var(--body-text-color) !important;
        }

        .content-header {
            margin-bottom: 1rem;
        }

        .content-header h1 {
            font-size: 28px;
            color: var(--heading-text-color);
            margin-bottom: 0.5rem;
        }

        .content-header p {
            color: var(--body-text-color);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body.sidebar-collapsed .sidebar-top {
                justify-content: flex-start;
                padding: 14px 16px 14px 16px;
            }

            body.sidebar-collapsed .sidebar-brand {
                justify-content: flex-start;
            }

            body.sidebar-collapsed .sidebar-brand .brand-text {
                display: inline;
            }

            body.sidebar-collapsed .admin-sidebar {
                width: 280px;
                padding: 0 0 0 0;
            }

            body.sidebar-collapsed .sidebar-menu .menu-link {
                justify-content: flex-start;
                gap: 15px;
                padding: 14px 16px 14px 16px;
            }

            body.sidebar-collapsed .sidebar-menu .menu-link span {
                display: inline;
            }

            body.sidebar-collapsed .sidebar-menu .menu-link i {
                width: 24px;
            }

            .admin-sidebar {
                position: fixed;
                left: -280px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                top: 0;
            }

            .admin-sidebar.show {
                left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
        }

        /* Cards and Components */
        .card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: var(--button-color);
            border-color: var(--button-color);
        }

        .btn-primary:hover {
            background: var(--button-color);
            border-color: var(--button-color);
            filter: brightness(0.9);
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .user-details .user-name,
            .user-details .user-email {
                font-size: 12px;
            }

            .user-info-nav {
                gap: 10px;
            }
        }
    </style>

    @yield('extra-css')
</head>
<body>
    @php
        $systemSettings = \App\Models\SystemSetting::query()->first();
    @endphp
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-top">
                <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
                    @if ($systemSettings && $systemSettings->system_logo)
                        <img src="{{ asset($systemSettings->system_logo) }}" alt="Logo">
                    @else
                        <i class="bi bi-graph-up" style="font-size: 24px;"></i>
                    @endif
                    <span class="brand-text">{{ $systemSettings->system_short_name ?? 'Admin' }}</span>
                </a>
                <button type="button" class="sidebar-collapse-btn d-none d-md-inline-flex" id="sidebarCollapseBtn" aria-label="Collapse sidebar">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.interns-list') }}" class="menu-link {{ request()->routeIs('admin.interns-list', 'admin.intern-detail') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Interns</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.attendance-report') }}" class="menu-link {{ request()->routeIs('admin.attendance-report') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Attendance</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.holidays') }}" class="menu-link {{ request()->routeIs('admin.holidays') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>
                        <span>Holidays</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.system-settings') }}" class="menu-link {{ request()->routeIs('admin.system-settings') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span>System Settings</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.admins') }}" class="menu-link {{ request()->routeIs('admin.admins') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i>
                        <span>Admins</span>
                    </a>
                </li>
            </ul>
        </aside>

        <div class="main-panel">
            <!-- Top Navbar (content only) -->
            <nav class="navbar navbar-dark admin-navbar">
                <div class="container-fluid">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-link text-white sidebar-toggle d-md-none" id="sidebarToggle">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="user-info-nav">
                            <div class="user-avatar-small">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-email">{{ Auth::user()->email }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.logout') }}" class="mb-0">
                                @csrf
                                <button type="submit" class="logout-btn">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 3000, timerProgressBar: true, showConfirmButton: true });
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), showConfirmButton: true });
            @endif
            @if ($errors->any())
                Swal.fire({ icon: 'error', title: 'Validation Error', text: @json($errors->first()), showConfirmButton: true });
            @endif
        });
    </script>
    <script>
        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
        const SIDEBAR_STATE_KEY = 'admin_sidebar_collapsed';

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                adminSidebar.classList.toggle('show');
            });
        }

        function setCollapsedSidebar(isCollapsed) {
            document.body.classList.toggle('sidebar-collapsed', isCollapsed);
            if (sidebarCollapseBtn) {
                const icon = sidebarCollapseBtn.querySelector('i');
                if (icon) {
                    icon.className = isCollapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
                }
                sidebarCollapseBtn.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
                sidebarCollapseBtn.title = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
            }
        }

        try {
            const saved = localStorage.getItem(SIDEBAR_STATE_KEY);
            if (saved === '1') {
                setCollapsedSidebar(true);
            }
        } catch (e) {
            // ignore
        }

        if (sidebarCollapseBtn) {
            sidebarCollapseBtn.addEventListener('click', function() {
                const isCollapsed = !document.body.classList.contains('sidebar-collapsed');
                setCollapsedSidebar(isCollapsed);
                try {
                    localStorage.setItem(SIDEBAR_STATE_KEY, isCollapsed ? '1' : '0');
                } catch (e) {
                    // ignore
                }
            });
        }

        // Close sidebar when clicking on a link (mobile)
        if (window.innerWidth < 768) {
            const sidebarLinks = document.querySelectorAll('.sidebar-menu .menu-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    adminSidebar.classList.remove('show');
                });
            });
        }
    </script>
    @yield('extra-js')
</body>
</html>
