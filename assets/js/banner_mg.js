document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.querySelector('input[type="file"]');
    const previewContainer = document.getElementById("preview-container");
    const previewImage = document.getElementById("preview-image");

    // Show image preview before upload
    fileInput.addEventListener("change", function () {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    });

    // Confirm before deleting
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function (e) {
            if (!confirm("Are you sure you want to delete this banner?")) {
                e.preventDefault();
            }
        });
    });
    
});
