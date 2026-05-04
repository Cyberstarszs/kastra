<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$api_key_deepseek = 'ISI_API_KEY_DEEPSEEK_DI_SINI';
$id_user = (int) $_SESSION['id_user'];
$respon_ai = '';
$pertanyaan_user = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pertanyaan_user = trim($_POST['pertanyaan'] ?? '');

    $q = $koneksi->prepare('SELECT jenis_transaksi, kategori, nominal, tanggal FROM transaksi WHERE id_user=:id_user ORDER BY tanggal DESC LIMIT 10');
    $q->execute(['id_user' => $id_user]);
    $riwayat = $q->fetchAll();

    $ringkasan = "";
    foreach ($riwayat as $r) {
        $ringkasan .= "- {$r['tanggal']} | {$r['jenis_transaksi']} | {$r['kategori']} | {$r['nominal']}\n";
    }

    $prompt = "Berikut data keuangan saya:\n{$ringkasan}\nPertanyaan saya: {$pertanyaan_user}\nBerikan saran keuangan sederhana, ringkas, dan praktis.";

    if ($api_key_deepseek !== 'ISI_API_KEY_DEEPSEEK_DI_SINI') {
        $payload = [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => 'Kamu adalah asisten keuangan pribadi yang sederhana.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
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
            CURLOPT_TIMEOUT => 30,
        ]);

        $hasil = curl_exec($ch);
        $error_curl = curl_error($ch);
        curl_close($ch);

        if ($error_curl) {
            $respon_ai = 'Gagal terhubung ke AI: ' . $error_curl;
        } else {
            $json = json_decode($hasil, true);
            $respon_ai = $json['choices'][0]['message']['content'] ?? 'Tidak ada respon dari AI.';
        }
    } else {
        $respon_ai = 'Silakan isi API Key DeepSeek terlebih dahulu di file ai_assistant.php.';
    }
}

$judul_halaman='AI Assistant';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="card card-modern p-3">
  <h6>Asisten Keuangan AI</h6>
  <div class="chat-box mb-3">
    <?php if ($pertanyaan_user): ?><div class="bubble-user"><strong>Kamu:</strong> <?= h($pertanyaan_user) ?></div><?php endif; ?>
    <?php if ($respon_ai): ?><div class="bubble-ai"><strong>AI:</strong><br><?= nl2br(h($respon_ai)) ?></div><?php endif; ?>
  </div>
  <form method="post" class="d-flex gap-2">
    <input type="text" class="form-control" name="pertanyaan" placeholder="Tanya saran keuangan..." required>
    <button class="btn btn-utama">Kirim</button>
  </form>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

