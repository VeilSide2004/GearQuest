document.addEventListener("DOMContentLoaded", function () {
    console.log("Sidebar JS is loaded!"); // Debugging

    let menuIcon = document.querySelector(".menu-icon");
    let sidebar = document.querySelector(".sidebar");

    if (!menuIcon || !sidebar) {
        console.error("Sidebar or menu icon not found!");
        return;
    }

    menuIcon.addEventListener("click", function () {
        console.log("Menu icon clicked!"); // Debugging
        sidebar.classList.toggle("active");

        let bars = menuIcon.querySelectorAll("div");
        if (sidebar.classList.contains("active")) {
            bars[0].style.transform = "rotate(45deg) translate(5px, 5px)";
            bars[1].style.opacity = "0";
            bars[2].style.transform = "rotate(-45deg) translate(5px, -5px)";
        } else {
            bars[0].style.transform = "none";
            bars[1].style.opacity = "1";
            bars[2].style.transform = "none";
        }
    });

    // Ensure the sidebar appears in the correct position
    function adjustSidebarPosition() {
        let navbarHeight = document.querySelector(".navbar").offsetHeight;
        sidebar.style.top = navbarHeight + "px"; // Position below navbar
    }

    window.addEventListener("resize", adjustSidebarPosition);
    adjustSidebarPosition(); // Adjust on load
});
