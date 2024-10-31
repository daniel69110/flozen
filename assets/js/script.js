document.getElementById('menu-burger').addEventListener('click', function() {
    document.getElementById('menu-content').classList.remove('hidden');
});

document.getElementById('close').addEventListener('click', function() {
    document.getElementById('menu-content').classList.add('hidden');
});
