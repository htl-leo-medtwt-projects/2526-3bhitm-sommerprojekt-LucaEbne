document.addEventListener('DOMContentLoaded', () => {
    let fileInput = document.getElementById('fileInput');
    let cameraBtn = document.getElementById('camera-btn');
    let previewImg = document.getElementById('preview-img');

    if (!cameraBtn || !fileInput || !previewImg) return; 
    cameraBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            let file = this.files[0];

            let reader = new FileReader();
            reader.onload = (e) => previewImg.src = e.target.result;
            reader.readAsDataURL(file);

            let formData = new FormData();
            formData.append("fileToUpload", file);

            fetch("createAccount.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(data => console.log("Upload-Antwort:", data))
                .catch(err => console.error("Fehler:", err));
        }
    });
});

// Wheel-to-horizontal scroll fallback for sliders (mouse wheel / trackpad)
document.addEventListener('DOMContentLoaded', () => {
    const sliders = document.querySelectorAll('.beach-slider');
    sliders.forEach(slider => {
        slider.addEventListener('wheel', (e) => {
            // Only handle when there's a vertical scroll delta
            if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                e.preventDefault();
                slider.scrollLeft += e.deltaY;
            }
        }, { passive: false });
    });
});