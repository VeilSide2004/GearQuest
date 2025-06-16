document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".add-to-cart");

    buttons.forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            button.innerText = "✔ Added!";
            button.style.background = "#4CAF50";  // Green success color
            setTimeout(() => {
                button.innerText = "🛒 Add to Cart";
                button.style.background = "linear-gradient(to right, #6A5ACD, #836FFF)";
            }, 2000);
        });
    });
});