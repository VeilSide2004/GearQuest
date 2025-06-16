document.addEventListener("DOMContentLoaded", () => {
    const toggleButton = document.getElementById('dark-mode-toggle');
    const body = document.body;

    // Check local storage for dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        toggleButton.innerText = "☀️"; // Set to sun icon
    }

    // Toggle dark mode on button click
    toggleButton.addEventListener('click', () => {
        body.classList.toggle('dark-mode');

        // Update button text and local storage
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            toggleButton.innerText = "☀️"; // Switch to light mode icon
        } else {
            localStorage.setItem('darkMode', 'disabled');
            toggleButton.innerText = "🌙"; // Switch to dark mode icon
        }
    });
});
