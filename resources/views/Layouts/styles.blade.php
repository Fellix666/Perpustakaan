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
</style>