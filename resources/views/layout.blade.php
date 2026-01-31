<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ABC Academy</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>ABC Academy</h1>
            </div>
            <nav class="nav">
                <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="/about" class="{{ request()->is('about*') ? 'active' : '' }}">About</a>
                <a href="/programs" class="{{ request()->is('programs*') ? 'active' : '' }}">Programs</a>
                <a href="/courses" class="{{ request()->is('courses*') ? 'active' : '' }}">Courses</a>
                @auth
                    <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
                    @if(auth()->user()->role === 'Admin')
                    <a href="/reports" class="{{ request()->is('reports*') ? 'active' : '' }}">Reports</a>
                    @endif
                    @if (auth()->user()->role === 'Admin')
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle">Admin</a>
                            <div class="dropdown-menu">
                                <a href="/admin/programs">Programs</a>
                                <a href="/admin/courses">Courses</a>
                                <a href="/admin/units">Units</a>
                                <hr style="margin: 8px 0; border: none; border-top: 1px solid #eee;">
                                <a href="/admin/about">About Page</a>
                            </div>
                        </div>
                    @endif
                @endauth
            </nav>
            <div class="user-info">
                @auth
                    <a href="{{ route('profile.edit') }}" class="user-avatar-link">
                        <div class="user-avatar ">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    </a>
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-success">Register</a>
                @endauth
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success fade-in">
                    <span>✓</span>
                    {{ session('status') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-main">
                <div class="footer-section">
                    <h3>ABC Academy</h3>
                    <p>Empowering students through quality education</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/">Home</a></li>
                        <li><a href="/about">About</a></li>
                        <li><a href="/programs">Programs</a></li>
                        <li><a href="/courses">Courses</a></li>
                        <li><a href="/dashboard">Dashboard</a></li>
                        @auth
                            @if(auth()->user()->role === 'Admin')
                            <li><a href="/reports">Reports</a></li>
                            @endif
                        @endauth
                        @auth
                            @if (auth()->user()->role === 'Admin')
                                <li><a href="/admin/programs">Admin Programs</a></li>
                                <li><a href="/admin/courses">Admin Courses</a></li>
                                <li><a href="/admin/units">Admin Units</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="/help">Help Center</a></li>
                        <li><a href="/contact">Contact Us</a></li>
                        <li><a href="/faq">FAQ</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/terms">Terms of Service</a></li>
                    </ul>
                </div>
                
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; {{ date('Y') }} ABC Academy. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="/privacy">Privacy</a>
                        <a href="/terms">Terms</a>
                        <a href="/cookies">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            background: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 8px;
            z-index: 1;
            top: 100%;
            left: 0;
        }
        
        .dropdown-menu a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-radius: 8px;
            margin: 4px;
        }
        
        .dropdown-menu a:hover {
            background: #f8f9fa;
        }
        
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        
        .dropdown-toggle::after {
            content: ' ▼';
            font-size: 0.8em;
        }
        
        /* Reports Styling */
        .reports-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .report-form {
            margin-bottom: 30px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
        }
        
        .form-select, .form-input {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Searchable select styles */
        .searchable-select {
            position: relative;
        }
        
        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .search-dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }
        
        .search-dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .search-dropdown-item:last-child {
            border-bottom: none;
        }
        
        .search-dropdown-item.selected {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .filters-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
        }
        
        .filters-section.show {
            display: block;
        }
        
        .filters-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
        }
        
        .report-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .preview-section, .results-section {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: none;
        }
        
        .preview-section.show, .results-section.show {
            display: block;
        }
        
        .preview-content, .results-content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .report-meta {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .meta-item {
            background: #e5e7eb;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #374151;
        }
        
        .report-content {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .report-data {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .report-data pre {
            margin: 0;
            white-space: pre-wrap;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .loading-indicator {
            text-align: center;
            padding: 40px;
            display: none;
        }
        
        .loading-indicator.show {
            display: block;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        .loading-text {
            color: #6b7280;
            font-size: 14px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Button styles for reports */
        .btn-info {
            background: #06b6d4;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-info:hover {
            background: #0891b2;
        }
        
        .btn-warning {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-warning:hover {
            background: #d97706;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
    </style>
</body>
</html>