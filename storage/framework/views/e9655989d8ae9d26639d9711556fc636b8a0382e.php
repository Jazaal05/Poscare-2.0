<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="icon" type="image/png" href="<?php echo e(asset('images/poscare-logo.png')); ?>">
    <title><?php echo $__env->yieldContent('title', 'PosCare Lansia'); ?> - PosCare</title>

    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('css/fontawesome/all.min.css')); ?>" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Navbar Lansia - Glassmorphism with Green Theme */
        .navbar {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 4px 16px rgba(5, 150, 105, 0.08) !important;
        }
        
        /* Navbar logo green */
        .navbar-logo {
            color: #059669 !important;
            font-weight: 600 !important;
        }
        
        /* Navbar menu items - default gray */
        .navbar-menu li a {
            color: #64748b !important;
            background: transparent !important;
            box-shadow: none !important;
            position: relative;
        }
        
        /* Animated Underline Effect - Green */
        .navbar-menu li a::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 80%;
            height: 3px;
            background: linear-gradient(90deg, #059669, #10B981);
            border-radius: 2px;
            transform-origin: center;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Hover state - green color with underline */
        .navbar-menu li a:hover {
            color: #059669 !important;
            background: transparent !important;
            transform: translateY(-2px);
            box-shadow: none !important;
        }
        
        .navbar-menu li a:hover::after {
            transform: translateX(-50%) scaleX(1);
        }
        
        /* Icon bounce on hover */
        .navbar-menu li a:hover i {
            transform: scale(1.15) rotate(5deg);
            color: #059669 !important;
        }
        
        /* Active state - green with underline always visible */
        .navbar-menu li a.active {
            color: #059669 !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        
        .navbar-menu li a.active::after {
            transform: translateX(-50%) scaleX(1);
            background: #059669;
        }
        
        /* Navbar actions - Balita and Keluar buttons */
        .navbar-actions {
            border-left: 1px solid rgba(100, 116, 139, 0.2);
        }
        
        .navbar-actions a {
            transition: all 0.2s ease;
        }
        
        .navbar-actions a:hover {
            background: rgba(0,0,0,0.05) !important;
            transform: translateY(-1px);
        }
        
        /* Green theme for body background */
        body {
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 50%, #BBF7D0 100%) !important;
        }
        
        /* Update card and button colors to match green theme */
        .btn-primary {
            background: linear-gradient(135deg, #059669, #10B981) !important;
            border: none !important;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #047857, #059669) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3) !important;
        }
        
        /* Tab buttons green theme */
        .tab-btn.active {
            color: #059669 !important;
            border-bottom-color: #059669 !important;
            background: rgba(5, 150, 105, 0.08) !important;
        }
        
        .tab-btn:hover:not(.active) {
            color: #059669 !important;
            background: rgba(5, 150, 105, 0.05) !important;
        }
        
        /* Search input focus green */
        .search-input:focus {
            border-color: #10B981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }
        
        /* Form input focus green */
        .form-group input:focus, 
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #10B981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }
        
        /* Badge colors - green theme */
        .badge-success {
            background: #D1FAE5 !important;
            color: #065F46 !important;
        }
        
        /* Pagination green theme */
        .pagination-btn {
            border-color: #10B981 !important;
            color: #10B981 !important;
        }
        
        .pagination-btn:hover:not(:disabled) {
            background: #10B981 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
        }
        
        .pagination-info .page-number {
            color: #065F46 !important;
        }
        
        .pagination-dot.active {
            background: #10B981 !important;
        }
        
        .pagination-dot:hover:not(.active) {
            background: #6EE7B7 !important;
        }
        
        /* Action buttons - softer colors */
        .action-btn.-view {
            background: #D1FAE5 !important;
            color: #059669 !important;
        }
        
        .action-btn.-view:hover {
            background: #A7F3D0 !important;
        }
        
        .action-btn.-edit {
            background: #FEF3C7 !important;
            color: #D97706 !important;
        }
        
        .action-btn.-edit:hover {
            background: #FDE68A !important;
        }
        
        .action-btn.-delete {
            background: #FEE2E2 !important;
            color: #DC2626 !important;
        }
        
        .action-btn.-delete:hover {
            background: #FECACA !important;
        }
        
        /* Table header green theme */
        thead th {
            background: #F0FDF4 !important;
            color: #065F46 !important;
            border-bottom: 2px solid #BBF7D0 !important;
        }
        
        tbody tr:hover {
            background: #F0FDF4 !important;
        }
        
        /* Modal title green */
        .modal-title {
            color: #065F46 !important;
        }
        
        /* Button success green */
        .btn-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857) !important;
        }
        
        /* Button outline green */
        .btn-outline {
            border-color: #10B981 !important;
            color: #10B981 !important;
        }
        
        .btn-outline:hover {
            background: #10B981 !important;
            color: #fff !important;
        }
    </style>

    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>


<nav class="navbar">
    <div class="navbar-container">
        <span class="navbar-logo">
            <img src="<?php echo e(asset('images/poscare-logo.png')); ?>" alt="PosCare Logo" style="width:32px;height:32px;object-fit:contain;"> PosCare Lansia
        </span>
        <ul class="navbar-menu">
            <li><a href="<?php echo e(route('lansia.dashboard')); ?>" class="<?php echo e(request()->routeIs('lansia.dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-chart-line"></i>Dashboard
            </a></li>
            <li><a href="<?php echo e(route('lansia.kunjungan.index')); ?>" class="<?php echo e(request()->routeIs('lansia.kunjungan*') || request()->routeIs('lansia.index') ? 'active' : ''); ?>">
                <i class="fas fa-heartbeat"></i>Kunjungan
            </a></li>
            <li><a href="<?php echo e(route('lansia.jadwal.index')); ?>" class="<?php echo e(request()->routeIs('lansia.jadwal*') ? 'active' : ''); ?>">
                <i class="fas fa-calendar-alt"></i>Jadwal
            </a></li>
            <li><a href="<?php echo e(route('lansia.laporan.index')); ?>" class="<?php echo e(request()->routeIs('lansia.laporan*') ? 'active' : ''); ?>">
                <i class="fas fa-file-alt"></i>Laporan
            </a></li>
            <li><a href="<?php echo e(route('lansia.edukasi.index')); ?>" class="<?php echo e(request()->routeIs('lansia.edukasi*') ? 'active' : ''); ?>">
                <i class="fas fa-book-open"></i>Edukasi
            </a></li>
            <li><a href="<?php echo e(route('lansia.pengaturan.index')); ?>" class="<?php echo e(request()->routeIs('lansia.pengaturan*') ? 'active' : ''); ?>">
                <i class="fas fa-cog"></i>Pengaturan
            </a></li>
        </ul>
        <div class="navbar-actions">
            <a href="<?php echo e(route('dashboard')); ?>" style="color:#3B82F6 !important;text-decoration:none;font-weight:500;font-size:0.95rem;padding:0.5rem 0.75rem;display:inline-flex;align-items:center;gap:0.5rem;border-radius:8px;" title="Ke Balita">
                <i class="fas fa-exchange-alt"></i>Balita
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" id="logoutForm" style="display:inline;">
                <?php echo csrf_field(); ?>
                <a href="#" onclick="event.preventDefault();document.getElementById('logoutForm').submit();"
                   style="color:#ef4444 !important;text-decoration:none;font-weight:500;font-size:0.95rem;padding:0.5rem 0.75rem;display:inline-flex;align-items:center;gap:0.5rem;border-radius:8px;">
                    <i class="fas fa-sign-out-alt"></i>Keluar
                </a>
            </form>
        </div>
    </div>
</nav>


<div class="container">
    <?php echo $__env->yieldContent('content'); ?>
</div>

<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>
<script src="<?php echo e(asset('js/input-validator.js')); ?>"></script>

<?php echo $__env->yieldContent('scripts'); ?>

</body>
</html>
<?php /**PATH C:\Users\asus\VSCode\poscare-laravel\resources\views/layouts/lansia.blade.php ENDPATH**/ ?>