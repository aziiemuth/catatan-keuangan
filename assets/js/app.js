/**
 * Catatan Keuangan — Main Application Script
 * Dark mode, sidebar toggle, SweetAlert2 wrappers
 */

document.addEventListener("DOMContentLoaded", function () {
  // ========== DARK MODE ==========
  const themeToggle = document.getElementById("themeToggle");
  const html = document.documentElement;

  // Load saved theme
  const savedTheme = localStorage.getItem("ck-theme") || "light";
  html.setAttribute("data-theme", savedTheme);
  updateThemeIcon(savedTheme);

  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      const current = html.getAttribute("data-theme");
      const next = current === "dark" ? "light" : "dark";
      html.setAttribute("data-theme", next);
      localStorage.setItem("ck-theme", next);
      updateThemeIcon(next);
    });
  }

  function updateThemeIcon(theme) {
    const icon = document.getElementById("themeIcon");
    if (icon) {
      icon.className = theme === "dark" ? "fas fa-sun" : "fas fa-moon";
    }
  }

  // ========== SIDEBAR TOGGLE (MOBILE) ==========
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const sidebar = document.getElementById("sidebar");
  const sidebarOverlay = document.getElementById("sidebarOverlay");

  if (hamburgerBtn && sidebar) {
    hamburgerBtn.addEventListener("click", function () {
      sidebar.classList.toggle("active");
      if (sidebarOverlay) {
        sidebarOverlay.classList.toggle("active");
        sidebarOverlay.style.display = sidebar.classList.contains("active")
          ? "block"
          : "none";
      }
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", function () {
      sidebar.classList.remove("active");
      sidebarOverlay.classList.remove("active");
      sidebarOverlay.style.display = "none";
    });
  }

  // ========== ACTIVE NAV LINK ==========
  const currentPage = window.location.pathname.split("/").pop() || "index.php";
  document.querySelectorAll(".nav-link[data-page]").forEach(function (link) {
    if (link.getAttribute("data-page") === currentPage) {
      link.classList.add("active");
    }
  });

  // ========== FLASH MESSAGES (SweetAlert2) ==========
  const flashEl = document.getElementById("flashMessage");
  if (flashEl) {
    const type = flashEl.getAttribute("data-type") || "success";
    const msg = flashEl.getAttribute("data-message") || "";
    if (msg) {
      Swal.fire({
        icon: type,
        title:
          type === "success"
            ? "Berhasil!"
            : type === "error"
              ? "Gagal!"
              : "Info",
        text: msg,
        timer: 2500,
        showConfirmButton: false,
        toast: true,
        position: "top-end",
      });
    }
  }

  // ========== FADE-IN ANIMATION ==========
  document.querySelectorAll(".animate-fade-up").forEach(function (el, i) {
    el.style.animationDelay = i * 0.05 + "s";
  });
});

// ========== SWEETALERT2 GLOBAL FUNCTIONS ==========

/**
 * Show delete confirmation dialog
 * @param {string} url - URL to redirect to on confirm
 * @param {string} itemName - Name of item being deleted (optional)
 */
function confirmDelete(url, itemName) {
  Swal.fire({
    title: "Hapus Data?",
    text: itemName
      ? 'Yakin ingin menghapus "' + itemName + '"?'
      : "Data yang dihapus tidak dapat dikembalikan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#64748b",
    confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
    cancelButtonText: "Batal",
  }).then(function (result) {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}

/**
 * Show success notification
 */
function showSuccess(message) {
  Swal.fire({
    icon: "success",
    title: "Berhasil!",
    text: message,
    timer: 2000,
    showConfirmButton: false,
  });
}

/**
 * Show error notification
 */
function showError(message) {
  Swal.fire({
    icon: "error",
    title: "Gagal!",
    text: message,
  });
}
