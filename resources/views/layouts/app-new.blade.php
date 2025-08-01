<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', config('app.name', 'Marketplace'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema Marketplace Completo" name="description" />
    <meta content="Marketplace Team" name="author" />
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('Theme1/images/favicon.ico') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('Theme1/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('Theme1/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('Theme1/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom Css-->
    <link href="{{ asset('Theme1/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom Styles -->
    <style>
        :root {
            --bs-primary: #405189;
            --bs-secondary: #6c757d;
            --bs-success: #0ab39c;
            --bs-danger: #f06548;
            --bs-warning: #f7b84b;
            --bs-info: #299cdb;
        }

        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .card {
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
            border: 1px solid #e9ecef;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover {
            background-color: #364574;
            border-color: #364574;
        }

        .page-title-box {
            background: #fff;
            padding: 20px 24px;
            margin: -20px -20px 20px -20px;
            border-bottom: 1px solid #e9ecef;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
        }
    </style>
    
    @stack('styles')
</head>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        @unless(request()->routeIs('login', 'register', 'password.*'))
            @include('layouts.header')
            @include('layouts.sidebar')
        @endunless

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        @if(request()->routeIs('login', 'register', 'password.*'))
            <!-- Auth pages without sidebar -->
            @yield('content')
        @else
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        
                        <!-- Page Title -->
                        @hasSection('page-title')
                            <div class="page-title-box">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="page-title">@yield('page-title')</h6>
                                        @hasSection('breadcrumb')
                                            <ol class="breadcrumb m-0">
                                                @yield('breadcrumb')
                                            </ol>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="float-end d-none d-md-block">
                                            @yield('page-actions')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Alerts -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ri-check-line me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="ri-alert-line me-2"></i>{{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="ri-information-line me-2"></i>{{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Main Content -->
                        @yield('content')

                    </div>
                </div>
                
                @include('layouts.footer')
            </div>
        @endif
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @stack('modals')

    <!-- JAVASCRIPT -->
    <script src="{{ asset('Theme1/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Theme1/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('Theme1/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('Theme1/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('Theme1/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('Theme1/js/plugins.js') }}"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token setup for AJAX
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.Laravel = {
                    csrfToken: token.getAttribute('content')
                };
                
                // Setup AJAX headers
                if (typeof $ !== 'undefined') {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': token.getAttribute('content')
                        }
                    });
                }
            }

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Confirm delete actions
            const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const message = this.getAttribute('data-confirm-delete') || 'Tem certeza que deseja excluir este item?';
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });

            // Loading states for forms
            const forms = document.querySelectorAll('form[data-loading]');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
                        
                        // Re-enable after 10 seconds as fallback
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 10000);
                    }
                });
            });

            // Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            console.log('Marketplace Layout System Loaded');
        });

        // Global utility functions
        window.MarketplaceUtils = {
            showAlert: function(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="ri-${type === 'success' ? 'check' : type === 'danger' ? 'error-warning' : 'information'}-line me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                const alertContainer = document.querySelector('.container-fluid') || document.body;
                alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
            },
            
            formatCurrency: function(value) {
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(value);
            },
            
            formatDate: function(date) {
                return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
            }
        };
    </script>

    <!-- Page Specific JS -->
    @stack('scripts')

</body>

</html>
