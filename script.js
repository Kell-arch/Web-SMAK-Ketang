function escapeHtml(t) {
    var d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

/* ===== PENGUMUMAN MODAL ===== */
var pengumumanModalSlide = 0;

function openPengumuman(p) {
    var content = '';
    var gambarList = p.gambar_list || [];
    var jml = gambarList.length;

    if (jml > 0) {
        content += '<div class="pengumuman-modal-carousel" data-slide="0">';
        content += '<div class="pengumuman-modal-carousel-track">';
        for (var i = 0; i < jml; i++) {
            content += '<div class="pengumuman-modal-carousel-slide"><img src="assets/uploads/pengumuman/' + gambarList[i] + '" alt="' + p.judul + '"></div>';
        }
        content += '</div>';
        if (jml > 1) {
            content += '<button class="pengumuman-modal-nav prev" onclick="modalCarousel(-1)">&#8249;</button>';
            content += '<button class="pengumuman-modal-nav next" onclick="modalCarousel(1)">&#8250;</button>';
            content += '<div class="pengumuman-modal-dots">';
            for (var i = 0; i < jml; i++) {
                content += '<span class="' + (i === 0 ? 'active' : '') + '" data-index="' + i + '"></span>';
            }
            content += '</div>';
        }
        content += '</div>';
    }

    content += '<div class="pengumuman-modal-body">';
    content += '<h3 class="pengumuman-modal-judul">' + escapeHtml(p.judul) + '</h3>';
    content += '<span class="pengumuman-modal-tanggal">' + escapeHtml(p.tanggal) + '</span>';
    content += '<div class="pengumuman-modal-isi">' + (p.isi || '').replace(/\n/g, '<br>') + '</div>';
    if (p.penulis) {
        content += '<span class="pengumuman-modal-penulis">\u2014 ' + escapeHtml(p.penulis) + '</span>';
    }
    content += '</div>';

    document.getElementById('pengumumanModalContent').innerHTML = content;
    document.getElementById('pengumumanModal').classList.add('show');
    pengumumanModalSlide = 0;
}

function closePengumuman() {
    document.getElementById('pengumumanModal').classList.remove('show');
}

function modalCarousel(dir) {
    var carousel = document.querySelector('.pengumuman-modal-carousel');
    if (!carousel) return;
    var slides = carousel.querySelectorAll('.pengumuman-modal-carousel-slide');
    var total = slides.length;
    pengumumanModalSlide = (pengumumanModalSlide + dir + total) % total;
    carousel.querySelector('.pengumuman-modal-carousel-track').style.transform = 'translateX(-' + (pengumumanModalSlide * 100) + '%)';
    var dots = carousel.querySelectorAll('.pengumuman-modal-dots span');
    dots.forEach(function(d, i) {
        d.classList.toggle('active', i === pengumumanModalSlide);
    });
}

/* ===== GALERI MODAL ===== */
var galeriSlide = 0;

function openGaleri(d) {
    var content = '';
    var gambarList = d.gambar_list || [];
    var jml = gambarList.length;

    if (jml > 0) {
        content += '<div class="galeri-modal-carousel" data-slide="0">';
        content += '<div class="galeri-modal-carousel-track">';
        for (var i = 0; i < jml; i++) {
            content += '<div class="galeri-modal-carousel-slide"><img src="assets/uploads/dokumentasi/' + gambarList[i] + '" alt="' + d.judul + '"></div>';
        }
        content += '</div>';
        if (jml > 1) {
            content += '<button class="galeri-modal-nav prev" onclick="galeriCarousel(-1)">&#8249;</button>';
            content += '<button class="galeri-modal-nav next" onclick="galeriCarousel(1)">&#8250;</button>';
            content += '<div class="galeri-modal-dots">';
            for (var i = 0; i < jml; i++) {
                content += '<span class="' + (i === 0 ? 'active' : '') + '" data-index="' + i + '"></span>';
            }
            content += '</div>';
        }
        content += '</div>';
    }

    content += '<div class="galeri-modal-body">';
    content += '<h3 class="galeri-modal-judul">' + escapeHtml(d.judul) + '</h3>';
    content += '<span class="galeri-modal-tanggal">' + escapeHtml(d.tanggal) + '</span>';
    if (d.deskripsi) {
        content += '<div class="galeri-modal-deskripsi">' + (d.deskripsi || '').replace(/\n/g, '<br>') + '</div>';
    }
    content += '</div>';

    document.getElementById('galeriModalContent').innerHTML = content;
    document.getElementById('galeriModal').classList.add('show');
    galeriSlide = 0;
}

function closeGaleri() {
    document.getElementById('galeriModal').classList.remove('show');
}

function galeriCarousel(dir) {
    var carousel = document.querySelector('.galeri-modal-carousel');
    if (!carousel) return;
    var slides = carousel.querySelectorAll('.galeri-modal-carousel-slide');
    var total = slides.length;
    galeriSlide = (galeriSlide + dir + total) % total;
    carousel.querySelector('.galeri-modal-carousel-track').style.transform = 'translateX(-' + (galeriSlide * 100) + '%)';
    var dots = carousel.querySelectorAll('.galeri-modal-dots span');
    dots.forEach(function(d, i) {
        d.classList.toggle('active', i === galeriSlide);
    });
}

document.addEventListener('DOMContentLoaded', function () {

    /* ===== HAMBURGER TOGGLE ===== */
    var hamburger = document.getElementById('hamburger');
    var navMenu = document.getElementById('nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            navMenu.classList.toggle('open');
        });

        navMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                hamburger.classList.remove('active');
                navMenu.classList.remove('open');
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('nav')) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('open');
            }
        });
    }

    /* ===== COUNTER ANIMATION ===== */
    var counters = document.querySelectorAll('.data-sekolah p data');
    var speed = 200;

    counters.forEach(function (counter) {
        var originalText = counter.innerText;
        var target = parseInt(originalText.replace(/\D/g, ''));
        var suffix = originalText.replace(/[0-9]/g, '');
        var count = 0;
        var increment = target / speed;

        function updateCount() {
            count += increment;
            if (count < target) {
                counter.innerText = Math.ceil(count) + suffix;
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target + suffix;
            }
        }
        updateCount();
    });

    /* ===== PENGUMUMAN DOT CLICK (INLINE) ===== */
    document.addEventListener('click', function (e) {
        var dot = e.target.closest('.pengumuman-carousel-dots span');
        if (dot) {
            var carousel = dot.closest('.pengumuman-carousel');
            if (!carousel) return;
            var index = parseInt(dot.getAttribute('data-index'));
            var track = carousel.querySelector('.pengumuman-carousel-track');
            var slides = carousel.querySelectorAll('.pengumuman-carousel-slide');
            if (!track || !slides.length) return;
            carousel.setAttribute('data-slide', index);
            track.style.transform = 'translateX(-' + (index * 100) + '%)';
            carousel.querySelectorAll('.pengumuman-carousel-dots span').forEach(function (d2, i2) {
                d2.classList.toggle('active', i2 === index);
            });
        }
    });

    /* ===== PENGUMUMAN MODAL DOT CLICK ===== */
    document.addEventListener('click', function (e) {
        var dot = e.target.closest('.pengumuman-modal-dots span');
        if (dot) {
            var index = parseInt(dot.getAttribute('data-index'));
            var carousel = document.querySelector('.pengumuman-modal-carousel');
            if (!carousel) return;
            pengumumanModalSlide = index;
            carousel.querySelector('.pengumuman-modal-carousel-track').style.transform = 'translateX(-' + (index * 100) + '%)';
            carousel.querySelectorAll('.pengumuman-modal-dots span').forEach(function (d2, i2) {
                d2.classList.toggle('active', i2 === index);
            });
        }
    });

    /* ===== PENGUMUMAN MODAL BACKDROP CLOSE ===== */
    var pengumumanModal = document.getElementById('pengumumanModal');
    if (pengumumanModal) {
        pengumumanModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closePengumuman();
            }
        });
    }

    /* ===== PENGUMUMAN CAROUSEL DRAG / SWIPE ===== */
    document.querySelectorAll('.pengumuman-carousel').forEach(function (c) {
        var track = c.querySelector('.pengumuman-carousel-track');
        if (!track) return;
        var startX = 0,
            currentX = 0,
            isDragging = false;

        c.addEventListener('mousedown', function (e) {
            startX = e.clientX;
            isDragging = true;
            currentX = e.clientX;
        });
        c.addEventListener('mousemove', function (e) {
            if (isDragging) currentX = e.clientX;
        });
        c.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            var diff = currentX - startX;
            if (Math.abs(diff) > 50) {
                var slides = c.querySelectorAll('.pengumuman-carousel-slide');
                var cur = parseInt(c.getAttribute('data-slide') || '0');
                var next = diff < 0 ? Math.min(cur + 1, slides.length - 1) : Math.max(cur - 1, 0);
                if (next !== cur) {
                    c.setAttribute('data-slide', next);
                    track.style.transform = 'translateX(-' + (next * 100) + '%)';
                    c.querySelectorAll('.pengumuman-carousel-dots span').forEach(function (d2, i2) {
                        d2.classList.toggle('active', i2 === next);
                    });
                }
            }
        });
        c.addEventListener('mouseleave', function () {
            isDragging = false;
        });

        c.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            currentX = startX;
        });
        c.addEventListener('touchmove', function (e) {
            currentX = e.touches[0].clientX;
        });
        c.addEventListener('touchend', function () {
            var diff = currentX - startX;
            if (Math.abs(diff) > 50) {
                var slides = c.querySelectorAll('.pengumuman-carousel-slide');
                var cur = parseInt(c.getAttribute('data-slide') || '0');
                var next = diff < 0 ? Math.min(cur + 1, slides.length - 1) : Math.max(cur - 1, 0);
                if (next !== cur) {
                    c.setAttribute('data-slide', next);
                    track.style.transform = 'translateX(-' + (next * 100) + '%)';
                    c.querySelectorAll('.pengumuman-carousel-dots span').forEach(function (d2, i2) {
                        d2.classList.toggle('active', i2 === next);
                    });
                }
            }
        });
    });

    /* ===== GALERI MODAL DOT CLICK ===== */
    document.addEventListener('click', function (e) {
        var dot = e.target.closest('.galeri-modal-dots span');
        if (dot) {
            var index = parseInt(dot.getAttribute('data-index'));
            var carousel = document.querySelector('.galeri-modal-carousel');
            if (!carousel) return;
            galeriSlide = index;
            carousel.querySelector('.galeri-modal-carousel-track').style.transform = 'translateX(-' + (index * 100) + '%)';
            carousel.querySelectorAll('.galeri-modal-dots span').forEach(function (d2, i2) {
                d2.classList.toggle('active', i2 === index);
            });
        }
    });

    /* ===== GALERI MODAL BACKDROP CLOSE ===== */
    var galeriModal = document.getElementById('galeriModal');
    if (galeriModal) {
        galeriModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeGaleri();
            }
        });
    }
});
