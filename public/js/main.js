// ================================================
// POSCARE - MAIN JAVASCRIPT
// Global functions & utilities
// ================================================

// --------------------------------
// Authentication Check
// --------------------------------
function checkAuth() {
  if (!sessionStorage.getItem("loggedIn")) {
    window.location.href = "../index.html";
    return false;
  }
  return true;
}

// --------------------------------
// Logout Function
// --------------------------------
function logout() {
  if (confirm("Yakin ingin keluar?")) {
    sessionStorage.clear();
    window.location.href = "../index.html";
  }
}

// ================================================
// TOAST NOTIFICATIONS
// ================================================
function showToast(message, type = "success") {
  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;

  const iconMap = {
    success: "fa-check-circle",
    warning: "fa-exclamation-triangle",
    error: "fa-times-circle",
    info: "fa-info-circle",
  };

  const colorMap = {
    success: "#10B981",
    warning: "#F59E0B",
    error: "#EF4444",
    info: "#246BCE",
  };

  toast.innerHTML = `
        <i class="fas ${iconMap[type]} toast-icon" style="color: ${colorMap[type]};"></i>
        <span class="toast-message">${message}</span>
    `;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideOutRight 0.3s ease-out";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ================================================
// Z-SCORE CALCULATIONS (WHO Standards - simplified)
// ================================================
function calculateZScore(measurement, age, gender, type) {
  const whoStandards = {
    "BB/U": {
      L: { 12: { mean: 9.6, sd: 1.0 }, 24: { mean: 12.2, sd: 1.2 } },
      P: { 12: { mean: 9.0, sd: 0.9 }, 24: { mean: 11.5, sd: 1.1 } },
    },
    "TB/U": {
      L: { 12: { mean: 75.7, sd: 2.9 }, 24: { mean: 86.8, sd: 3.2 } },
      P: { 12: { mean: 74.0, sd: 2.8 }, 24: { mean: 85.1, sd: 3.1 } },
    },
  };

  const ageInMonths = Math.round(age);
  const closestAge = ageInMonths >= 18 ? 24 : 12;

  if (!whoStandards[type] || !whoStandards[type][gender]) {
    return { zScore: 0, status: "Data tidak tersedia" };
  }

  const standard = whoStandards[type][gender][closestAge];
  const zScore = (measurement - standard.mean) / standard.sd;

  return {
    zScore: zScore.toFixed(2),
    status: getStatusFromZScore(zScore, type),
  };
}

function getStatusFromZScore(zScore, type) {
  if (type === "BB/U") {
    if (zScore < -3) return "Gizi Buruk";
    if (zScore < -2) return "Gizi Kurang";
    if (zScore > 2) return "Obesitas";
    return "Gizi Baik";
  } else if (type === "TB/U") {
    if (zScore < -3) return "Sangat Terhambat";
    if (zScore < -2) return "Terhambat (Stunting)";
    if (zScore > 2) return "Tinggi";
    return "Normal";
  }
  return "Normal";
}

// ================================================
// DATE & TIME UTILITIES
// ================================================
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

function calculateAge(birthDate) {
  const today = new Date();
  const birth = new Date(birthDate);

  const years = today.getFullYear() - birth.getFullYear();
  const months = today.getMonth() - birth.getMonth();
  const totalMonths = years * 12 + months;

  if (totalMonths < 12) return `${totalMonths} bulan`;
  if (totalMonths < 24)
    return `${Math.floor(totalMonths / 12)} tahun ${totalMonths % 12} bulan`;
  return `${Math.floor(totalMonths / 12)} tahun`;
}

function getAgeInMonths(birthDate) {
  const today = new Date();
  const birth = new Date(birthDate);
  const years = today.getFullYear() - birth.getFullYear();
  const months = today.getMonth() - birth.getMonth();
  return years * 12 + months;
}

// ================================================
// LOCAL STORAGE HELPER
// ================================================
const LocalStorage = {
  save: (key, data) => {
    try {
      localStorage.setItem(key, JSON.stringify(data));
      return true;
    } catch (e) {
      console.error("Error saving to localStorage:", e);
      return false;
    }
  },
  get: (key) => {
    try {
      const data = localStorage.getItem(key);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      console.error("Error reading from localStorage:", e);
      return null;
    }
  },
  remove: (key) => {
    localStorage.removeItem(key);
  },
  clear: () => {
    localStorage.clear();
  },
};

// ================================================
// VALIDATION HELPERS
// ================================================
function validateNIK(nik) {
  return /^\d{16}$/.test(nik);
}
function validatePhone(p) {
  return /^(\+62|62|0)[0-9]{9,12}$/.test(p);
}
function validateEmail(mail) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail);
}

// ================================================
// MODAL HELPERS
// ================================================
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("active");
    document.body.style.overflow = "hidden";
  }
}
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("active");
    document.body.style.overflow = "auto";
  }
}
// Close modal on outside click
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("modal-overlay")) {
    e.target.classList.remove("active");
    document.body.style.overflow = "auto";
  }
});

// ================================================
// TABLE SEARCH & FILTER
// ================================================
function searchTable(searchInput, tableBody) {
  const term = searchInput.value.toLowerCase();
  const rows = tableBody.querySelectorAll("tr");
  rows.forEach((row) => {
    row.style.display = row.textContent.toLowerCase().includes(term)
      ? ""
      : "none";
  });
}

// ================================================
// EXPORT HELPERS
// ================================================
function exportTableToExcel(tableData, fileName) {
  if (typeof XLSX === "undefined") {
    showToast("Library XLSX tidak ditemukan!", "error");
    return;
  }
  const ws = XLSX.utils.json_to_sheet(tableData);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Data");

  const cols = Object.keys(tableData[0] || {}).map(() => ({ wch: 20 }));
  ws["!cols"] = cols;

  XLSX.writeFile(wb, fileName);
  showToast("File Excel berhasil diunduh!", "success");
}

function exportTableToCSV(tableData, fileName) {
  const headers = Object.keys(tableData[0] || {});
  const csvRows = [
    headers.join(","),
    ...tableData.map((row) =>
      headers
        .map((h) => {
          const v = row[h];
          return typeof v === "string" && v.includes(",") ? `"${v}"` : v;
        })
        .join(",")
    ),
  ];
  const blob = new Blob([csvRows.join("\n")], {
    type: "text/csv;charset=utf-8;",
  });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = fileName;
  link.click();
  showToast("File CSV berhasil diunduh!", "success");
}

// ================================================
// RECOMMENDATION ENGINE (contoh)
// ================================================
function getHealthRecommendations(ageInMonths, statusGizi, gender) {
  const r = { makanan: [], aktivitas: [] };

  if (ageInMonths >= 6 && ageInMonths < 24) {
    r.makanan = [
      "MPASI bergizi: telur, tempe, sayur hijau",
      "ASI eksklusif sampai 6 bulan; lanjut sampai 2 tahun",
      "Hindari gula/garam berlebihan",
      "Porsi 3–4x/hari + camilan sehat",
    ];
    r.aktivitas = [
      "Main luar ruangan 1 jam/hari",
      "Stimulasi motorik: merangkak/berjalan",
      "Interaksi: membaca/bernyanyi",
      "Tidur 12–14 jam/hari",
    ];
  } else if (ageInMonths >= 24 && ageInMonths < 60) {
    r.makanan = [
      "Sarapan bergizi setiap hari",
      "Protein hewani min. 1x/hari",
      "Susu 2–3 gelas/hari",
      "Batasi junk food & minuman manis",
    ];
    r.aktivitas = [
      "Bermain aktif min. 3 jam/hari",
      "Olahraga ringan (lompat tali/sepeda)",
      "Aktivitas kreatif",
      "Tidur 10–12 jam/hari",
    ];
  }

  if (statusGizi.includes("Kurang") || statusGizi.includes("Stunting")) {
    r.makanan.unshift(
      "⚠️ PRIORITAS: tambah protein & kalori",
      "Konsultasi puskesmas",
      "Makan 5–6x porsi kecil"
    );
  } else if (statusGizi.includes("Obesitas")) {
    r.makanan.unshift(
      "⚠️ PRIORITAS: kurangi gula/lemak",
      "Perbanyak sayur/buah",
      "Porsi lebih kecil, sering"
    );
    r.aktivitas.unshift(
      "⚠️ Tingkatkan aktivitas 4–5 jam/hari",
      "Batasi screen time ≤1 jam/hari"
    );
  }
  return r;
}

// ================================================
// SAMPLE DATA GENERATOR (dummy untuk demo tabel)
// ================================================
function generateSampleChildren(count = 10) {
  const names = [
    "Ahmad Rizki",
    "Siti Aisyah",
    "Budi Santoso",
    "Dewi Lestari",
    "Eko Prasetyo",
    "Fitri Handayani",
    "Gita Purnama",
    "Hadi Wijaya",
    "Indah Permata",
    "Joko Susanto",
  ];
  const mothers = [
    "Siti Nurjanah",
    "Dewi Kartika",
    "Ani Suryani",
    "Rina Wati",
    "Sri Mulyani",
    "Yuni Astuti",
    "Lina Marlina",
    "Dian Pertiwi",
    "Eka Sari",
    "Maya Anggraini",
  ];
  const data = [];

  for (let i = 0; i < count; i++) {
    const birthYear = 2020 + Math.floor(Math.random() * 3);
    const birthMonth = Math.floor(Math.random() * 12) + 1;
    const birthDay = Math.floor(Math.random() * 28) + 1;
    const tglLahir = `${birthYear}-${String(birthMonth).padStart(
      2,
      "0"
    )}-${String(birthDay).padStart(2, "0")}`;

    const ageMonths = getAgeInMonths(tglLahir);
    const gender = Math.random() > 0.5 ? "L" : "P";

    const baseWeight = 3.5 + ageMonths * 0.4;
    const baseHeight = 50 + ageMonths * 1.5;

    const weight = (baseWeight + (Math.random() * 2 - 1)).toFixed(1);
    const height = (baseHeight + (Math.random() * 5 - 2.5)).toFixed(1);

    const bbStatus = calculateZScore(
      parseFloat(weight),
      ageMonths,
      gender,
      "BB/U"
    );

    data.push({
      nores: String(i + 1).padStart(3, "0"),
      nik: `33010120${birthYear}0${birthMonth}00${i + 1}`,
      namaAnak: names[i % names.length],
      gender: gender,
      tglLahir: tglLahir,
      anakKe: Math.floor(Math.random() * 3) + 1,
      bb: parseFloat(weight),
      tb: parseFloat(height),
      namaIbu: mothers[i % mothers.length],
      alamat: `Dusun ${["Mawar", "Melati", "Anggrek"][i % 3]} RT0${
        (i % 3) + 1
      }/RW0${Math.floor(i / 3) + 1}`,
      noTelp: `08123456${String(7890 + i).padStart(4, "0")}`,
      statusGizi: bbStatus.status,
    });
  }
  return data;
}

// ================================================
// CALENDAR (Jadwal Posyandu) — Glass/Blur UI
// ================================================
// Dibungkus agar tidak mengotori global scope.
const PoscareCalendar = (() => {
  let currentDate = new Date();
  let selectedDate = null;

  const monthNames = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];

  const els = {
    title: () => document.getElementById("calendarTitle"),
    days: () => document.getElementById("calendarDays"),
    prev: () => document.getElementById("btnPrevMonth"),
    next: () => document.getElementById("btnNextMonth"),
    today: () => document.getElementById("btnToday"),
  };

  function renderCalendar() {
    const container = els.days();
    if (!container) return; // tidak ada kalender di halaman ini

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Update title
    const title = els.title();
    if (title) title.textContent = `${monthNames[month]} ${year}`;

    // Hitung tanggal
    const firstDay = new Date(year, month, 1).getDay(); // 0 = Minggu
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    container.innerHTML = "";

    const today = new Date();
    const isCurMonth =
      today.getFullYear() === year && today.getMonth() === month;
    const todayDate = today.getDate();

    // Hari dari bulan sebelumnya
    for (let i = firstDay - 1; i >= 0; i--) {
      const day = daysInPrevMonth - i;
      const d = document.createElement("div");
      d.className = "calendar-day other-month";
      d.textContent = day;
      container.appendChild(d);
    }

    // Hari bulan ini
    for (let day = 1; day <= daysInMonth; day++) {
      const d = document.createElement("div");
      d.className = "calendar-day";
      d.textContent = day;

      if (isCurMonth && day === todayDate) d.classList.add("today");

      // Demo penanda event (silakan ganti dengan logikamu)
      if (day % 7 === 0) d.classList.add("has-event");

      d.addEventListener("click", () => selectDate(year, month, day, d));
      container.appendChild(d);
    }

    // Isi sisa grid dengan bulan berikutnya
    const totalCells = container.children.length;
    const remain = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let day = 1; day <= remain; day++) {
      const d = document.createElement("div");
      d.className = "calendar-day other-month";
      d.textContent = day;
      container.appendChild(d);
    }
  }

  function selectDate(year, month, day, el) {
    document
      .querySelectorAll(".calendar-day")
      .forEach((d) => d.classList.remove("selected"));
    el.classList.add("selected");
    selectedDate = new Date(year, month, day);
    console.log("Selected date:", selectedDate);
    // TODO: muat slot jadwal/agenda untuk tanggal terpilih
  }

  function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
  }
  function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
  }
  function today() {
    currentDate = new Date();
    renderCalendar();
  }

  function init() {
    // Pasang event tombol jika ada
    const p = els.prev();
    if (p) p.addEventListener("click", prevMonth);
    const n = els.next();
    if (n) n.addEventListener("click", nextMonth);
    const t = els.today();
    if (t) t.addEventListener("click", today);

    renderCalendar();
  }

  // API publik
  return {
    init,
    render: renderCalendar,
    prevMonth,
    nextMonth,
    today,
    getSelectedDate: () => selectedDate,
  };
})();

// ================================================
// INITIALIZATION
// ================================================
document.addEventListener("DOMContentLoaded", function () {
  // Fade-in ringan
  document.body.style.opacity = "0";
  setTimeout(() => {
    document.body.style.transition = "opacity 0.3s ease";
    document.body.style.opacity = "1";
  }, 100);

  // Mobile menu toggle
  const menuToggle = document.querySelector(".menu-toggle");
  const navMenu = document.querySelector(".navbar-menu");
  if (menuToggle && navMenu) {
    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active");
    });
  }

  // Keyboard shortcuts
  document.addEventListener("keydown", (e) => {
    // Ctrl/Cmd + K untuk fokus pencarian
    if ((e.ctrlKey || e.metaKey) && e.key === "k") {
      e.preventDefault();
      const searchInput = document.querySelector(
        'input[type="search"], input[placeholder*="Cari"]'
      );
      if (searchInput) searchInput.focus();
    }
    // ESC untuk tutup modal
    if (e.key === "Escape") {
      const activeModal = document.querySelector(".modal-overlay.active");
      if (activeModal) {
        activeModal.classList.remove("active");
        document.body.style.overflow = "auto";
      }
    }
  });

  // >>> Inisialisasi Kalender (jika elemen ada di halaman)
  PoscareCalendar.init();
});

// ================================================
// CONSOLE GREETING
// ================================================
console.log(
  "%c PosCare ",
  "background: #246BCE; color: white; font-size: 20px; padding: 10px;"
);
console.log(
  "%c Sistem Admin Digital Posyandu ",
  "background: #79A8EA; color: white; font-size: 14px; padding: 5px;"
);
console.log(
  "%c Developed with ❤️ for Indonesia ",
  "color: #10B981; font-size: 12px;"
);
