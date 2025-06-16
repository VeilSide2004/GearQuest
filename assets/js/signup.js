document.addEventListener("DOMContentLoaded", function () {
    // Select elements
    const elements = document.querySelectorAll("h1, p, .right-box");
    const signupBox = document.querySelector(".signup-box");
    const leftImage = document.querySelector(".left-box"); // Select Image

    // Hide elements initially
    elements.forEach((el) => {
        el.style.opacity = "0";
        el.style.visibility = "hidden";
        el.style.transform = "translateY(30px)";
    });

    // Image slides from left (Fix)
    if (leftImage) {
        leftImage.style.transform = "translateX(-50px)";
    }

    // Signup box starts off-screen (Right side)
    if (signupBox) {
        signupBox.style.opacity = "0";
        signupBox.style.visibility = "hidden";
        signupBox.style.transform = "translateX(100px)"; // Swipe effect
    }

    // Start animations
    requestAnimationFrame(() => {
        setTimeout(() => {
            // Fade-in & movement for text
            elements.forEach((el) => {
                el.style.transition = "opacity 1.2s ease-out, transform 1.2s ease-out";
                el.style.opacity = "1";
                el.style.visibility = "visible";
                el.style.transform = "translateY(0)";
            });

            // Ensure image slides in at the same time
            if (leftImage) {
                leftImage.style.transition = "transform 1.2s ease-out";
                leftImage.style.transform = "translateX(0)";
            }

            // Swipe-in animation for signup box
            if (signupBox) {
                signupBox.style.transition = "opacity 1.2s ease-out, transform 1.2s ease-out";
                signupBox.style.opacity = "1";
                signupBox.style.visibility = "visible";
                signupBox.style.transform = "translateX(0)";
            }
        }, 300); // Delay to sync animations
    });
});
