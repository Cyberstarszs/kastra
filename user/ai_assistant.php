<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
$pesan_error = '';
$limit_habis = false;
$pengaturan_sistem = ambil_pengaturan_sistem($koneksi);
$konfigurasi_aplikasi = require __DIR__ . '/../config/app.php';
$api_key_deepseek = (string) ($konfigurasi_aplikasi['ai']['api_key_deepseek'] ?? 'ISI_API_KEY_DEEPSEEK');
$timeout_ai = (int) ($konfigurasi_aplikasi['ai']['timeout'] ?? 30);
$limit_prompt_harian = max(1, (int) ($pengaturan_sistem['limit_prompt'] ?? 15));
$status_akun = sinkron_status_premium($koneksi, $id_user);
$query_user = $koneksi->prepare('SELECT status_user, sisa_prompt, tanggal_reset_prompt FROM users WHERE id_user = :id_user LIMIT 1');
$query_user->execute(['id_user' => $id_user]);
$data_user = $query_user->fetch();
$status_user = $status_akun['status_user'] ?? ($data_user['status_user'] ?? 'biasa');
$sisa_prompt = (int) ($data_user['sisa_prompt'] ?? 15);
$tanggal_reset_prompt = $data_user['tanggal_reset_prompt'] ?? null;
$_SESSION['status_user'] = $status_user;
if ($status_user !== 'premium') {
    $hari_ini = date('Y-m-d');
    if ($tanggal_reset_prompt !== $hari_ini) {
        $reset_prompt = $koneksi->prepare("UPDATE users SET sisa_prompt = :limit_prompt, tanggal_reset_prompt = :hari_ini WHERE id_user = :id_user");
        $reset_prompt->execute([
            'limit_prompt' => $limit_prompt_harian,
            'hari_ini' => $hari_ini,
            'id_user' => $id_user
        ]);
        $sisa_prompt = $limit_prompt_harian;
    }
}
$query_data_keuangan = $koneksi->prepare("SELECT
COALESCE(SUM(CASE WHEN jenis_transaksi='pemasukan' THEN nominal END),0) AS total_pemasukan,
COALESCE(SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN nominal END),0) AS total_pengeluaran
FROM transaksi WHERE id_user = :id_user");
$query_data_keuangan->execute(['id_user' => $id_user]);
$data_keuangan = $query_data_keuangan->fetch();
$query_kategori_terbesar = $koneksi->prepare("SELECT kategori, SUM(nominal) AS total
FROM transaksi
WHERE id_user = :id_user AND jenis_transaksi='pengeluaran'
GROUP BY kategori
ORDER BY total DESC
LIMIT 1");
$query_kategori_terbesar->execute(['id_user' => $id_user]);
$kategori_terbesar = $query_kategori_terbesar->fetch();
$nama_kategori_terbesar = $kategori_terbesar['kategori'] ?? 'Belum ada';
function simpan_chat(PDO $koneksi, int $id_user, string $pesan_user, string $respon_ai): void
{
    $query = $koneksi->prepare('INSERT INTO chat_ai (id_user, pesan_user, respon_ai, waktu) VALUES (:id_user, :pesan_user, :respon_ai, NOW())');
    $query->execute([
        'id_user' => $id_user,
        'pesan_user' => $pesan_user,
        'respon_ai' => $respon_ai,
    ]);
}
function rapikan_respon_ai(string $teks): string
{
    $hasil = trim($teks);
    if ($hasil === '') {
        return $hasil;
    }
    $hasil = preg_replace('/^[ \t]*#{1,6}[ \t]*/m', '', $hasil) ?? $hasil;
    $hasil = str_replace(['**', '__', '```', '`'], '', $hasil);
    $hasil = preg_replace('/^[ \t]*[-•][ \t]*/m', '- ', $hasil) ?? $hasil;
    $hasil = preg_replace('/^[ \t]*\d+\.[ \t]*/m', '- ', $hasil) ?? $hasil;
    $hasil = preg_replace("/\n{3,}/", "\n\n", $hasil) ?? $hasil;
    return trim($hasil);
}
function proses_prompt_ai(PDO $koneksi, int $id_user, string $status_user, int &$sisa_prompt, int $timeout_ai, string $api_key_deepseek, array $data_keuangan, string $nama_kategori_terbesar): array
{
    $pesan_user = trim($_POST['pesan_user'] ?? '');
    if (mb_strlen($pesan_user) < 3) {
        return ['ok' => false, 'pesan' => 'Pertanyaan minimal 3 karakter.'];
    }
    if (mb_strlen($pesan_user) > 600) {
        return ['ok' => false, 'pesan' => 'Pertanyaan terlalu panjang (maksimal 600 karakter).'];
    }
    if ($status_user !== 'premium') {
        $kurangi = $koneksi->prepare("UPDATE users SET sisa_prompt = sisa_prompt - 1, tanggal_reset_prompt = :hari_ini WHERE id_user = :id_user AND sisa_prompt > 0");
        $kurangi->execute([
            'id_user' => $id_user,
            'hari_ini' => date('Y-m-d')
        ]);
        if ($kurangi->rowCount() === 0) {
            return ['ok' => false, 'pesan' => 'Silakan coba kembali.', 'limit_habis' => true, 'sisa_prompt' => $sisa_prompt];
        }
        $sisa_prompt--;
    }
    $ringkasan_prompt = "Data keuangan saya:\n" .
        "Total pemasukan: " . format_rupiah((float) $data_keuangan['total_pemasukan']) . "\n" .
        "Total pengeluaran: " . format_rupiah((float) $data_keuangan['total_pengeluaran']) . "\n" .
        "Kategori terbesar: " . $nama_kategori_terbesar . "\n\n" .
        "Pertanyaan: {$pesan_user}\n\n" .
        "Berikan saran keuangan sederhana, ringkas, dan personal dalam bahasa Indonesia.";
    if (empty($api_key_deepseek) || $api_key_deepseek === 'ISI_API_KEY_DEEPSEEK_DI_SINI' || $api_key_deepseek === 'ISI_API_KEY' || $api_key_deepseek === 'ISI_API_KEY_DEEPSEEK') {
        $respon_ai = 'Silakan isi API Key DeepSeek di berkas .env Anda.';
    } else {
        $payload = [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => 'Kamu adalah asisten keuangan pribadi yang ramah, praktis, dan ringkas. Tulis jawaban rapi tanpa markdown dekoratif. Jangan gunakan simbol seperti **, ##, atau format berlebihan. Gunakan kalimat singkat dan bullet sederhana jika perlu.'],
                ['role' => 'user', 'content' => $ringkasan_prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 400,
        ];
        $ch = curl_init('https://api.deepseek.com/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key_deepseek,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => $timeout_ai > 0 ? $timeout_ai : 30,
        ]);
        $hasil = curl_exec($ch);
        $error_curl = curl_error($ch);
        curl_close($ch);
        if ($error_curl) {
            error_log('DeepSeek cURL error user ' . $id_user . ': ' . $error_curl);
            $respon_ai = 'Terjadi kendala, silakan coba kembali.';
        } else {
            $json = json_decode((string) $hasil, true);
            $respon_ai = trim((string) ($json['choices'][0]['message']['content'] ?? 'AI belum memberikan respon.'));
            if ($respon_ai === '' || isset($json['error'])) {
                if (isset($json['error'])) {
                    error_log('DeepSeek API error user ' . $id_user . ': ' . json_encode($json['error']));
                }
                $respon_ai = 'Terjadi kendala, silakan coba kembali.';
            }
        }
    }
    $respon_ai = rapikan_respon_ai($respon_ai);
    simpan_chat($koneksi, $id_user, $pesan_user, $respon_ai);
    return [
        'ok' => true,
        'pesan_user' => $pesan_user,
        'respon_ai' => $respon_ai,
        'sisa_prompt' => $sisa_prompt,
        'status_user' => $status_user,
    ];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['aksi'] ?? '') === 'hapus_histori') {
        $hapus = $koneksi->prepare('DELETE FROM chat_ai WHERE id_user = :id_user');
        $hapus->execute(['id_user' => $id_user]);
        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'histori_hapus' => true]);
            exit;
        }
        header('Location: ' . url('user/ai_assistant.php'));
        exit;
    }
    $hasil_proses = proses_prompt_ai($koneksi, $id_user, $status_user, $sisa_prompt, $timeout_ai, $api_key_deepseek, $data_keuangan, $nama_kategori_terbesar);
    if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
        header('Content-Type: application/json');
        echo json_encode($hasil_proses);
        exit;
    }
    if (!($hasil_proses['ok'] ?? false)) {
        $pesan_error = $hasil_proses['pesan'] ?? 'Gagal memproses permintaan.';
        $limit_habis = !empty($hasil_proses['limit_habis']);
    } else {
        header('Location: ' . url('user/ai_assistant.php'));
        exit;
    }
}
$query_riwayat = $koneksi->prepare('SELECT * FROM chat_ai WHERE id_user = :id_user ORDER BY waktu ASC, id_chat ASC LIMIT 50');
$query_riwayat->execute(['id_user' => $id_user]);
$riwayat_chat = $query_riwayat->fetchAll();
$judul_halaman = 'Asisten Keuangan';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Asisten AI Finansial</h2>
    <p class="text-muted mb-0">Konsultasikan alokasi anggaran dan dapatkan wawasan keuangan berdasarkan pola transaksi Anda.</p>
  </div>
  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
    <div class="small text-muted" id="statusPromptAI">Status: <span class="badge-status <?= $status_user === 'premium' ? 'badge-premium' : 'badge-biasa' ?>"><?= $status_user === 'premium' ? 'Premium' : 'Standar' ?></span> · Sisa Pertanyaan: <?= $status_user === 'premium' ? 'Tanpa batas' : $sisa_prompt ?></div>
    <button type="button" id="tombolHapusHistoriAI" class="btn btn-light btn-sm" title="Hapus riwayat obrolan">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</div>
<?php if ($pesan_error): ?><div class="alert alert-danger border-0" id="pesanErrorAI"><?= h($pesan_error) ?></div><?php else: ?><div class="alert alert-danger border-0 d-none" id="pesanErrorAI"></div><?php endif; ?>
<?php if ($limit_habis): ?>
<div class="card-modern p-3 mb-3" id="cardLimitAI">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div><strong>Batas penggunaan harian telah tercapai</strong></div>
    <a href="<?= url('user/upgrade_premium.php') ?>" class="btn btn-utama">Tingkatkan ke Premium</a>
  </div>
</div>
<?php else: ?>
<div class="card-modern p-3 mb-3 d-none" id="cardLimitAI">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div><strong>Batas penggunaan harian telah tercapai</strong></div>
    <a href="<?= url('user/upgrade_premium.php') ?>" class="btn btn-utama">Tingkatkan ke Premium</a>
  </div>
</div>
<?php endif; ?>
<div class="card-modern p-3 ai-chat-shell">
  <div id="chatArea" class="chat-box ai-chat-area mb-3">
    <?php if (count($riwayat_chat) === 0): ?>
      <div class="text-center py-5" id="emptyStateAI">
        <div class="icon-btn mx-auto mb-3"><i class="bi bi-robot"></i></div>
        <h5>Asisten AI Finansial</h5>
        <p class="text-muted mb-0">Tanyakan alokasi anggaran, rencana menabung, atau mintalah wawasan dari data keuangan Anda.</p>
      </div>
    <?php else: ?>
      <?php foreach ($riwayat_chat as $chat): ?>
        <div class="chat-row user fade-in-up"><div class="chat-bubble user"><?= nl2br(h($chat['pesan_user'])) ?></div></div>
        <div class="chat-row fade-in-up"><div class="chat-bubble ai"><?= nl2br(h($chat['respon_ai'])) ?></div></div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <form method="post" class="chat-input-wrap ai-input-wrap" id="formChatAI" data-no-loading>
    <input type="hidden" name="ajax" value="1">
    <input type="text" class="form-control" name="pesan_user" id="inputChatAI" maxlength="600" placeholder="Tanyakan tentang keuangan Anda…" required>
    <button class="btn btn-utama" type="submit" id="tombolKirimAI" title="Kirim"><i class="bi bi-send"></i></button>
  </form>
</div>
<script>
(function () {
  const chatArea = document.getElementById('chatArea');
  const formChat = document.getElementById('formChatAI');
  const inputChat = document.getElementById('inputChatAI');
  const tombolKirimAI = document.getElementById('tombolKirimAI');
  const tombolHapusHistoriAI = document.getElementById('tombolHapusHistoriAI');
  const statusPrompt = document.getElementById('statusPromptAI');
  const pesanError = document.getElementById('pesanErrorAI');
  const cardLimit = document.getElementById('cardLimitAI');
  const emptyState = document.getElementById('emptyStateAI');
  if (chatArea) {
    chatArea.scrollTop = chatArea.scrollHeight;
  }
  function escapeHtml(teks) {
    const div = document.createElement('div');
    div.textContent = teks;
    return div.innerHTML;
  }
  function setKirimState(sedangKirim) {
    if (!tombolKirimAI) return;
    tombolKirimAI.disabled = sedangKirim;
    tombolKirimAI.innerHTML = sedangKirim ? '<span class="spinner-border spinner-border-sm"></span>' : '<i class="bi bi-send"></i>';
  }
  function tambahBubble(role, text, isLoading) {
    const row = document.createElement('div');
    row.className = 'chat-row fade-in-up' + (role === 'user' ? ' user' : '');
    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble ' + role;
    if (isLoading) {
      bubble.innerHTML = 'Sedang memproses<span class="typing-dots"><span>.</span><span>.</span><span>.</span></span>';
    } else {
      bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
    }
    row.appendChild(bubble);
    chatArea.appendChild(row);
    chatArea.scrollTop = chatArea.scrollHeight;
    return row;
  }
  if (formChat && inputChat) {
    formChat.addEventListener('submit', async function (e) {
      e.preventDefault();
      const pesan = inputChat.value.trim();
      if (!pesan) return;
      if (emptyState) {
        emptyState.remove();
      }
      if (pesanError) {
        pesanError.classList.add('d-none');
        pesanError.textContent = '';
      }
      tambahBubble('user', pesan, false);
      inputChat.value = '';
      const loadingRow = tambahBubble('ai', '', true);
      setKirimState(true);
      try {
        const formData = new FormData(formChat);
        formData.set('pesan_user', pesan);
        formData.set('ajax', '1');
        const response = await fetch(window.location.href, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const data = await response.json();
        loadingRow.remove();
        if (!data.ok) {
          tambahBubble('ai', data.pesan || 'Gagal memproses permintaan.', false);
          if (pesanError) {
            pesanError.textContent = data.pesan || 'Gagal memproses permintaan.';
            pesanError.classList.remove('d-none');
          }
          if (data.limit_habis && cardLimit) {
            cardLimit.classList.remove('d-none');
          }
          return;
        }
        tambahBubble('ai', data.respon_ai || 'Terjadi kendala, silakan coba kembali.', false);
        if (statusPrompt) {
          const premium = data.status_user === 'premium';
          const badgeClass = premium ? 'badge-premium' : 'badge-biasa';
          const badgeText = premium ? 'Premium' : 'Standar';
          const sisa = premium ? 'Tanpa batas' : data.sisa_prompt;
          statusPrompt.innerHTML = 'Status: <span class="badge-status ' + badgeClass + '">' + badgeText + '</span> · Sisa Pertanyaan: ' + sisa;
        }
      } catch (error) {
        loadingRow.remove();
        tambahBubble('ai', 'Terjadi kendala, silakan coba kembali.', false);
      } finally {
        setKirimState(false);
        inputChat.focus();
      }
    });
    inputChat.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        formChat.requestSubmit();
      }
    });
  }
  if (tombolHapusHistoriAI) {
    tombolHapusHistoriAI.addEventListener('click', async function () {
      const yakin = confirm('Hapus semua riwayat percakapan?');
      if (!yakin) return;
      try {
        const formData = new FormData();
        formData.set('aksi', 'hapus_histori');
        formData.set('ajax', '1');
        const response = await fetch(window.location.href, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const data = await response.json();
        if (!data.ok) {
          if (typeof tampilToast === 'function') {
            tampilToast('Gagal memproses permintaan.', 'gagal');
          }
          return;
        }
        chatArea.innerHTML = '<div class="text-center py-5" id="emptyStateAI"><div class="icon-btn mx-auto mb-3"><i class="bi bi-robot"></i></div><h5>Asisten AI Finansial</h5><p class="text-muted mb-0">Tanyakan alokasi anggaran, rencana menabung, atau mintalah wawasan dari data keuangan Anda.</p></div>';
        if (typeof tampilToast === 'function') {
          tampilToast('Perubahan berhasil diterapkan.', 'sukses');
        }
      } catch (error) {
        if (typeof tampilToast === 'function') {
          tampilToast('Gagal memproses permintaan.', 'gagal');
        }
      }
    });
  }
})();
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
