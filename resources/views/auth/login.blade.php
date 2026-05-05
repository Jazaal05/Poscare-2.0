<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="160x160" href="{{ asset('images/poscare-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/poscare-logo.png') }}">

    <title>PosCare - Login Admin Posyandu</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}" />
    {{-- Font Awesome lokal - tidak pakai CDN agar tidak diblokir browser --}}
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />

    <script>
        // Bersihkan session markers saat halaman login dimuat
        (function() {
            sessionStorage.clear();
            localStorage.removeItem('loggedIn');
            if (window.location.search.includes('logout=success')) {
                window.history.replaceState(null, '', window.location.pathname);
            }
        })();
    </script>
</head>
<body>

{{-- VIDEO BACKGROUND --}}
<div class="video-background">
    <video id="bgVideo" autoplay loop muted playsinline preload="auto">
        <source src="{{ asset('images/login-bg.mp4') }}" type="video/mp4">
    </video>
</div>

{{-- FORM LOGIN --}}
<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/poscare-logo.png') }}"
                 alt="Logo PosCare"
                 class="logo-image"
                 style="background: transparent !important;"
                 onerror="this.onerror=null; this.style.display='none';">
        </div>

        <p class="login-subtitle">Admin Digital Posyandu</p>

        {{-- Tampilkan error jika ada --}}
        @if(session('error'))
            <div style="background:#fee2e2;border-left:4px solid #ef4444;padding:12px 16px;border-radius:8px;margin-bottom:16px;color:#991b1b;font-size:0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        <form class="login-form" id="loginForm">
            @csrf
            {{-- Hidden field untuk menyimpan redirect target --}}
            <input type="hidden" id="redirectTo" value="{{ $intended ?? '' }}">
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" id="email" name="email" placeholder="Email" required />
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" id="password" name="password" placeholder="Kata Sandi" required />
                <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
            </div>

            <div class="forgot-password-container">
                <a href="#" class="forgot-password-link" id="forgotPasswordLink">
                    <i class="fas fa-key"></i> Lupa Kata Sandi?
                </a>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <span class="btn-text">Masuk</span>
                <div class="spinner"></div>
            </button>
        </form>
    </div>
</div>

{{-- MODAL LUPA PASSWORD --}}
<div class="modal" id="forgotPasswordModal">
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-key"></i> Atur Ulang Kata Sandi</h2>
            <button class="modal-close" id="closeForgotModal">&times;</button>
        </div>
        <div class="modal-body">

            {{-- STEP 1: Request OTP --}}
            <div id="step1RequestOtp">
                <p class="modal-info">Masukkan alamat email yang terdaftar. Kami akan mengirimkan kode OTP untuk mengatur ulang kata sandi Anda.</p>
                <form id="requestOtpForm">
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="forgotEmail" placeholder="Email" required />
                    </div>
                    <button type="submit" class="btn-primary-modal">
                        <i class="fas fa-paper-plane"></i> Kirim Kode OTP
                    </button>
                </form>
            </div>

            {{-- STEP 2: Verifikasi OTP & Reset Password --}}
            <div id="step2VerifyOtp" style="display: none;">
                <p class="modal-info">Masukkan kode OTP yang telah dikirim ke email Anda dan atur kata sandi baru.</p>
                <form id="verifyOtpForm">
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="emailReadonly" placeholder="Email" readonly style="background-color: #f0f0f0;" />
                    </div>
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input type="text" id="otpCode" placeholder="Kode OTP (6 digit)" maxlength="6" pattern="\d{6}" required />
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="newPasswordReset" placeholder="Kata Sandi Baru" required />
                        <i class="fas fa-eye-slash password-toggle" id="toggleNewPasswordReset"></i>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="confirmPasswordReset" placeholder="Konfirmasi Kata Sandi Baru" required />
                        <i class="fas fa-eye-slash password-toggle" id="toggleConfirmPasswordReset"></i>
                    </div>
                    <button type="submit" class="btn-primary-modal">
                        <i class="fas fa-check-circle"></i> Atur Ulang Kata Sandi
                    </button>
                    <button type="button" id="backToStep1" class="btn-secondary-modal" style="margin-top: 10px;">
                        <i class="fas fa-arrow-left"></i> Kirim Ulang OTP
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- LOADING OVERLAY --}}
<div id="loadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9999;backdrop-filter:blur(5px);">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
        <div style="width:80px;height:80px;border:6px solid rgba(255,255,255,0.3);border-top-color:#246BCE;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 20px;"></div>
        <p style="color:white;font-size:18px;font-weight:600;">Memproses...</p>
    </div>
</div>

{{-- NOTIFICATION POPUP --}}
<div id="notificationPopup" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:10000;min-width:400px;max-width:500px;">
    <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 10px 40px rgba(0,0,0,0.3);text-align:center;">
        <div id="notificationIcon" style="width:80px;height:80px;margin:0 auto 20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px;"></div>
        <h3 id="notificationTitle" style="font-size:22px;font-weight:600;margin-bottom:12px;color:#1E293B;"></h3>
        <p id="notificationMessage" style="font-size:15px;color:#64748B;margin-bottom:24px;line-height:1.5;"></p>
        <button id="notificationClose" style="background:#246BCE;color:white;border:none;padding:12px 32px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">OK</button>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes fadeIn { from { opacity:0; transform:translate(-50%,-50%) scale(0.9); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }
    @keyframes fadeOut { from { opacity:1; transform:translate(-50%,-50%) scale(1); } to { opacity:0; transform:translate(-50%,-50%) scale(0.9); } }
</style>

<script>
// ============================================================
// CSRF TOKEN untuk semua request
// ============================================================
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ============================================================
// VIDEO BACKGROUND AUTOPLAY
// ============================================================
window.addEventListener('load', function() {
    const video = document.getElementById('bgVideo');
    if (video) {
        video.play().catch(() => {});
        video.addEventListener('canplay', () => { if (video.paused) video.play().catch(() => {}); });
    }
});

// ============================================================
// TOGGLE PASSWORD VISIBILITY
// ============================================================
function setupPasswordToggle(toggleId, inputId) {
    const toggle = document.getElementById(toggleId);
    const input  = document.getElementById(inputId);
    if (toggle && input) {
        toggle.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    }
}
setupPasswordToggle('togglePassword', 'password');
setupPasswordToggle('toggleNewPasswordReset', 'newPasswordReset');
setupPasswordToggle('toggleConfirmPasswordReset', 'confirmPasswordReset');

// ============================================================
// NOTIFICATION HELPER
// ============================================================
function showNotification(type, title, message, callback) {
    const popup   = document.getElementById('notificationPopup');
    const icon    = document.getElementById('notificationIcon');
    const titleEl = document.getElementById('notificationTitle');
    const msgEl   = document.getElementById('notificationMessage');
    const closeBtn = document.getElementById('notificationClose');

    const config = {
        success: { bg: '#D1FAE5', color: '#065F46', emoji: '✅' },
        error:   { bg: '#FEE2E2', color: '#991B1B', emoji: '❌' },
        warning: { bg: '#FEF3C7', color: '#92400E', emoji: '⚠️' },
    };
    const c = config[type] || config.warning;

    icon.style.background = c.bg;
    icon.style.color      = c.color;
    icon.textContent      = c.emoji;
    titleEl.textContent   = title;
    msgEl.textContent     = message;

    popup.style.display = 'block';
    popup.style.animation = 'fadeIn 0.3s ease';

    closeBtn.onclick = function() {
        popup.style.display = 'none';
        if (callback) callback();
    };
}

// ============================================================
// MODAL LUPA PASSWORD - BUKA / TUTUP
// ============================================================
const modal           = document.getElementById('forgotPasswordModal');
const forgotLink      = document.getElementById('forgotPasswordLink');
const closeModalBtn   = document.getElementById('closeForgotModal');
const overlay         = document.getElementById('modalOverlay');

function openModal() {
    modal.classList.add('active');
    document.getElementById('step1RequestOtp').style.display = 'block';
    document.getElementById('step2VerifyOtp').style.display  = 'none';
}

function closeModal() {
    modal.classList.remove('active');
    document.getElementById('requestOtpForm').reset();
    document.getElementById('verifyOtpForm').reset();
    document.getElementById('step1RequestOtp').style.display = 'block';
    document.getElementById('step2VerifyOtp').style.display  = 'none';
}

forgotLink.addEventListener('click', e => { e.preventDefault(); openModal(); });
closeModalBtn.addEventListener('click', closeModal);
overlay.addEventListener('click', closeModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
document.getElementById('backToStep1').addEventListener('click', () => {
    document.getElementById('step1RequestOtp').style.display = 'block';
    document.getElementById('step2VerifyOtp').style.display  = 'none';
});

// ============================================================
// FORM LOGIN - SUBMIT
// Menggantikan: fetch ke api_web/auth.php
// ============================================================
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const btn      = document.getElementById('loginBtn');

    if (!email || !password) {
        showNotification('warning', 'Form Tidak Lengkap', 'Email dan kata sandi harus diisi.');
        return;
    }

    btn.classList.add('loading');
    btn.disabled = true;

    try {
        // POST ke route Laravel: POST /login
        const redirectTo = document.getElementById('redirectTo')?.value || '';

        const response = await fetch('{{ route("auth.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ email, password, redirect_to: redirectTo }),
        });

        const data = await response.json();

        if (data.success) {
            showNotification('success', 'Login Berhasil!', 'Selamat datang di PosCare.', () => {
                window.location.href = data.redirect;
            });
        } else {
            showNotification('error', 'Login Gagal', data.message || 'Email atau kata sandi salah.');
        }
    } catch (err) {
        showNotification('error', 'Koneksi Gagal', 'Gagal terhubung ke server. Coba lagi.');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
});

// ============================================================
// REQUEST OTP - STEP 1
// Menggantikan: fetch ke api_web/reset_password.php (action: request_otp)
// ============================================================
document.getElementById('requestOtpForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email  = document.getElementById('forgotEmail').value.trim();
    const btn    = this.querySelector('button[type="submit"]');
    const origTxt = btn.innerHTML;

    if (!email) { showNotification('warning', 'Email Kosong', 'Silakan masukkan alamat email Anda.'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showNotification('warning', 'Format Email Salah', 'Masukkan email yang valid.'); return; }

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    btn.disabled  = true;

    try {
        // POST ke route Laravel: POST /reset-password/request-otp
        const response = await fetch('{{ route("auth.request-otp") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ email }),
        });

        const data = await response.json();

        if (data.success) {
            showNotification('success', 'OTP Terkirim!', data.message);
            document.getElementById('step1RequestOtp').style.display = 'none';
            document.getElementById('step2VerifyOtp').style.display  = 'block';
            document.getElementById('emailReadonly').value = email;
        } else {
            showNotification('error', 'Gagal Mengirim OTP', data.message || 'Email tidak terdaftar.');
        }
    } catch (err) {
        showNotification('error', 'Koneksi Gagal', 'Gagal terhubung ke server.');
    } finally {
        btn.innerHTML = origTxt;
        btn.disabled  = false;
    }
});

// ============================================================
// VERIFY OTP & RESET PASSWORD - STEP 2
// Menggantikan: fetch ke api_web/reset_password.php (action: reset_with_otp)
// ============================================================
document.getElementById('verifyOtpForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email           = document.getElementById('emailReadonly').value.trim();
    const otp             = document.getElementById('otpCode').value.trim();
    const newPassword     = document.getElementById('newPasswordReset').value.trim();
    const confirmPassword = document.getElementById('confirmPasswordReset').value.trim();
    const btn             = this.querySelector('button[type="submit"]');
    const origTxt         = btn.innerHTML;

    if (!email || !otp || !newPassword || !confirmPassword) {
        showNotification('warning', 'Form Tidak Lengkap', 'Semua kolom harus diisi!'); return;
    }
    if (!/^\d{6}$/.test(otp)) {
        showNotification('warning', 'Kode OTP Salah', 'Kode OTP harus 6 digit angka!'); return;
    }
    if (newPassword !== confirmPassword) {
        showNotification('error', 'Kata Sandi Tidak Cocok', 'Kata sandi baru dan konfirmasi tidak cocok!'); return;
    }
    if (newPassword.length < 6) {
        showNotification('warning', 'Kata Sandi Terlalu Pendek', 'Kata sandi minimal 6 karakter!'); return;
    }

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    btn.disabled  = true;

    try {
        // POST ke route Laravel: POST /reset-password/reset
        const response = await fetch('{{ route("auth.reset-password") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ email, otp, new_password: newPassword }),
        });

        const data = await response.json();

        if (data.success) {
            showNotification('success', 'Berhasil!', data.message, () => {
                closeModal();
            });
        } else {
            showNotification('error', 'Gagal Reset Password', data.message || 'Terjadi kesalahan.');
        }
    } catch (err) {
        showNotification('error', 'Koneksi Gagal', 'Gagal terhubung ke server.');
    } finally {
        btn.innerHTML = origTxt;
        btn.disabled  = false;
    }
});
</script>

</body>
</html>
