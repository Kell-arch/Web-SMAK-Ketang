function openModal(id) {
    document.getElementById(id).classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

function confirmHapus(id, nama) {
    if (confirm('Yakin ingin menghapus "' + nama + '"?')) {
        document.getElementById('hapus_id').value = id;
        document.getElementById('formHapus').submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal-overlay').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });

    var sidebar = document.querySelector('.sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle = document.getElementById('mobileToggle');
    if (toggle && sidebar && overlay) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }
});
