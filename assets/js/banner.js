document.addEventListener("DOMContentLoaded", function () {
    fetch("banner.php")
        .then(response => response.json())
        .then(images => {
            if (images.length === 0) return;

            let currentIndex = 0;
            const bannerImage = document.getElementById("banner-image");

            function changeBanner() {
                bannerImage.src = images[currentIndex];
                currentIndex = (currentIndex + 1) % images.length;
            }

            // Initial Banner Load
            changeBanner();

            // Change banner every 5 seconds
            setInterval(changeBanner, 5000);
        })
        .catch(error => console.error("Error loading banners:", error));
});
