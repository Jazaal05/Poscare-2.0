<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="icon" type="image/png" href="<?php echo e(asset('images/poscare-logo.png')); ?>">
    <title><?php echo $__env->yieldContent('title', 'PosCare'); ?> - PosCare</title>

    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('css/fontawesome/all.min.css')); ?>" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>


<nav class="navbar">
    <div class="navbar-container">
        <span class="navbar-logo">
            <img src="<?php echo e(asset('images/poscare-logo.png')); ?>" alt="PosCare Logo" style="width:32px;height:32px;object-fit:contain;"> PosCare
        </span>
        <ul class="navbar-menu">
            <li><a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-home"></i>Beranda
            </a></li>
            <li><a href="<?php echo e(route('anak.index')); ?>" class="<?php echo e(request()->routeIs('anak*') ? 'active' : ''); ?>">
                <i class="fas fa-users"></i>Data Anak
            </a></li>
            <li><a href="<?php echo e(route('jadwal.index')); ?>" class="<?php echo e(request()->routeIs('jadwal*') ? 'active' : ''); ?>">
                <i class="fas fa-calendar-alt"></i>Jadwal
            </a></li>
            <li><a href="<?php echo e(route('edukasi.index')); ?>" class="<?php echo e(request()->routeIs('edukasi*') ? 'active' : ''); ?>">
                <i class="fas fa-book-open"></i>Edukasi
            </a></li>
            <li><a href="<?php echo e(route('imunisasi.index')); ?>" class="<?php echo e(request()->routeIs('imunisasi*') ? 'active' : ''); ?>">
                <i class="fas fa-syringe"></i>Imunisasi
            </a></li>
            <li><a href="<?php echo e(route('laporan.index')); ?>" class="<?php echo e(request()->routeIs('laporan*') ? 'active' : ''); ?>">
                <i class="fas fa-chart-line"></i>Laporan
            </a></li>
            <li><a href="<?php echo e(route('pengaturan.index')); ?>" class="<?php echo e(request()->routeIs('pengaturan*') ? 'active' : ''); ?>">
                <i class="fas fa-cog"></i>Pengaturan
            </a></li>
        </ul>
        <div class="navbar-actions">
            <a href="<?php echo e(route('lansia.dashboard')); ?>" style="color:#10B981 !important;text-decoration:none;font-weight:500;font-size:0.95rem;padding:0.5rem 1rem;display:inline-flex;align-items:center;gap:0.5rem;" title="Ke Lansia">
                <i class="fas fa-exchange-alt"></i>Lansia
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" id="logoutForm" style="display:inline;">
                <?php echo csrf_field(); ?>
                <a href="#" onclick="event.preventDefault();document.getElementById('logoutForm').submit();"
                   style="color:#ef4444 !important;text-decoration:none;font-weight:500;font-size:0.95rem;padding:0.5rem 1rem;display:inline-flex;align-items:center;gap:0.5rem;">
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
<?php /**PATH C:\Users\asus\VSCode\poscare-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>