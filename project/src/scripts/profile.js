function showTab(name, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

async function removeFavouriteIsland(button) {
    const islandCard = button.closest('.island-card');
    if (!islandCard) return;

    const islandId = button.dataset.id;
    if (!islandId) return;

    button.disabled = true;

    try {
        const response = await fetch('../../src/php/toggle-favourite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ island_id: parseInt(islandId, 10) })
        });

        const result = await response.json();

        if (!response.ok || !result.success || result.action !== 'removed') {
            throw new Error(result.reason || 'Could not remove favourite');
        }

        islandCard.remove();

        const favouriteCountEl = document.querySelector('.stats-container .stat-box .stat-number');
        if (favouriteCountEl) {
            const currentCount = parseInt(favouriteCountEl.textContent || '0', 10);
            favouriteCountEl.textContent = String(Math.max(0, currentCount - 1));
        }

        const cardsGrid = document.querySelector('.cards-grid');
        if (cardsGrid && !cardsGrid.querySelector('.island-card')) {
            cardsGrid.innerHTML = '<p style="color:#888; padding: 24px 0;">Du hast noch keine Favourites gespeichert.</p>';
        }
    } catch (error) {
        console.error('Failed to remove favourite island:', error);
        button.disabled = false;
    }
}

document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('.btn-remove');
    if (!removeButton) return;

    event.preventDefault();
    removeFavouriteIsland(removeButton);
});

(function () {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab) {
        const btn = document.querySelector(`.tab-btn[onclick*="'${tab}'"]`);
        if (btn) showTab(tab, btn);
    }
})();