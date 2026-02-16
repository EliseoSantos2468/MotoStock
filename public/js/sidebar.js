document.addEventListener("livewire:navigated", function () {
    const toggleDropdown = (dropdown, menu, isOpen) => {
        dropdown.classList.toggle("open", isOpen);
        menu.style.height = isOpen ? `${menu.scrollHeight}px` : 0;
    };

    const closeAllDropdowns = () => {
        document
            .querySelectorAll(".dropdown-container.open")
            .forEach((openDropdown) => {
                toggleDropdown(
                    openDropdown,
                    openDropdown.querySelector(".dropdown-menu"),
                    false,
                );
            });
    };

    document.querySelectorAll(".dropdown-toggler").forEach((dropdownToggle) => {
        dropdownToggle.addEventListener("click", (e) => {
            e.preventDefault();
            const dropdown = e.target.closest(".dropdown-container");
            const menu = dropdown.querySelector(".dropdown-menu");
            const isOpen = dropdown.classList.contains("open");

            closeAllDropdowns();
            toggleDropdown(dropdown, menu, !isOpen);
        });
    });

    document
        .querySelectorAll(".sidebar-toggler, .sidebar-menu-buttom")
        .forEach((button) => {
            button.addEventListener("click", () => {
                closeAllDropdowns();
                document
                    .querySelector(".sidebar")
                    .classList.toggle("collapsed");
            });
        });

    if (window.innerWidth <= 1024) {
        const side = document.querySelector(".sidebar");
        if (side) side.classList.add("collapsed");
    }

    const mobileBtn = document.getElementById("mobile-menu-btn");
    const closeBtn = document.getElementById("close-sidebar-btn");
    const sidebar = document.getElementById("main-sidebar");
    const overlay = document.getElementById("sidebar-overlay");

    const toggleMobileSidebar = () => {
        if (sidebar && overlay) {
            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");
        }
    };

    if (mobileBtn) mobileBtn.addEventListener("click", toggleMobileSidebar);
    if (closeBtn) closeBtn.addEventListener("click", toggleMobileSidebar);
    if (overlay) overlay.addEventListener("click", toggleMobileSidebar);
});
