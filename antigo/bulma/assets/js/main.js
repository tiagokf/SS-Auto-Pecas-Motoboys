document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggle-sidebar");
    const sidebar = document.getElementById("side-menu");
    const mainContent = document.querySelector(".main-content");

    toggleButton.addEventListener("click", function () {
        sidebar.classList.toggle("collapsed");
        mainContent.style.marginLeft = sidebar.classList.contains("collapsed") ? "0" : "250px";
    });
});
