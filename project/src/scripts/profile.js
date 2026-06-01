function showTab(name, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

(function () {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab) {
        const btn = document.querySelector(`.tab-btn[onclick*="'${tab}'"]`);
        if (btn) showTab(tab, btn);
    }
})();