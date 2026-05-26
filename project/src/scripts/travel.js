function showPreviews(files, container, single) {
    if (single) container.innerHTML = '';
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;
            if (container.classList.contains('preview-gallery') || container.id === 'photos-preview') {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.appendChild(img);
                container.appendChild(item);
            } else {
                container.appendChild(img);
            }

            const zone = container.closest('.upload-zone');
            zone.querySelectorAll('.zone-icon, .zone-text').forEach(el => {
                el.style.opacity = '0';
            });
            zone.classList.add('has-preview');
        };
        reader.readAsDataURL(file);
    });
}

function setupUploadZone(zoneId, inputId, previewId, single) {
    const zone = document.getElementById(zoneId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        const files = single ? [e.dataTransfer.files[0]] : e.dataTransfer.files;
        showPreviews(files, preview, single);

    });
    input.addEventListener('change', () => showPreviews(input.files, preview, single));
}

setupUploadZone('cover-zone', 'cover', 'cover-preview', true);
setupUploadZone('photos-zone', 'photos', 'photos-preview', false);

const categoryRatings = Array.from(document.querySelectorAll('.category-rating'));
const overallRatingText = document.getElementById('overall-rating-text');
const overallRatingInput = document.getElementById('rating-value');

if (categoryRatings.length) {
    const categoryState = categoryRatings.map(container => {
        const stars = Array.from(container.querySelectorAll('i'));
        const row = container.closest('.rating-criterion');
        const valueText = row ? row.querySelector('.criterion-value') : null;
        const inputId = container.dataset.input || '';
        const hiddenInput = inputId ? document.getElementById(inputId) : null;
        const startValue = Math.max(1, Math.min(5, parseInt(hiddenInput?.value || '1', 10) || 1));

        const render = (value) => {
            stars.forEach((star, index) => {
                const isFilled = index < value;
                star.classList.toggle('fa-solid', isFilled);
                star.classList.toggle('fa-regular', !isFilled);
                star.classList.toggle('is-filled', isFilled);
                star.classList.toggle('is-empty', !isFilled);
            });

            if (valueText) {
                valueText.textContent = value.toFixed(1);
            }
        };

        return {
            container,
            stars,
            hiddenInput,
            render,
            value: startValue
        };
    });

    const updateOverallRating = () => {
        const sum = categoryState.reduce((acc, item) => acc + item.value, 0);
        const average = sum / categoryState.length;
        const roundedAverage = Math.round(average * 10) / 10;

        if (overallRatingText) {
            overallRatingText.textContent = roundedAverage.toFixed(1);
        }

        if (overallRatingInput) {
            overallRatingInput.value = roundedAverage.toFixed(1);
        }
    };

    categoryState.forEach(item => {
        const setValue = (value) => {
            item.value = value;
            if (item.hiddenInput) {
                item.hiddenInput.value = String(value);
            }
            item.render(value);
            updateOverallRating();
        };

        item.container.addEventListener('click', (event) => {
            const targetStar = event.target.closest('i');
            if (!targetStar) {
                return;
            }

            const index = item.stars.indexOf(targetStar);
            if (index > -1) {
                setValue(index + 1);
            }
        });

        item.container.addEventListener('mouseover', (event) => {
            const targetStar = event.target.closest('i');
            if (!targetStar) {
                return;
            }

            const index = item.stars.indexOf(targetStar);
            if (index > -1) {
                item.render(index + 1);
            }
        });

        item.container.addEventListener('mouseleave', () => {
            item.render(item.value);
        });

        item.render(item.value);
    });

    updateOverallRating();
}