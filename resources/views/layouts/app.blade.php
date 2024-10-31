<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Punto de Venta') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --secondary-color: #0ea5e9;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #f8fafc;
            --surface-color: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --sidebar-width: 280px;
            --header-height: 70px;
            --border-radius: 16px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
        }

        .pos-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .pos-layout .sidebar {
            width: var(--sidebar-width);
            background: var(--surface-color);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .pos-layout .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .pos-layout .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .pos-layout .sidebar-brand i {
            font-size: 2rem;
        }

        .pos-layout .nav-section {
            padding: 1.5rem 1rem;
        }

        .pos-layout .nav-section-title {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 0.5rem 0.75rem;
            margin: 0;
        }

        .pos-layout .nav-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pos-layout .nav-item {
            margin: 0.25rem 0;
        }

        .pos-layout .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            gap: 0.75rem;
        }

        .pos-layout .nav-link:hover {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .pos-layout .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .pos-layout .nav-link i {
            font-size: 1.25rem;
        }

        /* Main Content Styles */
        .pos-layout .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            background-color: var(--background-color);
        }

        .pos-layout .header {
            height: var(--header-height);
            background: var(--surface-color);
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .pos-layout .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .pos-layout .search-bar {
            display: flex;
            align-items: center;
            background: var(--background-color);
            border-radius: 2rem;
            padding: 0.5rem 1rem;
            width: 300px;
        }

        .pos-layout .search-bar input {
            border: none;
            background: none;
            padding: 0.5rem;
            width: 100%;
            outline: none;
            color: var(--text-primary);
        }

        .pos-layout .search-bar i {
            color: var(--text-secondary);
        }

        .pos-layout .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pos-layout .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pos-layout .header-icon:hover {
            background-color: var(--background-color);
            color: var(--primary-color);
        }

        .pos-layout .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 2rem;
            transition: all 0.2s ease;
        }

        .pos-layout .user-profile:hover {
            background-color: var(--background-color);
        }

        .pos-layout .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .pos-layout .user-info {
            display: none;
        }

        .pos-layout .user-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .pos-layout .user-role {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .pos-layout .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: var(--surface-color);
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            padding: 0.5rem;
            min-width: 200px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s ease;
        }

        .pos-layout .dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .pos-layout .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 0.25rem;
            gap: 0.75rem;
        }

        .pos-layout .dropdown-item:hover {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .pos-layout .content {
            padding: 2rem;
        }

        /* Stats Cards */
        .pos-layout .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .pos-layout .stat-card {
            background: var(--surface-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .pos-layout .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .pos-layout .stat-icon.sales {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .pos-layout .stat-icon.products {
            background: rgba(14, 165, 233, 0.1);
            color: var(--secondary-color);
        }

        .pos-layout .stat-icon.customers {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .pos-layout .stat-icon.revenue {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .pos-layout .stat-content {
            flex: 1;
        }

        .pos-layout .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .pos-layout .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* View Content Container */
        .pos-layout .view-content {
            padding: 2rem;
            /* Resetea estilos para el contenido interno */
            > * {
                font-family: inherit;
                color: inherit;
            }
        }

        /* Responsive Design */
        @media (min-width: 768px) {
            .pos-layout .user-info {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .pos-layout .sidebar {
                transform: translateX(-100%);
            }

            .pos-layout .sidebar.active {
                transform: translateX(0);
            }

            .pos-layout .main-content {
                margin-left: 0;
            }

            .pos-layout .header {
                padding: 0 1rem;
            }

            .pos-layout .search-bar {
                display: none;
            }

            .pos-layout .content {
                padding: 1rem;
            }
        }

        /* Menu Toggle Button for Mobile */
        .pos-layout .menu-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .pos-layout .menu-toggle {
                display: flex;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="pos-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ url('/') }}" class="sidebar-brand">
                    <i class="material-icons-round">storefront</i>
                    <span>POS System</span>
                </a>
            </div>

            <nav class="nav-section">
                <h3 class="nav-section-title">Principal</h3>
                <ul class="nav-items">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                <i class="material-icons-round">point_of_sale</i>
                                <span>Punto de Venta</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="material-icons-round">inventory_2</i>
                                <span>Productos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="material-icons-round">category</i>
                                <span>Categorías</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                                <i class="material-icons-round">analytics</i>
                                <span>Ventas</span>
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>

            <nav class="nav-section">
                <h3 class="nav-section-title">Gestión</h3>
                <ul class="nav-items">
                    <li class="nav-item">
                        <a class="nav-link" href="#reports">
                            <i class="material-icons-round">summarize</i>
                            <span>Reportes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#inventory">
                            <i class="material-icons-round">inventory</i>
                            <span>Inventario</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#customers">
                            <i class="material-icons-round">people</i>
                            <span>Clientes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav class="nav-section">
                <h3 class="nav-section-title">Configuración</h3>
                <ul class="nav-items">
                    <li class="nav-item">
                        <a class="nav-link" href="#settings">
                            <i class="material-icons-round">settings</i>
                            <span>Ajustes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#profile">
                            <i class="material-icons-round">account_circle</i>
                            <span>Perfil</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-content">
                    <div class="search-bar">
                        <i class="material-icons-round">search</i>
                        <input type="text" placeholder="Buscar productos, ventas, clientes...">
                    </div>

                    <div class="header-actions">
                        <div class="header-icon">
                            <i class="material-icons-round">notifications</i>
                        </div>
                        <div class="header-icon">
                            <i class="material-icons-round">help</i>
                        </div>
                        
                        @auth
                        <div class="user-profile dropdown">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">Administrador</div>
                            </div>
                            <div class="dropdown-menu">
                                <a href="#profile" class="dropdown-item">
                                    <i class="material-icons-round">person</i>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="#settings" class="dropdown-item">
                                    <i class="material-icons-round">settings</i>
                                    <span>Configuración</span>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="{{ route('logout') }}" class="dropdown-item"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="material-icons-round">logout</i>
                                    <span>Cerrar Sesión</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon sales">
                            <i class="material-icons-round">payments</i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">$15,890</div>
                            <div class="stat-label">Ventas de Hoy</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon products">
                            <i class="material-icons-round">inventory_2</i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">245</div>
                            <div class="stat-label">Total Productos</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon customers">
                            <i class="material-icons-round">groups</i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">1,234</div>
                            <div class="stat-label">Clientes</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="material-icons-round">trending_up</i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">$45,678</div>
                            <div class="stat-label">Ingresos Mensuales</div>
                        </div>
                    </div>
                </div>

                <!-- View Content Container -->
                <div class="view-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')

    <script>
        // Toggle dropdown
        document.querySelector('.user-profile').addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-profile')) {
                document.querySelector('.user-profile')?.classList.remove('active');
            }
        });

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'menu-toggle';
            toggleBtn.innerHTML = '<i class="material-icons-round">menu</i>';
            
            document.body.appendChild(toggleBtn);

            toggleBtn.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>