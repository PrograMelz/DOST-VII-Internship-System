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

        $internId = session('intern_id');
        $intern = $internId ? \App\Models\Intern::query()->find($internId) : null;
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

        .intern-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color) 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
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

        .intern-sidebar {
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
            color: var(--primary-color);
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

        .sidebar-menu .menu-link:hover,
        .sidebar-menu .menu-link.active {
            background: var(--secondary-color);
            color: var(--button-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-menu .menu-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        body.sidebar-collapsed .intern-sidebar {
            width: 76px;
            padding: 0 0 1rem 0;
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

        .intern-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            height: 100%;
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
            color: #666;
            font-size: 14px;
        }

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

            body.sidebar-collapsed .intern-sidebar {
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

            .intern-sidebar {
                position: fixed;
                left: -280px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                top: 0;
            }

            .intern-sidebar.show {
                left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
        }
    </style>

    @yield('extra-css')
</head>
<body>
<div class="app-shell">
    <aside class="intern-sidebar" id="internSidebar">
        <div class="sidebar-top">
            <a class="sidebar-brand" href="{{ route('intern.dashboard') }}">
                @if ($systemSettings && $systemSettings->system_logo)
                    <img src="{{ asset($systemSettings->system_logo) }}" alt="Logo">
                @else
                    <i class="bi bi-mortarboard" style="font-size: 24px;"></i>
                @endif
                <span class="brand-text">{{ $systemSettings->system_short_name ?? 'Internship System' }}</span>
            </a>
            <button type="button" class="sidebar-collapse-btn d-none d-md-inline-flex" id="sidebarCollapseBtn" aria-label="Collapse sidebar">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('intern.dashboard') }}" class="menu-link {{ request()->routeIs('intern.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('intern.calendar') }}" class="menu-link {{ request()->routeIs('intern.calendar') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendar</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('intern.dtr') }}" class="menu-link {{ request()->routeIs('intern.dtr') ? 'active' : '' }}">
                    <i class="bi bi-download"></i>
                    <span>DTR</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('intern.profile') }}" class="menu-link {{ request()->routeIs('intern.profile') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i>
                    <span>Personal Details</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-panel">
        <nav class="navbar navbar-dark intern-navbar">
            <div class="container-fluid">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-link text-white sidebar-toggle d-md-none" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center">
                    <div class="user-info-nav">
                        <div class="user-avatar-small" id="internNavbarAvatar">
                            {{ $intern ? mb_substr($intern->full_name, 0, 1) : 'I' }}
                        </div>
                        <div class="user-details">
                            <div class="user-name" id="internNavbarName">{{ $intern?->full_name ?? 'Intern' }}</div>
                            <div class="user-email">{{ $intern?->email ?? '' }}</div>
                        </div>
                        <form method="POST" action="{{ route('intern.logout') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="intern-content">
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
    const sidebarToggle = document.getElementById('sidebarToggle');
    const internSidebar = document.getElementById('internSidebar');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const SIDEBAR_STATE_KEY = 'intern_sidebar_collapsed';

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            internSidebar.classList.toggle('show');
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

    if (window.innerWidth < 768) {
        const sidebarLinks = document.querySelectorAll('.sidebar-menu .menu-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                internSidebar.classList.remove('show');
            });
        });
    }
</script>
@yield('extra-js')
</body>
</html>

