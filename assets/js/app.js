function konfirmasiHapus(pesan = 'Yakin ingin menghapus data ini?') {
  return confirm(pesan);
}

function toggleSidebar() {
  const sidebar = document.getElementById('sidebarUtama');
  if (sidebar) {
    sidebar.classList.toggle('show');
  }
}

document.addEventListener('click', function (event) {
  const sidebar = document.getElementById('sidebarUtama');
  const tombol = document.getElementById('tombolSidebar');
  if (!sidebar || !tombol || window.innerWidth > 767) return;

  const klikDiSidebar = sidebar.contains(event.target);
  const klikDiTombol = tombol.contains(event.target);
  if (!klikDiSidebar && !klikDiTombol) {
    sidebar.classList.remove('show');
  }
});

function tampilToast(pesan, tipe = 'sukses') {
  const area = document.getElementById('globalToastArea');
  if (!area) return;

  const item = document.createElement('div');
  item.className = 'toast-item ' + (tipe === 'gagal' ? 'gagal' : 'sukses');
  item.textContent = pesan;
  area.appendChild(item);

  setTimeout(() => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(-4px)';
    item.style.transition = 'all .2s ease';
    setTimeout(() => item.remove(), 220);
  }, 2800);
}

function setGlobalLoader(tampil) {
  const loader = document.getElementById('globalPageLoader');
  if (!loader) return;
  loader.classList.toggle('d-none', !tampil);
}

document.addEventListener('DOMContentLoaded', function () {
  document.body.classList.add('page-ready');

  const params = new URLSearchParams(window.location.search);
  if (params.has('sukses')) tampilToast('Berhasil disimpan.', 'sukses');
  if (params.has('hapus')) tampilToast('Perubahan berhasil diterapkan.', 'sukses');

  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', function () {
      const tombolSubmit = form.querySelector('button[type="submit"], .btn[type="submit"]');
      if (tombolSubmit && !tombolSubmit.disabled) {
        tombolSubmit.disabled = true;
        const teksAsli = tombolSubmit.innerHTML;
        tombolSubmit.dataset.teksAsli = teksAsli;
        tombolSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses…';
      }

      if (!form.hasAttribute('data-no-loading')) {
        setTimeout(() => setGlobalLoader(true), 120);
      }
    });
  });

  document.addEventListener('click', function (event) {
    const link = event.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href') || '';
    const abaikan = href.startsWith('#') ||
      href.startsWith('javascript:') ||
      link.getAttribute('target') === '_blank' ||
      link.hasAttribute('download');

    if (abaikan) return;
    if (link.origin !== window.location.origin) return;

    setGlobalLoader(true);
  });

  window.addEventListener('pageshow', function () {
    setGlobalLoader(false);
  });
});
