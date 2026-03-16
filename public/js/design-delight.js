function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('overlay').classList.toggle('hidden');
}

function toggleRightPanel() {
    document.getElementById('rightPanel').classList.toggle('translate-x-full');
    document.getElementById('overlay').classList.toggle('hidden');
}

function closePanels() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('rightPanel').classList.add('translate-x-full');
    document.getElementById('overlay').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
