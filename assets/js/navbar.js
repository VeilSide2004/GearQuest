const texts = ["The adventure Begins here", "Refine Your Adventures", "Make more Memories", "Journeys become Memories", "Start your Own Adventure now", "We have everything you need to make your adventure unforgettable", ];
let textIndex = 0;
let charIndex = 0;
let isDeleting = false;
const typingSpeed = 100; // Speed of typing
const erasingSpeed = 50; // Speed of backspacing
const delayBetweenTexts = 2000; // Delay before starting the next text
const textElement = document.getElementById("dynamic-text");

function typeEffect() {
    const currentText = texts[textIndex];
    
    if (isDeleting) {
        textElement.textContent = currentText.substring(0, charIndex - 1);
        charIndex--;
    } else {
        textElement.textContent = currentText.substring(0, charIndex + 1);
        charIndex++;
    }

    if (!isDeleting && charIndex === currentText.length) {
        setTimeout(() => isDeleting = true, delayBetweenTexts);
    } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        textIndex = (textIndex + 1) % texts.length;
    }

    setTimeout(typeEffect, isDeleting ? erasingSpeed : typingSpeed);
}

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(typeEffect, 1000);
});
