#!/bin/bash

# ============================================================
# GIT COMMIT COMMANDS - PEMBERSIHAN TOTAL MODUL LANSIA
# ============================================================
# Tanggal: 5 Mei 2026
# Status: SIAP DIJALANKAN
# ============================================================

echo "🚀 Memulai Git Commit untuk Pembersihan Total Modul Lansia..."
echo ""

# Masuk ke folder Laravel
cd "$(dirname "$0")"
echo "📁 Current directory: $(pwd)"
echo ""

# Cek status git
echo "📊 Git Status:"
git status --short
echo ""

# Add semua perubahan
echo "➕ Adding all changes..."
git add .
echo "✅ All changes added!"
echo ""

# Commit dengan pesan yang jelas
echo "💾 Committing changes..."
git commit -m "feat(lansia): Pembersihan total modul lansia dengan struktur database baru

- Drop tabel lama (lansia, kunjungan_lansia, jadwal_lansia, edukasi_lansia)
- Jalankan migration baru dengan struktur yang diperbaiki
- Rename model: LansiaBaru → Lansia, KunjunganLansiaBaru → KunjunganLansia, dll
- Update controller: LansiaDashboardController, LansiaKunjunganController
- Update field: tanggal_lahir → tgl_lahir untuk konsistensi
- Tambah fitur: Auto-calculate status kesehatan, BMI, rentang usia
- Tambah scope: aktif(), bulanIni(), tidakNormal(), dll
- Tambah accessor: umur, umur_display, rentang_usia, bmi, dll
- Tambah static method: hitungStatusTensi(), hitungStatusGula(), dll
- Testing: Database, model, dan route berhasil

BREAKING CHANGES:
- Field tanggal_lahir diganti menjadi tgl_lahir
- Foreign key menggunakan unsignedBigInteger
- Soft delete menggunakan is_deleted boolean
- Setiap kunjungan = INSERT baru (bukan UPDATE)

Refs: #TASK5-PEMBERSIHAN-TOTAL"

echo "✅ Commit berhasil!"
echo ""

# Push ke GitHub
echo "🚀 Pushing to GitHub..."
git push origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Push berhasil!"
    echo ""
    echo "🎉 SELESAI! Pembersihan total modul lansia berhasil di-commit dan di-push ke GitHub!"
else
    echo ""
    echo "❌ Push gagal! Silakan cek koneksi internet atau branch name."
    echo "💡 Jika branch bukan 'main', ganti dengan: git push origin <branch-name>"
fi

echo ""
echo "📚 Dokumentasi:"
echo "   - STRUKTUR_DATABASE_LANSIA_BARU.md"
echo "   - IMPLEMENTASI_MODUL_LANSIA_BARU.md"
echo "   - CHANGELOG_PEMBERSIHAN_TOTAL_LANSIA.md"
echo "   - SUMMARY_PEMBERSIHAN_TOTAL.md"
echo ""
echo "🎊 Terima kasih!"
