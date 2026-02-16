document.addEventListener("DOMContentLoaded", function () {
    const mobileBtn = document.getElementById("mobile-menu-btn");
    const closeBtn = document.getElementById("close-sidebar-btn");
    const sidebar = document.getElementById("main-sidebar");
    const overlay = document.getElementById("sidebar-overlay");

    function toggleSidebar() {
        sidebar.classList.toggle("-translate-x-full");
        overlay.classList.toggle("hidden");
    }

    if (mobileBtn) {
        mobileBtn.addEventListener("click", toggleSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener("click", toggleSidebar);
    }
});
