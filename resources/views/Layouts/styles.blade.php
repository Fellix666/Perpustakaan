<style>
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1;
        display: flex;
    }

    /* Updated Sidebar - White background with dark text */
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: white;
        border-right: 1px solid #e9ecef;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar .nav-link {
        color: #333;
        padding: 12px 20px;
        margin: 5px 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover {
        background-color: #f8f9fa;
        color: #4fa6fc;
        transform: translateX(5px);
    }

    .sidebar .nav-link.active {
        background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%);
        color: white;
        transform: translateX(5px);
    }

    /* Sidebar header styling */
    .sidebar .border-bottom {
        border-color: #e9ecef !important;
    }

    .sidebar h4 {
        color: #333 !important;
    }

    .sidebar small {
        color: #6c757d !important;
    }

    /* Section headers in sidebar */
    .sidebar .text-white-50 {
        color: #6c757d !important;
    }

    .content-wrapper {
        flex-grow: 1;
        padding: 0;
        overflow-y: auto;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    .content-wrapper-full {
        flex-grow: 1;
        padding: 0;
        overflow-y: auto;
        width: 100%;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Updated Navbar - Colorful background */
    .navbar {
        background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%) !important;
        border-bottom: none !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        color: white !important;
        font-weight: bold;
    }

    .navbar-brand:hover {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .navbar-brand span {
        color: white !important;
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.3);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Navbar dropdown styling */
    .navbar .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .navbar .nav-link:hover {
        color: white !important;
    }

    .navbar .dropdown-menu {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Login page specific styles */
    .login-page {
        background: linear-gradient(135deg, #ccd4f2 0%, #f8f9fa 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 400px;
        width: 100%;
    }

    .login-header {
        background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%);
        color: white;
        text-align: center;
        padding: 2rem;
    }

    .login-body {
        padding: 2rem;
    }

    .login-page .form-control {
        border-radius: 10px;
        border: 2px solid #f0f0f0;
        padding: 12px 15px;
    }

    .login-page .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-login {
        background: linear-gradient(135deg, #9fb0f1 0%, #76ace2 100%);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stats-card-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }

    .stats-card-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stats-card-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .btn-custom {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .table th {
        background-color: #6c757d;
        color: white;
        border: none;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            position: static;
        }
    }

    /* Updated Offcanvas sidebar styling for mobile - White background */
    .offcanvas {
        background-color: white;
    }

    .offcanvas-header {
        background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%);
        color: white;
    }

    .offcanvas-title {
        color: white;
    }

    .offcanvas .btn-close {
        filter: invert(1);
    }

    .offcanvas .nav-link {
        color: #333;
        padding: 10px 15px;
        display: block;
        border-radius: 5px;
        margin: 2px 0;
    }

    .offcanvas .nav-link:hover {
        background-color: #f8f9fa;
        color: #4fa6fc;
    }

    .offcanvas .nav-link.active {
        background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%);
        color: white;
    }

    /* Mobile offcanvas section headers */
    .offcanvas .text-muted {
        color: #6c757d !important;
    }

    /* Dashboard specific styles */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    }

    /* Chart container styling */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* Performance metrics styling */
    .performance-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .performance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Animated counters */
    .counter-animation {
        animation: countUp 2s ease-out;
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Card hover effects */
    .dashboard-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* Welcome section styling */
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        overflow: hidden;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }

    /* Responsive chart adjustments */
    @media (max-width: 768px) {
        .chart-container {
            height: 200px;
        }
        
        .display-4 {
            font-size: 2rem;
        }
    }

    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        color: #6c757d;
        border-color: #dee2e6;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .pagination .page-link:hover {
        color: #495057;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Card Footer Pagination */
    .card-footer .text-muted.small {
        font-size: 0.875rem;
    }
</style>