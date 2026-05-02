<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pinjam Barang') }} - Dashboard</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary-color: #2C98A0;
                --sidebar-bg: #1c6d73; /* A bit darker teal to match the sidebar visually */
                --sidebar-hover: #175a5f;
                --bg-light: #d6eef0; /* Match the light blue background of the mockup */
                --text-dark: #2c3e50;
                --text-muted: #6c757d;
                --card-bg: #EAF4F6;
                --card-border: #DDEEF0;
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--bg-light);
                /* Adding a subtle gradient similar to the mockup background (Top/Bottom) */
                background: linear-gradient(135deg, #d3eaf2 0%, #a4cfd6 100%);
                background-attachment: fixed;
                color: var(--text-dark);
                overflow-x: hidden;
            }

            /* Container styling to look like a window inside the browser, matching mockup frame */
            #app-container {
                max-width: 1400px;
                margin: 20px auto;
                background-color: var(--bg-light);
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                display: flex;
                height: calc(100vh - 40px);
                overflow: hidden;
                border: 4px solid #1c2733; /* Outer dark border to mimic mockup */
            }

            /* Sidebar */
            #sidebar {
                width: 250px;
                min-width: 250px;
                background-color: #2F4F5B; /* Dark bluish sidebar color from mockup */
                color: #fff;
                transition: all 0.3s;
                display: flex;
                flex-direction: column;
                z-index: 1000;
                border-top-left-radius: 12px;
                border-bottom-left-radius: 12px;
            }

            /* User Profile in Sidebar */
            .sidebar-profile {
                padding: 40px 20px 20px;
                text-align: center;
            }
            .sidebar-profile img {
                width: 70px;
                height: 70px;
                border-radius: 50%;
                margin-bottom: 15px;
                border: 2px solid rgba(255,255,255,0.8);
                object-fit: cover;
            }
            .sidebar-profile h6 {
                margin: 0;
                font-size: 1rem;
                font-weight: 400;
                letter-spacing: 0.5px;
            }
            .sidebar-profile p {
                margin: 0;
                font-size: 0.75rem;
                color: rgba(255,255,255,0.6);
            }

            /* Sidebar Menu */
            .sidebar-menu {
                padding: 20px 10px;
                list-style: none;
                margin: 0;
                flex-grow: 1;
                overflow-y: auto;
            }
            .sidebar-menu li {
                margin-bottom: 5px;
            }
            .sidebar-menu a {
                display: flex;
                align-items: center;
                color: rgba(255,255,255,0.7);
                padding: 12px 20px;
                text-decoration: none;
                border-radius: 8px;
                transition: all 0.3s;
                font-size: 0.95rem;
                font-weight: 400;
            }
            .sidebar-menu a:hover, .sidebar-menu a.active {
                color: #2C98A0;
                background-color: rgba(255,255,255,0.9);
                font-weight: 500;
                box-shadow: inset 4px 0 0 #2C98A0;
                border-radius: 0 20px 20px 0;
            }
            .sidebar-menu a i {
                margin-right: 15px;
                width: 20px;
                text-align: center;
                font-size: 1.1rem;
            }

            /* Logout Button in Sidebar */
            .sidebar-footer {
                padding: 20px;
            }
            .sidebar-footer button {
                width: 100%;
                background: transparent;
                border: none;
                color: rgba(255,255,255,0.7);
                text-align: left;
                padding: 12px 20px;
                border-radius: 8px;
                transition: all 0.3s;
                font-weight: 400;
                display: flex;
                align-items: center;
            }
            .sidebar-footer button:hover {
                color: #fff;
                background-color: rgba(255,255,255,0.1);
            }
            .sidebar-footer button i {
                margin-right: 15px;
                width: 20px;
                text-align: center;
            }

            /* Main Content */
            #page-content-wrapper {
                flex-grow: 1;
                padding: 20px 30px;
                overflow-y: auto;
                background-color: #f1f8fa; /* Inner layout background */
                border-radius: 20px;
                margin: 10px 10px 10px 0; /* Margin between sidebar and content to look like panel inside app */
                box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
            }

            /* Top Navbar */
            .top-navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                padding: 10px 0;
            }
            .greeting-title {
                font-size: 1.6rem;
                font-weight: 600;
                color: var(--text-dark);
                margin: 0;
            }
            .top-controls {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            .search-bar {
                position: relative;
            }
            .search-bar input {
                border-radius: 20px;
                padding: 8px 15px 8px 35px;
                border: 1px solid #ced4da;
                outline: none;
                width: 300px;
                transition: box-shadow 0.3s;
                font-size: 0.9rem;
                background-color: #fff;
            }
            .search-bar input:focus {
                box-shadow: 0 0 5px rgba(44, 152, 160, 0.5);
                border-color: var(--primary-color);
            }
            .search-bar i.fa-search {
                position: absolute;
                top: 50%;
                left: 15px;
                transform: translateY(-50%);
                color: #a0a5aa;
            }
            .top-icons {
                display: flex;
                gap: 15px;
                background: #fff;
                border-radius: 25px;
                padding: 5px 15px;
                align-items: center;
                box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            }
            .top-icons .icon-btn {
                font-size: 1.1rem;
                color: #2F4F5B;
                cursor: pointer;
                transition: color 0.2s;
            }
            .top-icons .icon-btn:hover {
                color: var(--primary-color);
            }

            /* Card Styles */
            .custom-card {
                background-color: #fff;
                border-radius: 12px;
                border: none;
                box-shadow: 0 4px 15px rgba(44, 152, 160, 0.05); /* Slight teal tint in shadow */
                margin-bottom: 20px;
                height: 100%;
                transition: transform 0.2s;
            }
            .custom-card:hover {
                transform: translateY(-2px);
            }
            .custom-card .card-body {
                padding: 20px;
            }
            .custom-card .card-title {
                font-size: 1rem;
                font-weight: 500;
                color: #2F4F5B;
                margin-bottom: 15px;
            }

            /* Global Header matching the mock image text "Dashboard" and "2024" */
            .app-header {
                max-width: 1400px;
                margin: 0 auto;
                display: flex;
                justify-content: space-between;
                padding: 10px 20px;
                font-weight: 500;
                color: #1c2733;
                font-size: 1.2rem;
            }
            .app-title-central {
                text-align: center;
                color: #1c2733;
                font-size: 2.2rem;
                font-weight: 600;
                margin-bottom: 20px;
            }

            /* Responsive */
            @media (max-width: 992px) {
                #app-container {
                    flex-direction: column;
                    height: auto;
                    margin: 0;
                    border-radius: 0;
                    border: none;
                }
                #sidebar {
                    width: 100%;
                    min-height: auto;
                    border-radius: 0;
                }
                .sidebar-menu {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                }
                #page-content-wrapper {
                    margin: 0;
                    border-radius: 0;
                }
                .top-controls {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .search-bar input {
                    width: 100%;
                }
                .app-header {
                    display: none;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="app-header">
            <div>Dashboard</div>
            <div>{{ date('Y') }}</div>
        </div>
        <h1 class="app-title-central">Inventory Management</h1>
        
        <div id="app-container">
            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar-profile">
                    <!-- Default user avatar -->
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=E8F5F7&color=2C98A0" alt="Profile">
                    <h6>{{ Auth::user()->name ?? 'User Name' }}</h6>
                    <p>{{ Auth::user()->email ?? 'user@example.com' }}</p>
                </div>

                <ul class="sidebar-menu">
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-th-large"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                                <i class="fas fa-box-open"></i> Inventory
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'active' : '' }}">
                                <i class="fas fa-layer-group"></i> Orders
                            </a>
                        </li>
                        @can('viewAny', App\Models\Loan::class)
                        <li>
                            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Reporting
                            </a>
                        </li>
                        @endcan
                        @if(Auth::user() && Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> Kategori
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    @endauth
                </ul>

                <div class="sidebar-footer">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <i class="fas fa-power-off"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light w-100 mb-2">Login</a>
                    @endauth
                </div>
            </nav>

            <!-- Page Content -->
            <div id="page-content-wrapper">
                
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
</html>
