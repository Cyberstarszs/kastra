-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 09, 2026 at 07:28 PM
-- Server version: 10.11.18-MariaDB
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kastrawe_kastra`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_ai`
--

CREATE TABLE `chat_ai` (
  `id_chat` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `pesan_user` text NOT NULL,
  `respon_ai` text NOT NULL,
  `waktu` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_ai`
--

INSERT INTO `chat_ai` (`id_chat`, `id_user`, `pesan_user`, `respon_ai`, `waktu`) VALUES
(1, 2, 'Pengeluaran saya kebanyakan di mana?', 'Dari pola terbaru, pengeluaran terbesar ada di kategori Makanan dan Belanja. Coba tetapkan batas mingguan untuk dua kategori itu.', '2026-05-01 09:00:00'),
(2, 2, 'Berapa ideal tabungan tiap bulan?', 'Target aman adalah 20% dari pemasukan. Jika terasa berat, mulai dari 10% lalu naik bertahap.', '2026-05-01 09:15:00'),
(3, 2, 'Apakah kondisi keuangan saya sehat?', 'Arus kas kamu masih positif. Fokus berikutnya: tekan pengeluaran variabel agar rasio tabungan meningkat.', '2026-05-01 09:30:00'),
(4, 2, 'haha', 'Silakan isi API Key DeepSeek di file user/ai_assistant.php agar AI Assistant aktif.', '2026-05-04 09:05:50'),
(5, 1, 'hai', 'Silakan isi API Key DeepSeek di file user/ai_assistant.php agar AI Assistant aktif.', '2026-05-04 09:54:00'),
(6, 1, 'auhuguh', 'Silakan isi API Key DeepSeek di file user/ai_assistant.php agar AI Assistant aktif.', '2026-05-04 10:08:31'),
(7, 1, 'aja', 'Silakan isi API Key DeepSeek di file user/ai_assistant.php agar AI Assistant aktif.', '2026-05-04 10:09:31'),
(24, 6, 'Kakkakakka', 'Halo! Sepertinya kamu belum mencatat pemasukan dan pengeluaran sama sekali.\n\nSaran saya:\n- Mulai catat semua uang yang masuk dan keluar, sekecil apapun.\n- Gunakan aplikasi catatan keuangan atau buku kecil.\n- Setelah punya data, baru bisa lihat pola pengeluaran dan mulai atur anggaran.\n\nKalau ada pertanyaan lain, sampaikan ya. Saya siap bantu.', '2026-05-04 13:57:48'),
(25, 6, 'Jannajjanakakkqkqkkqkq', 'Maaf, saya tidak bisa memahami pertanyaan Anda \"Jannajjanakakkqkqkkqkq\". \n\nMungkin ada kesalahan ketik. Silakan ulangi pertanyaan dengan jelas agar saya bisa membantu memberikan saran keuangan yang sesuai dengan data Anda.', '2026-05-04 13:57:52'),
(26, 6, 'Nannanjanja', 'Maaf, saya tidak mengerti maksud dari \"Nannanjanja\". Mungkin itu salah ketik?\n\nBerdasarkan data keuangan Anda saat ini, belum ada pemasukan dan pengeluaran. Saran saya:\n\n- Mulailah mencatat pemasukan pertama Anda, baik dari gaji, usaha, atau sumber lain.\n- Buat anggaran sederhana untuk kebutuhan pokok, tabungan, dan keinginan.\n- Jika belum ada pemasukan, fokus dulu mencari sumber pendapatan.\n\nKalau ada pertanyaan lain, tulis ulang dengan jelas ya. Saya siap bantu.', '2026-05-04 13:57:56'),
(27, 6, 'Nanannana', 'Halo! Karena data keuanganmu masih kosong, saran terbaik saya adalah mulai mencatat pemasukan dan pengeluaran harian.\n\n- Catat semua uang masuk, sekecil apa pun.\n- Catat semua pengeluaran, termasuk jajan atau transportasi.\n- Gunakan aplikasi catatan keuangan atau buku kecil.\n\nSetelah satu bulan, kamu bisa lihat pola keuanganmu dan mulai buat anggaran sederhana.\n\nAda yang bisa saya bantu lebih lanjut?', '2026-05-04 13:57:59'),
(28, 6, 'Nannajjaa', 'Maaf, saya tidak mengerti maksud dari \"Nannajjaa\". Jika itu adalah pertanyaan atau istilah tertentu, mohon jelaskan lebih lanjut.\n\nSementara itu, karena data keuangan Anda masih kosong, saran saya:\n- Mulai catat pemasukan dan pengeluaran harian.\n- Tentukan target tabungan minimal 10% dari pemasukan.\n- Buat anggaran sederhana untuk kebutuhan pokok, hiburan, dan darurat.\n\nJika ada pertanyaan lain, silakan sampaikan dengan jelas.', '2026-05-04 13:58:02'),
(29, 6, 'A bahuauaq', 'Maaf, saya tidak mengerti maksud dari \"A bahuauaq\". Mungkin ada kesalahan ketik.\n\nKarena data keuangan Anda masih kosong, saran saya:\n- Catat dulu pemasukan dan pengeluaran harian, sekecil apapun\n- Mulai buat anggaran sederhana bulan depan\n- Tentukan target menabung, misal 10% dari pemasukan\n\nBisa ulangi pertanyaan dengan lebih jelas?', '2026-05-04 13:58:05'),
(30, 6, 'A vabhahha', 'Maaf, saya tidak mengerti pertanyaan \"A vabhahha\". Bisakah Anda menulis ulang dengan jelas?\n\nSementara itu, karena data keuangan Anda masih kosong, saran saya: mulailah mencatat setiap pemasukan dan pengeluaran harian. Gunakan aplikasi catatan atau buku kecil. Ini langkah pertama penting untuk mengelola keuangan.', '2026-05-04 13:58:08'),
(31, 6, 'AbanjajaOalah *teks banyak dan acak* 😵‍💫  Maksudnya dapet data random kayak kode `0069ab97-3e54-41ef-87ea-807621d1922c` tadi banyak banget ya?  *Kalo mau beresin teks acak, ada 3 cara:* Kondisimu	Solusinya **Kode/redeem acak numpuk**	Pake Excel/Google Sheet → Data → Remove duplicates. Langsung bersih **Chat/log kepanjangan**	Paste ke sini aja. Bilang \"tolong ringkasin\" atau \"ambil poin penting\" **Cari pola di teks random**	Kasih contoh 3-5 baris. Nanti gw bantu regex/filter-nya *Tools cepet buat teks acak:* 1. *Notepad++* → Alt+C buat column mode, Ctrl+F buat find 2. *ChatGPT/Meta AI* → \"ha', 'Maaf, saya tidak bisa membantu mengolah teks acak seperti kode atau log yang panjang.\n\nTapi soal keuangan Anda, begini saran saya:\n\nPemasukan dan pengeluaran Anda masih Rp 0. Artinya data belum diisi.\n\nLangkah pertama:\n- Catat semua pemasukan dalam sebulan, berapa pun jumlahnya.\n- Catat semua pengeluaran, sekecil apa pun.\n\nSetelah itu baru bisa lihat kategori terbesar dan cara mengelolanya.\n\nKalau Anda punya angka atau transaksi nyata, tulis di sini. Saya bantu analisis.', '2026-05-04 13:58:14'),
(32, 6, 'Oalah *teks banyak dan acak* 😵‍💫  Maksudnya dapet data random kayak kode `0069ab97-3e54-41ef-87ea-807621d1922c` tadi banyak banget ya?  *Kalo mau beresin teks acak, ada 3 cara:* Kondisimu	Solusinya **Kode/redeem acak numpuk**	Pake Excel/Google Sheet → Data → Remove duplicates. Langsung bersih **Chat/log kepanjangan**	Paste ke sini aja. Bilang \"tolong ringkasin\" atau \"ambil poin penting\" **Cari pola di teks random**	Kasih contoh 3-5 baris. Nanti gw bantu regex/filter-nya *Tools cepet buat teks acak:* 1. *Notepad++* → Alt+C buat column mode, Ctrl+F buat find 2. *ChatGPT/Meta AI* → \"hapus dupli', 'Teks kamu tidak mengandung data keuangan yang bisa diolah karena pemasukan dan pengeluaran masih Rp 0. Jadi saran saya:\n\n- Mulai catat pemasukan pertama, berapa pun nominalnya.\n- Catat juga pengeluaran kecil, misal beli kopi atau ongkos.\n- Setelah ada angka riil, baru bisa lihat kategori terbesar dan saran penghematan.\n\nUntuk masalah teks acak seperti 0069ab97..., itu bukan urusan keuangan. Tapi kalau kamu ingin data keuanganmu rapi, saran saya:\n\n- Gunakan aplikasi catatan keuangan sederhana seperti Money Lover atau Catatan Keuangan Harian.\n- Jangan simpan data keuangan di file teks acak, karena sulit dibaca.\n- Kalau terlanjur banyak teks acak, copy paste 3-5 baris contoh ke sini, saya bantu filter angkanya.\n\nSiap bantu jika ada data keuangan riil.', '2026-05-04 13:58:20'),
(33, 6, 'Oalah *teks banyak dan acak* 😵‍💫  Maksudnya dapet data random kayak kode `0069ab97-3e54-41ef-87ea-807621d1922c` tadi banyak banget ya?  *Kalo mau beresin teks acak, ada 3 cara:* Kondisimu	Solusinya **Kode/redeem acak numpuk**	Pake Excel/Google Sheet → Data → Remove duplicates. Langsung bersih **Chat/log kepanjangan**	Paste ke sini aja. Bilang \"tolong ringkasin\" atau \"ambil poin penting\" **Cari pola di teks random**	Kasih contoh 3-5 baris. Nanti gw bantu regex/filter-nya *Tools cepet buat teks acak:* 1. *Notepad++* → Alt+C buat column mode, Ctrl+F buat find 2. *ChatGPT/Meta AI* → \"hapus dupli', 'Data keuanganmu masih kosong. Artinya, belum ada pemasukan atau pengeluaran tercatat.\n\nSaran sederhana:\n- Mulai catat semua uang masuk, sekecil apapun.\n- Catat juga semua pengeluaran harian, misal beli kopi atau makan siang.\n- Setelah seminggu, lihat pos terbesar. Itu yang bisa kamu evaluasi.\n\nUntuk teks acak seperti kode yang kamu sebut, itu bukan data keuangan. Jadi tidak perlu diproses di sini.\n\nKalau mau mulai, bilang saja \"catat pemasukan 50 ribu\" atau \"catat beli nasi goreng 15 ribu\". Nanti saya bantu rekap.', '2026-05-04 13:58:25'),
(34, 6, 'Oalah *teks banyak dan acak* 😵‍💫  Maksudnya dapet data random kayak kode `0069ab97-3e54-41ef-87ea-807621d1922c` tadi banyak banget ya?  *Kalo mau beresin teks acak, ada 3 cara:* Kondisimu	Solusinya **Kode/redeem acak numpuk**	Pake Excel/Google Sheet → Data → Remove duplicates. Langsung bersih **Chat/log kepanjangan**	Paste ke sini aja. Bilang \"tolong ringkasin\" atau \"ambil poin penting\" **Cari pola di teks random**	Kasih contoh 3-5 baris. Nanti gw bantu regex/filter-nya *Tools cepet buat teks acak:* 1. *Notepad++* → Alt+C buat column mode, Ctrl+F buat find 2. *ChatGPT/Meta AI* → \"hapus dupli', 'Data pemasukan dan pengeluaran Anda masih nol, artinya belum ada catatan keuangan sama sekali. Ini adalah titik awal yang bagus.\n\nSaran saya:\n\n- Mulai catat pemasukan pertama. Misalnya gaji, uang jajan, atau penghasilan harian.\n\n- Catat pengeluaran kecil sekalipun. Mulai dari Rp 1.000 untuk jajan. Ini membantu tahu ke mana uang pergi.\n\n- Buat kategori sederhana: Makanan, Transportasi, Hiburan, Tabungan.\n\n- Target sederhana: sisihkan minimal 10% dari pemasukan pertama untuk tabungan.\n\nSoal teks acak seperti kode itu, tidak perlu dipusingkan. Fokus dulu pada catatan keuangan nyata Anda.', '2026-05-04 13:58:30'),
(35, 6, 'Oalah *teks banyak dan acak* 😵‍💫  Maksudnya dapet data random kayak kode `0069ab97-3e54-41ef-87ea-807621d1922c` tadi banyak banget ya?  *Kalo mau beresin teks acak, ada 3 cara:* Kondisimu	Solusinya **Kode/redeem acak numpuk**	Pake Excel/Google Sheet → Data → Remove duplicates. Langsung bersih **Chat/log kepanjangan**	Paste ke sini aja. Bilang \"tolong ringkasin\" atau \"ambil poin penting\" **Cari pola di teks random**	Kasih contoh 3-5 baris. Nanti gw bantu regex/filter-nya *Tools cepet buat teks acak:* 1. *Notepad++* → Alt+C buat column mode, Ctrl+F buat find 2. *ChatGPT/Meta AI* → \"hapus dupli', 'Keuanganmu masih nol semua, artinya belum ada catatan pemasukan atau pengeluaran.\n\nSaran sederhana:\n- Mulai catat pemasukan pertama, berapa pun jumlahnya.\n- Catat juga pengeluaran harian, sekecil apa pun.\n- Setelah ada data, baru bisa lihat kategori terbesar dan evaluasi.\n\nUntuk teks acak seperti itu, saran saya:\n- Kalau mau bersihin duplikat, pakai Excel atau Google Sheet, fitur Remove Duplicates.\n- Kalau chat atau log panjang, tempel ke sini dan bilang \"ringkas\" atau \"ambil poin penting\".\n- Kalau cari pola, kirim 3-5 baris contoh, nanti saya bantu filter.\n\nMulai dari catat keuangan dulu ya.', '2026-05-04 13:58:36'),
(36, 6, 'Vaba', 'Maaf, saya tidak mengerti pertanyaan \"Vaba\". Mungkin ada kesalahan ketik.\n\nKarena data keuangan Anda masih kosong, saran saya:\n- Catat semua pemasukan dan pengeluaran harian mulai sekarang.\n- Tentukan target menabung minimal 10% dari pemasukan.\n- Buat anggaran sederhana untuk kebutuhan pokok, transportasi, dan hiburan.\n\nMulai dari hal kecil. Saya siap bantu jika ada data atau pertanyaan lain.', '2026-05-04 13:58:42'),
(37, 6, 'Babha', 'Halo! Sepertinya belum ada data pemasukan dan pengeluaran yang tercatat. Untuk memulai, coba catat dulu semua uang yang kamu terima dan keluarkan setiap hari.\n\nSaran sederhana:\n- Mulai catat pemasukan, misalnya uang saku atau gaji.\n- Catat juga pengeluaran kecil seperti jajan atau transportasi.\n- Setelah seminggu, lihat ke mana uangmu paling banyak pergi.\n\nKalau ada pertanyaan lebih lanjut, tanyakan saja ya.', '2026-05-04 13:58:45'),
(38, 6, 'Babba', 'Maaf, saya tidak mengerti maksud \"Babba\". Mungkin itu salah ketik atau pertanyaan yang tidak lengkap.\n\nBerdasarkan data keuangan Anda saat ini:\n- Pemasukan dan pengeluaran masih Rp 0.\n- Belum ada kategori belanja.\n\nSaran saya:\n- Mulai catat pemasukan pertama, misalnya dari gaji, usaha, atau kiriman.\n- Catat juga pengeluaran rutin seperti makan, transportasi, atau kebutuhan harian.\n- Setelah itu, analisis mana kategori terbesar agar bisa diatur anggarannya.\n\nJika ada pertanyaan lain, silakan tulis ulang dengan jelas.', '2026-05-04 13:58:49'),
(46, 12, 'setelah menikah saya mau umroh bersama istri saya', 'Baik, mari kita bahas rencana umroh Anda bersama istri.\n\nKarena saat ini pemasukan dan pengeluaran Anda masih Rp 0, langkah pertama adalah memastikan Anda memiliki sumber penghasilan tetap.\n\nSaran saya:\n\n- Hitung dulu pendapatan bulanan Anda dan istri setelah menikah.\n- Perkirakan biaya umroh berdua saat ini sekitar Rp 30-50 juta (tergantung paket).\n- Targetkan menabung secara rutin setiap bulan. Misal, jika target Rp 40 juta dalam 2 tahun, Anda perlu menabung sekitar Rp 1,7 juta per bulan.\n- Buat rekening tabungan khusus umroh agar tidak tercampur dengan kebutuhan lain.\n- Kurangi pengeluaran tidak penting seperti nongkrong atau belanja impulsif.\n- Cari tambahan penghasilan jika perlu, misal usaha kecil atau kerja freelance.\n\nSetelah menikah, komunikasikan rencana ini dengan istri agar sama-sama berkomitmen menabung.\n\nSemoga terkabul keinginan umrohnya.', '2026-05-09 11:08:29'),
(47, 12, 'penghasilan 3-5 juta perbulan kalo istri 2-4 juta perbulan', 'Baik, berdasarkan info penghasilan gabungan kamu dan istri, berikut saran keuangan sederhana:\n\n- Total penghasilan rumah tangga: Rp 5 - 9 juta per bulan.\n- Prioritas utama: sisihkan minimal 10% untuk tabungan atau dana darurat.\n- Keluar lebih dulu kebutuhan pokok: makan, listrik, air, transportasi.\n- Alokasikan maksimal 30% dari penghasilan untuk cicilan atau sewa rumah.\n- Sisanya untuk kebutuhan lain dan hiburan, jangan lebih dari 30%.\n- Catat pengeluaran harian agar tahu kemana uang pergi.\n- Mulai buat pos dana darurat minimal 3 bulan pengeluaran.\n\nSaran tambahan: jika ada sisa, coba investasi kecil di reksa dana atau emas. Jangan lupa diskusi dengan istri soal prioritas keuangan bersama.', '2026-05-09 11:09:27'),
(48, 12, 'pengeluaran setiap bulan cicilan rumah 800ribu cicilan spinjam 1juta spaylater 500 cicilan bank 1,3juta', 'Baik, saya bantu analisis dari data yang kamu berikan.\n\nDari total cicilan yang kamu sebutkan:\n- Cicilan rumah: Rp 800.000\n- Spinjam: Rp 1.000.000\n- Spaylater: Rp 500.000\n- Cicilan bank: Rp 1.300.000\n\nTotal cicilan per bulan: Rp 3.600.000\n\nSayangnya data pemasukan kamu masih Rp 0. Saran saya:\n\n- Segera catat pemasukan tetap bulananmu. Tanpa data pemasukan, sulit menilai apakah cicilan ini sehat atau tidak.\n- Idealnya total cicilan tidak lebih dari 30-40% dari pemasukan bulanan. Misal pemasukan Rp 10 juta, cicilan maksimal Rp 3-4 juta.\n- Prioritaskan lunasi cicilan dengan bunga tertinggi lebih dulu, biasanya Spinjam dan Spaylater.\n- Jika pemasukan terbatas, coba negosiasi perpanjangan tenor atau restrukturisasi ke bank agar cicilan lebih ringan.\n- Hindari tambah utang baru sampai cicilan saat ini terkendali.\n\nBeri tahu saya berapa pemasukan bulananmu agar saran bisa lebih tepat.', '2026-05-09 11:11:31'),
(49, 13, 'Saya mau ke jakarta Utara selatan berapa perhari yang harus saya tabung', 'Halo! Karena data keuanganmu masih nol, kita mulai dari awal ya.\n\nUntuk pergi ke Jakarta Utara atau Selatan, biaya harian tergantung moda transportasi dan kebutuhanmu. Sebagai gambaran:\n\n- Transportasi umum (TransJakarta, MRT, KRL) sekitar Rp 10.000 - Rp 20.000 per hari.\n- Makan 3 kali sehari sederhana sekitar Rp 30.000 - Rp 50.000.\n- Total perkiraan kebutuhan harian sekitar Rp 40.000 - Rp 70.000.\n\nSaran saya:\n\n- Mulai tabung Rp 10.000 per hari dulu.\n- Jika ada pemasukan, naikkan jadi Rp 20.000 - Rp 30.000 per hari.\n- Catat pengeluaran harianmu agar tahu kebutuhan sebenarnya.\n\nSemangat menabung, ya!', '2026-05-09 11:12:17'),
(50, 12, 'totalnya 5-9 juta', 'Baik, saya paham situasinya.\n\nKarena data pemasukan dan pengeluaran Anda masih Rp 0, saran saya fokus ke perencanaan awal dulu.\n\nBerikut saran sederhana untuk Anda:\n\n- Catat dulu semua pemasukan tetap bulanan, misalnya gaji atau penghasilan lain.\n- Catat semua pengeluaran rutin, seperti makan, transport, listrik, dan cicilan.\n- Gunakan aturan 50/30/20: 50% untuk kebutuhan, 30% untuk keinginan, 20% untuk tabungan atau investasi.\n- Jika total pemasukan Anda antara 5-9 juta, prioritaskan menabung minimal 10-20% dari pemasukan setiap bulan.\n- Siapkan dana darurat minimal 3-6 kali pengeluaran bulanan.\n- Hindari utang konsumtif seperti belanja online berlebihan atau pinjaman dengan bunga tinggi.\n\nMulai dengan mencatat selama satu bulan, lalu evaluasi. Semoga membantu.', '2026-05-09 11:12:51'),
(51, 13, 'Kamu salah', 'Maaf, saya baru mengecek data Anda. Total pemasukan dan pengeluaran Rp 0 memang belum bisa dianalisis.\n\nSaran saya:\n- Catat dulu semua pemasukan dan pengeluaran harian selama sebulan.\n- Gunakan aplikasi catatan keuangan atau buku kecil.\n- Setelah itu, baru kita lihat kategori pengeluaran terbesar.\n- Mulai dari hal kecil dulu, seperti jajan atau transportasi.\n- Baru bisa saya bantu saran lebih spesifik.\n\nSilakan isi data keuangan Anda, ya.', '2026-05-09 11:12:57'),
(52, 12, 'gimana caranya biar cepat banyak tabungan', 'Tidak masalah dengan angka nol. Ini langkah awal yang baik.\n\nKunci menabung cepat ada di dua hal: pemasukan dan pengeluaran.\n\nKarena pemasukan masih nol, fokus utama sekarang adalah menambah pemasukan dulu.\n\nSaran untuk Anda:\n\n- Cari sumber pemasukan tetap atau sambilan. Pekerjaan paruh waktu, freelance, atau jual barang yang tidak terpakai bisa jadi awal.\n- Jika sudah ada pemasukan, langsung sisihkan minimal 10-20 persen di awal bulan sebelum dipakai untuk hal lain. Ini metode \"bayar diri sendiri dulu\".\n- Catat setiap pengeluaran sekecil apapun. Ini akan membantu Anda tahu ke mana uang pergi.\n- Kurangi pengeluaran yang tidak perlu, seperti jajan, langganan layanan yang jarang dipakai, atau transportasi yang bisa diganti jalan kaki atau naik kendaraan umum.\n\nMulai dari satu langkah kecil dulu. Yang penting konsisten.', '2026-05-09 11:13:19'),
(53, 12, 'jika ada saran buat saya infestasi', 'Saran untuk Anda saat ini:\n\n- Mulai dari menabung dulu sebelum investasi. Karena pemasukan masih Rp 0, fokus utama adalah mencari penghasilan tetap.\n\n- Jika sudah punya pemasukan, sisihkan minimal 10% dari gaji untuk tabungan darurat. Tabungan ini penting sebagai bantalan sebelum mulai investasi.\n\n- Setelah tabungan darurat terkumpul (idealnya 3-6 bulan biaya hidup), baru pertimbangkan investasi. Pilih instrumen risiko rendah seperti reksa dana pasar uang atau deposito.\n\n- Pelajari dasar investasi dari sumber terpercaya agar tidak mudah tergoda tawaran cepat kaya.\n\n- Jangan meminjam uang untuk investasi. Mulailah dari nominal kecil yang Anda benar-benar miliki.\n\nSemangat membangun pondasi keuangan dari awal.', '2026-05-09 11:13:59'),
(54, 12, 'masih bingung', 'Tidak apa-apa bingung, itu langkah awal yang baik.\n\nMulai saja dengan mencatat semua pemasukan dan pengeluaran harian selama satu minggu. Pakai buku catatan, notes di ponsel, atau aplikasi gratis.\n\nSetelah seminggu, lihat pola keuanganmu. Dari situ kamu bisa tahu berapa pemasukan riil dan ke mana uang paling banyak pergi.\n\nKalau belum ada pemasukan, fokus dulu ke sumber pendapatan. Misal kerja sampingan, jual barang tak terpakai, atau freelance kecil.\n\nSatu langkah kecil setiap hari lebih baik daripada menunggu semuanya rapi dulu.', '2026-05-09 11:14:56'),
(55, 12, 'apakah mungkin', 'Tentu saja mungkin. Karena pemasukan dan pengeluaran masih Rp 0, artinya Anda belum mencatat atau belum mulai.\n\nSaran saya:\n- Mulai catat pemasukan pertama, sekecil apapun.\n- Catat pengeluaran harian, misal untuk makan atau transportasi.\n- Setelah ada data, baru bisa lihat kategori terbesar dan buat anggaran.\n\nLangkah pertama adalah mulai mencatat.', '2026-05-09 11:15:19'),
(56, 12, 'mau mulai dari mana', 'Baik, kita mulai dari awal yang paling dasar.\n\nPertama, catat semua pemasukan yang kamu punya sekarang, meskipun Rp 0. Pastikan dulu apakah ada uang tunai, saldo e-wallet, atau tabungan yang belum dihitung.\n\nKedua, catat semua pengeluaran harian selama seminggu ke depan. Tulis berapa pun yang keluar, sekecil apapun. Ini untuk tahu ke mana uangmu pergi.\n\nKetiga, tentukan satu sumber pemasukan paling realistis yang bisa segera kamu dapatkan, misalnya kerja lepas, jual barang tak terpakai, atau bantuan keluarga. Mulai dari situ.\n\nKeempat, buat target kecil: dalam sebulan, cari pemasukan minimal Rp 100.000 dan batasi pengeluaran hanya untuk kebutuhan pokok.\n\nKuncinya: catat dulu, baru atur. Jangan khawatir, semua orang mulai dari nol.', '2026-05-09 11:15:36'),
(57, 12, 'jika mau mulai dengan apa', 'Mulai dengan langkah kecil ini:\n\n- Catat pemasukan dan pengeluaran harian. Gunakan aplikasi atau buku catatan.  \n- Tentukan sumber pemasukan pertama, misal: kerja freelance, jual barang bekas, atau usaha kecil.  \n- Buat target pengeluaran minimal. Fokus pada kebutuhan pokok dulu.  \n- Kalau belum ada pemasukan, kurangi pengeluaran yang tidak penting.  \n\nMulai dari mencatat. Itu langkah paling awal dan paling penting.', '2026-05-09 11:16:01'),
(58, 12, 'jika liburan di bulan depan apakah bisa', 'Dari data Anda, saat ini belum ada pemasukan dan pengeluaran tercatat. Artinya, belum ada gambaran keuangan yang bisa dijadikan acuan untuk merencanakan liburan bulan depan.\n\nSaran saya:\n\n- Mulai catat pemasukan dan pengeluaran harian Anda sekarang.\n- Tentukan target dana liburan yang realistis.\n- Jika bulan depan belum ada pemasukan, sebaiknya tunda dulu liburan sampai ada dana cukup.\n- Liburan bisa tetap direncanakan, tapi pastikan tidak menggunakan uang kebutuhan pokok atau utang.\n\nFokus dulu membangun pemasukan, baru atur pengeluaran untuk liburan.', '2026-05-09 11:16:21'),
(59, 12, 'saya pengen liburan', 'Tidak ada pemasukan dan pengeluaran saat ini, artinya kamu belum punya aliran uang untuk direncanakan. Untuk bisa liburan, langkah pertama adalah cari pemasukan dulu.\n\nSaran sederhana:\n- Mulai kerja atau cari penghasilan tetap dulu.\n- Sisihkan minimal 10% dari penghasilan nanti untuk dana liburan.\n- Tentukan tujuan liburan dan perkirakan biayanya.\n- Setelah ada pemasukan, buat anggaran pengeluaran agar tidak boros.\n\nKalau sudah ada pemasukan, kamu bisa mulai menabung secara rutin untuk liburan.', '2026-05-09 11:16:41'),
(60, 12, 'berapa setiap bulannya', 'Maaf, dari data yang kamu berikan, semua pemasukan dan pengeluaran masih Rp 0. Jadi belum bisa dihitung berapa setiap bulannya.\n\nSaran saya:\n- Mulai catat semua uang masuk dan keluar, sekecil apapun.\n- Tentukan target pemasukan minimal per bulan, misalnya dari kerja atau usaha.\n- Buat anggaran sederhana untuk kebutuhan pokok, tabungan, dan hiburan.\n- Jika belum ada pemasukan, fokus dulu mencari sumber penghasilan.\n\nKalau nanti ada datanya, saya bantu hitung lagi.', '2026-05-09 11:17:40'),
(61, 12, 'gimana cara menabungnya', 'Karena saat ini pemasukan dan pengeluaran Anda masih Rp 0, langkah pertama adalah mulai mencatat setiap pemasukan dan pengeluaran harian.\n\nSaran saya:\n\n- Mulai cari sumber pemasukan, misal kerja sampingan, jual barang tak terpakai, atau freelance.\n- Jika sudah ada pemasukan, sisihkan minimal 10% langsung saat menerima uang, sebelum dipakai untuk kebutuhan lain.\n- Buat pos pengeluaran tetap seperti makan, transportasi, dan hiburan. Jangan sampai pengeluaran melebihi pemasukan.\n- Catat semua pengeluaran kecil agar tahu ke mana uang pergi.\n- Target awal: kumpulkan dana darurat setara 3-6 bulan pengeluaran pokok.\n\nMulai saja dari langkah pertama, yaitu mencatat dan mencari pemasukan.', '2026-05-09 11:18:00'),
(62, 11, 'halo', 'Halo! Senang berkenalan denganmu.\n\nKarena saat ini pengeluaranmu masih Rp 0 dan pemasukanmu sangat besar, saran saya sederhana:\n\n- Segera buat rencana pengeluaran bulanan agar uangmu bisa dikelola dengan baik.\n- Alokasikan untuk kebutuhan pokok, tabungan, investasi, dan dana darurat.\n- Mulai catat kategori pengeluaran agar kamu bisa melihat pola keuanganmu ke depan.\n\nJika ada yang ingin ditanyakan lebih lanjut, bilang saja ya.', '2026-05-09 20:11:28'),
(63, 11, 'bagaimana kondisi keuangan saya', 'Kondisi keuangan Anda sangat sehat. Pemasukan jauh lebih besar dibanding pengeluaran, hanya 0,01% dari total pemasukan yang terpakai. Artinya, Anda punya kapasitas menabung dan investasi yang sangat besar.\n\nSaran saya:\n- Alokasikan minimal 30% pemasukan untuk investasi jangka panjang seperti saham, reksa dana, atau properti.\n- Sisihkan dana darurat 3-6 kali pengeluaran bulanan, meskipun pengeluaran Anda kecil.\n- Buat anggaran tetap untuk kategori makan minum agar tidak membengkak, meski sekarang masih kecil.\n- Pertimbangkan diversifikasi aset untuk melindungi kekayaan dari inflasi.', '2026-05-09 20:13:10'),
(68, 14, 'halo', 'Halo!\n\nPengeluaranmu Rp 200.000 dari pemasukan Rp 4.000.000 itu sangat kecil. Artinya, kamu punya banyak sisa uang.\n\nSaran saya:\n- Sisihkan minimal 20% dari sisa uang untuk tabungan atau dana darurat.\n- Kategori makan adalah yang terbesar. Coba catat detail pengeluaran makan, apakah ada yang bisa dihemat atau tidak.\n- Nikmati sebagian sisanya untuk hiburan atau kebutuhan lain, agar keuangan tetap seimbang.\n\nSemangat mengatur keuangan ya!', '2026-05-29 06:50:09'),
(69, 14, 'saya mau liburan ke bali, kira kira dalam 1 bulan saya harus kumpulin uang berapa kalo liburna ke bali dari madura ke bali selama 7 hari', 'Dari data keuanganmu, sisa uang per bulan adalah Rp 3.800.000. Ini cukup besar, jadi kamu punya potensi menabung dengan baik.\n\nUntuk liburan dari Madura ke Bali selama 7 hari, perkiraan biaya rata-rata:\n\n- Transportasi (ferry + bus/pesawat PP): Rp 400.000 - Rp 800.000\n- Akomodasi 7 malam (penginapan hemat): Rp 700.000 - Rp 1.400.000\n- Makan 7 hari: Rp 350.000 - Rp 700.000\n- Transportasi lokal & aktivitas: Rp 300.000 - Rp 600.000\n- Dana darurat & oleh-oleh: Rp 200.000 - Rp 400.000\n\nTotal estimasi: sekitar Rp 2.000.000 - Rp 3.900.000.\n\nSaran saya:\n\n- Target menabung minimal Rp 2.500.000 untuk liburan cukup nyaman.\n- Dalam 1 bulan, kamu bisa sisihkan sekitar Rp 2.500.000 dari pemasukanmu.\n- Sisa Rp 1.300.000 bisa untuk kebutuhan lain dan tabungan darurat.\n- Jika ingin lebih hemat, kurangi pengeluaran makan atau cari penginapan sangat murah.\n\nDengan disiplin, kamu bisa berangkat bulan depan.', '2026-05-29 06:50:56'),
(70, 14, 'halo', 'Halo! Keuangan kamu terlihat sehat dengan pemasukan Rp4.000.000 dan pengeluaran hanya Rp200.000. Ini bagus.\n\nKategori terbesar adalah makan. Coba evaluasi apakah pengeluaran makan Rp200.000 itu sudah termasuk semua kebutuhan atau hanya sebagian. Jika masih ada sisa, kamu bisa alokasikan untuk tabungan atau investasi.\n\nSaran saya:\n- Sisihkan minimal 20% dari pemasukan untuk tabungan atau dana darurat.\n- Pantau pengeluaran makan agar tidak membengkak.\n- Nikmati hasil kerja kerasmu, tapi tetap bijak dalam belanja.\n\nSemangat terus!', '2026-05-29 07:08:25'),
(84, 15, 'Saya mau liburan ke bali selama 7 hari, kira kira kalo dari madura di posisi tabungan saya saat ini kurang berapa?', 'Halo! Saya bantu hitung ya.\n\nDari data kamu:\n- Pemasukan: Rp4.400.000\n- Pengeluaran: Rp200.000\n- Sisa tabungan per bulan: Rp4.200.000\n\nUntuk liburan 7 hari ke Bali dari Madura, perkiraan biaya:\n- Tiket PP (bus/kapal + pesawat): sekitar Rp1.000.000 - Rp1.500.000\n- Akomodasi 7 malam (penginapan budget): Rp700.000 - Rp1.400.000\n- Makan 7 hari: Rp700.000 - Rp1.050.000\n- Transportasi lokal + wisata: Rp500.000 - Rp1.000.000\n- Oleh-oleh & darurat: Rp300.000 - Rp500.000\n\nTotal perkiraan: Rp3.200.000 - Rp5.450.000\n\nDengan tabungan Rp4.200.000, kamu masih kurang sekitar Rp0 - Rp1.250.000 tergantung pilihan hemat atau nyaman.\n\nSaran saya:\n- Jika ingin berangkat bulan ini, kamu hampir cukup asal pilih akomodasi dan transportasi paling hemat.\n- Lebih aman: tabung dulu 1-2 bulan tambahan agar punya dana darurat.\n- Cari tiket promo atau travel partner biaya lebih murah.\n\nSemoga liburanmu menyenangkan dan tetap aman secara finansial.', '2026-06-04 13:21:56'),
(85, 15, 'Kalo saya mau ke padang?', 'Makan di padang bisa jadi pilihan yang oke. Dengan pemasukan Rp4.400.000 dan pengeluaran Rp200.000, keuanganmu masih sangat longgar.\n\nSaran saya:\n\n- Satu kali makan di restoran padang biasanya Rp20.000 - Rp40.000.\n- Kalau kamu makan di padang seminggu 2-3 kali, tambahan pengeluaran sekitar Rp200.000 - Rp300.000 per bulan.\n- Total pengeluaran jadi Rp400.000 - Rp500.000. Masih aman banget dari pemasukanmu.\n\nYang penting jangan setiap hari. Nikmati sesekali, dan tetap catat biayanya agar nggak kelebihan.', '2026-06-04 13:22:59'),
(86, 15, 'Kalo saya liburan ke padanv butuh bedapa ?', 'Dari data keuanganmu, pemasukan Rp4.400.000 dan pengeluaran Rp200.000, artinya kamu punya sisa Rp4.200.000 bulan ini.\n\nUntuk liburan ke Padang, perkiraan biaya tergantung durasi dan gaya:\n\n- Tiket pesawat PP sekitar Rp 800.000 - Rp 1.500.000.\n- Penginapan per malam Rp 150.000 - Rp 400.000.\n- Makan per hari Rp 50.000 - Rp 100.000.\n- Transportasi lokal dan oleh-oleh Rp 200.000 - Rp 500.000.\n\nContoh untuk 3 hari 2 malam hemat: sekitar Rp 1.500.000 - Rp 2.500.000.\n\nKarena pengeluaranmu kecil, kamu bisa sisihkan dana liburan dari sisa penghasilan. Pastikan tetap menyisakan dana darurat dan kebutuhan pokok. Selamat merencanakan liburan.', '2026-06-04 13:23:33'),
(91, 4, 'halo', 'Halo! Senang bertemu denganmu.\n\nKarena bulan ini kamu belum punya pengeluaran, ini waktu yang tepat untuk mulai merencanakan keuangan.\n\nSaran sederhana:\n- Sisihkan sebagian pemasukan untuk tabungan atau dana darurat. Misalnya Rp 5.000 dulu.\n- Rencanakan pengeluaran untuk kebutuhan sehari-hari agar tetap terkendali.\n- Catat setiap pemasukan dan pengeluaran supaya kamu tahu pola keuanganmu.\n\nKuncinya: atur pemasukanmu agar selalu ada yang ditabung sebelum dipakai untuk hal lain.\n\nAda yang ingin ditanyakan lagi?', '2026-07-09 19:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `id_user`, `nama_kategori`, `jenis`, `is_default`, `icon`) VALUES
(1, 2, 'Gaji', 'pemasukan', 0, NULL),
(2, 2, 'Freelance', 'pemasukan', 0, NULL),
(3, 2, 'Bonus', 'pemasukan', 0, NULL),
(4, 2, 'Investasi', 'pemasukan', 0, NULL),
(5, 2, 'Makanan', 'pengeluaran', 0, NULL),
(6, 2, 'Transportasi', 'pengeluaran', 0, NULL),
(7, 2, 'Belanja', 'pengeluaran', 0, NULL),
(8, 2, 'Tagihan', 'pengeluaran', 0, NULL),
(9, 2, 'Hiburan', 'pengeluaran', 0, NULL),
(10, 2, 'Kesehatan', 'pengeluaran', 0, NULL),
(11, 2, 'Pendidikan', 'pengeluaran', 0, NULL),
(12, 2, 'Rumah Tangga', 'pengeluaran', 0, NULL),
(13, 3, 'gaji', 'pemasukan', 0, NULL),
(14, 1, 'Gaji', 'pemasukan', 0, NULL),
(15, 11, 'makan minum', 'pengeluaran', 0, NULL),
(16, 11, 'gaji', 'pemasukan', 0, NULL),
(17, 11, 'investasi', 'pengeluaran', 0, NULL),
(18, 11, 'jakpot', 'pemasukan', 0, NULL),
(19, 14, 'Usaha', 'pemasukan', 0, NULL),
(20, 14, 'Gajian', 'pemasukan', 0, NULL),
(21, 14, 'Makan', 'pengeluaran', 0, NULL),
(22, 15, 'Usaha', 'pemasukan', 0, NULL),
(23, 15, 'Makan', 'pengeluaran', 0, NULL),
(24, 15, 'Gaji', 'pemasukan', 0, NULL),
(25, 15, 'Masuk', 'pengeluaran', 0, NULL),
(40, 16, 'Gaji', 'pemasukan', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id_reset` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expired_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id_reset`, `id_user`, `email`, `token_hash`, `expired_at`, `used_at`, `created_at`) VALUES
(1, 8, 'jashubpremium@gmail.com', '$2y$10$UvRFyGsf46TCuBL/krKZlu04vmwOpUYAeMVnmueHj/IQTXqF3wyj.', '2026-05-29 07:55:06', '2026-05-29 07:30:45', '2026-05-29 07:25:06'),
(2, 8, 'jashubpremium@gmail.com', '$2y$10$I35W5pwPHDkbBV1/m5yTiOnxSAwQMaaIWkf/86PcbuI7/8LKmMTW6', '2026-05-29 08:00:45', NULL, '2026-05-29 07:30:45');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_sistem`
--

CREATE TABLE `pengaturan_sistem` (
  `id_pengaturan` int(11) NOT NULL,
  `nama_pengaturan` varchar(100) NOT NULL,
  `nilai_pengaturan` text NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan_sistem`
--

INSERT INTO `pengaturan_sistem` (`id_pengaturan`, `nama_pengaturan`, `nilai_pengaturan`, `updated_at`) VALUES
(1, 'harga_premium', '3000', '2026-05-29 18:41:24'),
(2, 'limit_prompt', '15', '2026-05-04 10:08:35'),
(3, 'api_key_deepseek', 'ISI_API_KEY_DEEPSEEK_DI_SINI', '2026-05-04 10:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `tabungan`
--

CREATE TABLE `tabungan` (
  `id_tabungan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_tujuan` varchar(150) NOT NULL,
  `target_nominal` decimal(15,2) NOT NULL,
  `nominal_terkumpul` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tanggal_target` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tabungan`
--

INSERT INTO `tabungan` (`id_tabungan`, `id_user`, `nama_tujuan`, `target_nominal`, `nominal_terkumpul`, `tanggal_target`) VALUES
(1, 2, 'Dana Darurat', 20000000.00, 9200000.00, '2026-12-31'),
(2, 2, 'Beli Laptop Baru', 15000000.00, 8400000.00, '2026-10-15'),
(3, 2, 'Liburan Jepang', 30000000.00, 12350000.00, '2027-06-30'),
(4, 2, 'DP Motor', 12000000.00, 4500000.00, '2026-11-20'),
(5, 2, 'Kursus Sertifikasi', 8000000.00, 2300000.00, '2026-09-30'),
(6, 4, 'Beli Motor', 40000000.00, 0.00, '2026-05-04'),
(7, 16, 'Haji', 30000000.00, 5000000.00, '2026-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `jenis_transaksi` enum('pemasukan','pengeluaran') NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `jenis_transaksi`, `nominal`, `kategori`, `deskripsi`, `tanggal`) VALUES
(1, 1, 'pemasukan', 200000.00, 'gaji', 'wuhww', '2026-05-04'),
(2, 1, 'pengeluaran', 200000.00, 'gaji', '2222', '2026-05-04'),
(3, 2, 'pemasukan', 6141785.00, 'Gaji', 'Gaji bulanan', '2026-01-01'),
(4, 2, 'pengeluaran', 244168.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-01'),
(5, 2, 'pengeluaran', 405130.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-01'),
(6, 2, 'pengeluaran', 354697.00, 'Belanja', 'Pengeluaran belanja', '2026-01-02'),
(7, 2, 'pengeluaran', 211977.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-01-02'),
(8, 2, 'pengeluaran', 50798.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-02'),
(9, 2, 'pengeluaran', 140187.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-01-03'),
(10, 2, 'pengeluaran', 319245.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-03'),
(11, 2, 'pengeluaran', 125228.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-04'),
(12, 2, 'pengeluaran', 449453.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-01-05'),
(13, 2, 'pengeluaran', 137116.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-01-05'),
(14, 2, 'pengeluaran', 415041.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-06'),
(15, 2, 'pemasukan', 1045962.00, 'Freelance', 'Project freelance', '2026-01-07'),
(16, 2, 'pengeluaran', 207763.00, 'Belanja', 'Pengeluaran belanja', '2026-01-07'),
(17, 2, 'pengeluaran', 92898.00, 'Makanan', 'Pengeluaran makanan', '2026-01-07'),
(18, 2, 'pengeluaran', 413298.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-01-08'),
(19, 2, 'pengeluaran', 260366.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-09'),
(20, 2, 'pengeluaran', 77526.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-09'),
(21, 2, 'pengeluaran', 307122.00, 'Belanja', 'Pengeluaran belanja', '2026-01-09'),
(22, 2, 'pengeluaran', 326933.00, 'Makanan', 'Pengeluaran makanan', '2026-01-10'),
(23, 2, 'pengeluaran', 153761.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-10'),
(24, 2, 'pengeluaran', 197391.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-11'),
(25, 2, 'pengeluaran', 172434.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-11'),
(26, 2, 'pengeluaran', 399557.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-01-12'),
(27, 2, 'pengeluaran', 192303.00, 'Belanja', 'Pengeluaran belanja', '2026-01-13'),
(28, 2, 'pengeluaran', 301728.00, 'Belanja', 'Pengeluaran belanja', '2026-01-14'),
(29, 2, 'pengeluaran', 310981.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-14'),
(30, 2, 'pengeluaran', 33889.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-14'),
(31, 2, 'pemasukan', 6084295.00, 'Gaji', 'Gaji bulanan', '2026-01-15'),
(32, 2, 'pengeluaran', 205314.00, 'Transportasi', 'Pengeluaran transportasi', '2026-01-15'),
(33, 2, 'pengeluaran', 381530.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-15'),
(34, 2, 'pengeluaran', 193787.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-01-15'),
(35, 2, 'pengeluaran', 232319.00, 'Transportasi', 'Pengeluaran transportasi', '2026-01-16'),
(36, 2, 'pengeluaran', 242537.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-16'),
(37, 2, 'pengeluaran', 99024.00, 'Belanja', 'Pengeluaran belanja', '2026-01-17'),
(38, 2, 'pengeluaran', 353013.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-01-17'),
(39, 2, 'pengeluaran', 57256.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-17'),
(40, 2, 'pengeluaran', 233325.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-01-18'),
(41, 2, 'pengeluaran', 276330.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-19'),
(42, 2, 'pengeluaran', 44909.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-01-20'),
(43, 2, 'pengeluaran', 433840.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-20'),
(44, 2, 'pemasukan', 1314919.00, 'Freelance', 'Project freelance', '2026-01-21'),
(45, 2, 'pengeluaran', 269864.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-21'),
(46, 2, 'pengeluaran', 265951.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-21'),
(47, 2, 'pengeluaran', 85433.00, 'Makanan', 'Pengeluaran makanan', '2026-01-21'),
(48, 2, 'pengeluaran', 31018.00, 'Belanja', 'Pengeluaran belanja', '2026-01-22'),
(49, 2, 'pengeluaran', 329917.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-23'),
(50, 2, 'pengeluaran', 177426.00, 'Makanan', 'Pengeluaran makanan', '2026-01-23'),
(51, 2, 'pengeluaran', 218556.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-24'),
(52, 2, 'pengeluaran', 74514.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-25'),
(53, 2, 'pengeluaran', 51061.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-25'),
(54, 2, 'pengeluaran', 385431.00, 'Hiburan', 'Pengeluaran hiburan', '2026-01-26'),
(55, 2, 'pengeluaran', 380831.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-26'),
(56, 2, 'pengeluaran', 299622.00, 'Belanja', 'Pengeluaran belanja', '2026-01-27'),
(57, 2, 'pengeluaran', 284907.00, 'Tagihan', 'Pengeluaran tagihan', '2026-01-28'),
(58, 2, 'pengeluaran', 174895.00, 'Makanan', 'Pengeluaran makanan', '2026-01-29'),
(59, 2, 'pengeluaran', 383902.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-01-30'),
(60, 2, 'pengeluaran', 440726.00, 'Makanan', 'Pengeluaran makanan', '2026-01-30'),
(61, 2, 'pengeluaran', 56433.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-01-31'),
(62, 2, 'pemasukan', 7808387.00, 'Gaji', 'Gaji bulanan', '2026-02-01'),
(63, 2, 'pengeluaran', 343778.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-01'),
(64, 2, 'pengeluaran', 360185.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-02-02'),
(65, 2, 'pengeluaran', 182676.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-03'),
(66, 2, 'pengeluaran', 387417.00, 'Belanja', 'Pengeluaran belanja', '2026-02-03'),
(67, 2, 'pengeluaran', 187484.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-02-03'),
(68, 2, 'pengeluaran', 159005.00, 'Makanan', 'Pengeluaran makanan', '2026-02-04'),
(69, 2, 'pengeluaran', 272933.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-02-04'),
(70, 2, 'pengeluaran', 393144.00, 'Belanja', 'Pengeluaran belanja', '2026-02-05'),
(71, 2, 'pengeluaran', 337359.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-05'),
(72, 2, 'pengeluaran', 251122.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-02-06'),
(73, 2, 'pemasukan', 754618.00, 'Freelance', 'Project freelance', '2026-02-07'),
(74, 2, 'pengeluaran', 179534.00, 'Belanja', 'Pengeluaran belanja', '2026-02-07'),
(75, 2, 'pengeluaran', 38960.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-02-07'),
(76, 2, 'pengeluaran', 315382.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-08'),
(77, 2, 'pengeluaran', 391413.00, 'Makanan', 'Pengeluaran makanan', '2026-02-08'),
(78, 2, 'pengeluaran', 96140.00, 'Belanja', 'Pengeluaran belanja', '2026-02-08'),
(79, 2, 'pengeluaran', 300849.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-09'),
(80, 2, 'pengeluaran', 205884.00, 'Makanan', 'Pengeluaran makanan', '2026-02-09'),
(81, 2, 'pengeluaran', 357768.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-02-10'),
(82, 2, 'pengeluaran', 76729.00, 'Makanan', 'Pengeluaran makanan', '2026-02-10'),
(83, 2, 'pengeluaran', 256224.00, 'Transportasi', 'Pengeluaran transportasi', '2026-02-11'),
(84, 2, 'pengeluaran', 110063.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-12'),
(85, 2, 'pengeluaran', 191176.00, 'Transportasi', 'Pengeluaran transportasi', '2026-02-13'),
(86, 2, 'pengeluaran', 152570.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-02-14'),
(87, 2, 'pengeluaran', 149078.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-02-14'),
(88, 2, 'pemasukan', 4700768.00, 'Gaji', 'Gaji bulanan', '2026-02-15'),
(89, 2, 'pengeluaran', 311209.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-15'),
(90, 2, 'pengeluaran', 199292.00, 'Makanan', 'Pengeluaran makanan', '2026-02-16'),
(91, 2, 'pengeluaran', 354182.00, 'Belanja', 'Pengeluaran belanja', '2026-02-16'),
(92, 2, 'pengeluaran', 429942.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-02-17'),
(93, 2, 'pengeluaran', 110019.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-17'),
(94, 2, 'pengeluaran', 282840.00, 'Transportasi', 'Pengeluaran transportasi', '2026-02-17'),
(95, 2, 'pengeluaran', 145508.00, 'Makanan', 'Pengeluaran makanan', '2026-02-18'),
(96, 2, 'pengeluaran', 372497.00, 'Makanan', 'Pengeluaran makanan', '2026-02-18'),
(97, 2, 'pengeluaran', 100994.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-02-19'),
(98, 2, 'pengeluaran', 71113.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-02-19'),
(99, 2, 'pengeluaran', 67468.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-19'),
(100, 2, 'pengeluaran', 421104.00, 'Makanan', 'Pengeluaran makanan', '2026-02-20'),
(101, 2, 'pengeluaran', 282303.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-20'),
(102, 2, 'pemasukan', 912375.00, 'Freelance', 'Project freelance', '2026-02-21'),
(103, 2, 'pengeluaran', 436260.00, 'Belanja', 'Pengeluaran belanja', '2026-02-21'),
(104, 2, 'pengeluaran', 178337.00, 'Makanan', 'Pengeluaran makanan', '2026-02-21'),
(105, 2, 'pengeluaran', 378646.00, 'Belanja', 'Pengeluaran belanja', '2026-02-21'),
(106, 2, 'pengeluaran', 381551.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-02-22'),
(107, 2, 'pengeluaran', 342935.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-02-22'),
(108, 2, 'pengeluaran', 246386.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-22'),
(109, 2, 'pengeluaran', 33131.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-23'),
(110, 2, 'pengeluaran', 337103.00, 'Makanan', 'Pengeluaran makanan', '2026-02-23'),
(111, 2, 'pengeluaran', 159534.00, 'Makanan', 'Pengeluaran makanan', '2026-02-24'),
(112, 2, 'pengeluaran', 415167.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-02-24'),
(113, 2, 'pengeluaran', 396876.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-24'),
(114, 2, 'pengeluaran', 141800.00, 'Makanan', 'Pengeluaran makanan', '2026-02-25'),
(115, 2, 'pengeluaran', 289839.00, 'Transportasi', 'Pengeluaran transportasi', '2026-02-25'),
(116, 2, 'pengeluaran', 338808.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-25'),
(117, 2, 'pengeluaran', 246587.00, 'Makanan', 'Pengeluaran makanan', '2026-02-26'),
(118, 2, 'pengeluaran', 117325.00, 'Hiburan', 'Pengeluaran hiburan', '2026-02-26'),
(119, 2, 'pengeluaran', 183646.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-02-26'),
(120, 2, 'pengeluaran', 155277.00, 'Tagihan', 'Pengeluaran tagihan', '2026-02-27'),
(121, 2, 'pengeluaran', 287697.00, 'Belanja', 'Pengeluaran belanja', '2026-02-27'),
(122, 2, 'pengeluaran', 255087.00, 'Transportasi', 'Pengeluaran transportasi', '2026-02-28'),
(123, 2, 'pemasukan', 7281695.00, 'Gaji', 'Gaji bulanan', '2026-03-01'),
(124, 2, 'pengeluaran', 76065.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-03-01'),
(125, 2, 'pengeluaran', 230865.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-01'),
(126, 2, 'pengeluaran', 79704.00, 'Belanja', 'Pengeluaran belanja', '2026-03-02'),
(127, 2, 'pengeluaran', 189374.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-03-02'),
(128, 2, 'pengeluaran', 49561.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-03'),
(129, 2, 'pengeluaran', 112903.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-03'),
(130, 2, 'pengeluaran', 210431.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-03'),
(131, 2, 'pengeluaran', 390776.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-04'),
(132, 2, 'pengeluaran', 100397.00, 'Belanja', 'Pengeluaran belanja', '2026-03-04'),
(133, 2, 'pengeluaran', 182127.00, 'Makanan', 'Pengeluaran makanan', '2026-03-04'),
(134, 2, 'pengeluaran', 443192.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-03-05'),
(135, 2, 'pengeluaran', 371592.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-06'),
(136, 2, 'pengeluaran', 230347.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-06'),
(137, 2, 'pengeluaran', 91878.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-06'),
(138, 2, 'pemasukan', 882424.00, 'Freelance', 'Project freelance', '2026-03-07'),
(139, 2, 'pengeluaran', 25840.00, 'Belanja', 'Pengeluaran belanja', '2026-03-07'),
(140, 2, 'pengeluaran', 410570.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-07'),
(141, 2, 'pengeluaran', 90682.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-07'),
(142, 2, 'pengeluaran', 391261.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-08'),
(143, 2, 'pengeluaran', 201709.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-08'),
(144, 2, 'pengeluaran', 421346.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-09'),
(145, 2, 'pengeluaran', 414061.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-03-09'),
(146, 2, 'pengeluaran', 408099.00, 'Makanan', 'Pengeluaran makanan', '2026-03-10'),
(147, 2, 'pengeluaran', 229770.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-11'),
(148, 2, 'pengeluaran', 165884.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-12'),
(149, 2, 'pengeluaran', 356534.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-13'),
(150, 2, 'pengeluaran', 391404.00, 'Makanan', 'Pengeluaran makanan', '2026-03-14'),
(151, 2, 'pengeluaran', 280970.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-14'),
(152, 2, 'pemasukan', 5807045.00, 'Gaji', 'Gaji bulanan', '2026-03-15'),
(153, 2, 'pengeluaran', 394282.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-15'),
(154, 2, 'pengeluaran', 346546.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-15'),
(155, 2, 'pengeluaran', 136058.00, 'Belanja', 'Pengeluaran belanja', '2026-03-15'),
(156, 2, 'pengeluaran', 129792.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-16'),
(157, 2, 'pengeluaran', 394279.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-16'),
(158, 2, 'pengeluaran', 446518.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-17'),
(159, 2, 'pengeluaran', 199508.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-17'),
(160, 2, 'pengeluaran', 437273.00, 'Makanan', 'Pengeluaran makanan', '2026-03-18'),
(161, 2, 'pengeluaran', 283763.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-19'),
(162, 2, 'pengeluaran', 234364.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-19'),
(163, 2, 'pengeluaran', 449755.00, 'Belanja', 'Pengeluaran belanja', '2026-03-19'),
(164, 2, 'pengeluaran', 395933.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-20'),
(165, 2, 'pemasukan', 892555.00, 'Freelance', 'Project freelance', '2026-03-21'),
(166, 2, 'pengeluaran', 82681.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-21'),
(167, 2, 'pengeluaran', 371054.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-03-21'),
(168, 2, 'pengeluaran', 270108.00, 'Makanan', 'Pengeluaran makanan', '2026-03-22'),
(169, 2, 'pengeluaran', 320927.00, 'Makanan', 'Pengeluaran makanan', '2026-03-22'),
(170, 2, 'pengeluaran', 43475.00, 'Belanja', 'Pengeluaran belanja', '2026-03-22'),
(171, 2, 'pengeluaran', 217561.00, 'Makanan', 'Pengeluaran makanan', '2026-03-23'),
(172, 2, 'pengeluaran', 307953.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-23'),
(173, 2, 'pengeluaran', 259145.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-24'),
(174, 2, 'pengeluaran', 82741.00, 'Hiburan', 'Pengeluaran hiburan', '2026-03-25'),
(175, 2, 'pengeluaran', 29932.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-25'),
(176, 2, 'pengeluaran', 194849.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-03-25'),
(177, 2, 'pengeluaran', 182468.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-26'),
(178, 2, 'pengeluaran', 124334.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-26'),
(179, 2, 'pengeluaran', 141123.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-27'),
(180, 2, 'pengeluaran', 329899.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-27'),
(181, 2, 'pengeluaran', 313341.00, 'Belanja', 'Pengeluaran belanja', '2026-03-28'),
(182, 2, 'pengeluaran', 164419.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-28'),
(183, 2, 'pengeluaran', 261566.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-28'),
(184, 2, 'pengeluaran', 75100.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-29'),
(185, 2, 'pengeluaran', 80708.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-29'),
(186, 2, 'pengeluaran', 123951.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-03-29'),
(187, 2, 'pengeluaran', 347173.00, 'Transportasi', 'Pengeluaran transportasi', '2026-03-30'),
(188, 2, 'pengeluaran', 277730.00, 'Tagihan', 'Pengeluaran tagihan', '2026-03-30'),
(189, 2, 'pengeluaran', 286762.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-03-30'),
(190, 2, 'pengeluaran', 361166.00, 'Makanan', 'Pengeluaran makanan', '2026-03-31'),
(191, 2, 'pemasukan', 6492754.00, 'Gaji', 'Gaji bulanan', '2026-04-01'),
(192, 2, 'pengeluaran', 28431.00, 'Belanja', 'Pengeluaran belanja', '2026-04-01'),
(193, 2, 'pengeluaran', 268995.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-02'),
(194, 2, 'pengeluaran', 71265.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-02'),
(195, 2, 'pengeluaran', 226024.00, 'Makanan', 'Pengeluaran makanan', '2026-04-02'),
(196, 2, 'pengeluaran', 195359.00, 'Belanja', 'Pengeluaran belanja', '2026-04-03'),
(197, 2, 'pengeluaran', 110840.00, 'Makanan', 'Pengeluaran makanan', '2026-04-03'),
(198, 2, 'pengeluaran', 271438.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-03'),
(199, 2, 'pengeluaran', 220143.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-04'),
(200, 2, 'pengeluaran', 168894.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-04'),
(201, 2, 'pengeluaran', 90040.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-04'),
(202, 2, 'pengeluaran', 341505.00, 'Belanja', 'Pengeluaran belanja', '2026-04-05'),
(203, 2, 'pengeluaran', 129396.00, 'Makanan', 'Pengeluaran makanan', '2026-04-05'),
(204, 2, 'pengeluaran', 387251.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-06'),
(205, 2, 'pengeluaran', 187424.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-06'),
(206, 2, 'pengeluaran', 407916.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-06'),
(207, 2, 'pemasukan', 1150129.00, 'Freelance', 'Project freelance', '2026-04-07'),
(208, 2, 'pengeluaran', 268347.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-07'),
(209, 2, 'pengeluaran', 247920.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-07'),
(210, 2, 'pengeluaran', 336105.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-07'),
(211, 2, 'pengeluaran', 379607.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-08'),
(212, 2, 'pengeluaran', 428371.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-08'),
(213, 2, 'pengeluaran', 426520.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-09'),
(214, 2, 'pengeluaran', 97504.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-10'),
(215, 2, 'pengeluaran', 69060.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-10'),
(216, 2, 'pengeluaran', 397017.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-11'),
(217, 2, 'pengeluaran', 54022.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-11'),
(218, 2, 'pengeluaran', 309402.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-11'),
(219, 2, 'pengeluaran', 448702.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-12'),
(220, 2, 'pengeluaran', 343670.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-12'),
(221, 2, 'pengeluaran', 244536.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-12'),
(222, 2, 'pengeluaran', 297584.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-13'),
(223, 2, 'pengeluaran', 188183.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-04-14'),
(224, 2, 'pengeluaran', 431886.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-14'),
(225, 2, 'pengeluaran', 305767.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-14'),
(226, 2, 'pemasukan', 5411645.00, 'Gaji', 'Gaji bulanan', '2026-04-15'),
(227, 2, 'pengeluaran', 301094.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-15'),
(228, 2, 'pengeluaran', 38913.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-16'),
(229, 2, 'pengeluaran', 266553.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-17'),
(230, 2, 'pengeluaran', 300330.00, 'Makanan', 'Pengeluaran makanan', '2026-04-17'),
(231, 2, 'pengeluaran', 404442.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-17'),
(232, 2, 'pengeluaran', 399873.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-18'),
(233, 2, 'pengeluaran', 176722.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-19'),
(234, 2, 'pengeluaran', 171601.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-19'),
(235, 2, 'pengeluaran', 252069.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-20'),
(236, 2, 'pengeluaran', 406831.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-20'),
(237, 2, 'pemasukan', 872293.00, 'Freelance', 'Project freelance', '2026-04-21'),
(238, 2, 'pengeluaran', 229568.00, 'Belanja', 'Pengeluaran belanja', '2026-04-21'),
(239, 2, 'pengeluaran', 34831.00, 'Makanan', 'Pengeluaran makanan', '2026-04-21'),
(240, 2, 'pengeluaran', 238703.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-04-22'),
(241, 2, 'pengeluaran', 284874.00, 'Belanja', 'Pengeluaran belanja', '2026-04-23'),
(242, 2, 'pengeluaran', 204339.00, 'Belanja', 'Pengeluaran belanja', '2026-04-23'),
(243, 2, 'pengeluaran', 80356.00, 'Makanan', 'Pengeluaran makanan', '2026-04-24'),
(244, 2, 'pengeluaran', 437897.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-24'),
(245, 2, 'pengeluaran', 81928.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-25'),
(246, 2, 'pengeluaran', 425701.00, 'Makanan', 'Pengeluaran makanan', '2026-04-26'),
(247, 2, 'pengeluaran', 423666.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-26'),
(248, 2, 'pengeluaran', 310066.00, 'Hiburan', 'Pengeluaran hiburan', '2026-04-27'),
(249, 2, 'pengeluaran', 369117.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-28'),
(250, 2, 'pengeluaran', 303807.00, 'Transportasi', 'Pengeluaran transportasi', '2026-04-28'),
(251, 2, 'pengeluaran', 249796.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-04-29'),
(252, 2, 'pengeluaran', 97339.00, 'Tagihan', 'Pengeluaran tagihan', '2026-04-30'),
(253, 2, 'pengeluaran', 255491.00, 'Rumah Tangga', 'Pengeluaran rumah tangga', '2026-04-30'),
(254, 2, 'pengeluaran', 95405.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-04-30'),
(255, 2, 'pemasukan', 7987784.00, 'Gaji', 'Gaji bulanan', '2026-05-01'),
(256, 2, 'pengeluaran', 179042.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-05-01'),
(257, 2, 'pengeluaran', 331391.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-05-02'),
(258, 2, 'pengeluaran', 231008.00, 'Kesehatan', 'Pengeluaran kesehatan', '2026-05-02'),
(259, 2, 'pengeluaran', 387268.00, 'Hiburan', 'Pengeluaran hiburan', '2026-05-02'),
(260, 2, 'pengeluaran', 245925.00, 'Hiburan', 'Pengeluaran hiburan', '2026-05-03'),
(261, 2, 'pengeluaran', 238942.00, 'Pendidikan', 'Pengeluaran pendidikan', '2026-05-03'),
(262, 2, 'pengeluaran', 261881.00, 'Hiburan', 'Pengeluaran hiburan', '2026-05-04'),
(263, 2, 'pengeluaran', 326493.00, 'Makanan', 'Pengeluaran makanan', '2026-05-04'),
(264, 3, 'pemasukan', 2000000.00, 'gaji', 'gaji bulanan sayaa', '2026-05-04'),
(265, 11, 'pemasukan', 5454519457.00, 'jakpot', 'hshahahahs', '2026-05-09'),
(266, 11, 'pengeluaran', 555580.00, 'makan minum', 'gegshs', '2026-05-09'),
(267, 14, 'pemasukan', 4000000.00, 'Usaha', 'usaha', '2026-05-28'),
(268, 14, 'pengeluaran', 200000.00, 'Makan', 'makan diluar', '2026-05-28'),
(269, 15, 'pemasukan', 4000000.00, 'Usaha', 'Usaha', '2026-05-29'),
(270, 15, 'pengeluaran', 200000.00, 'Makan', 'makan diluar', '2026-05-29'),
(271, 16, 'pemasukan', 2000000.00, 'Gaji', 'Gaji bulanan', '2026-05-29'),
(272, 15, 'pemasukan', 400000.00, 'Gaji', 'Sns', '2026-06-04'),
(273, 4, 'pemasukan', 20000.00, 'Investasi', 'y2u2', '2026-06-11');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_premium`
--

CREATE TABLE `transaksi_premium` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `kode_pembayaran` varchar(100) NOT NULL,
  `status` enum('pending','berhasil','gagal','expired') NOT NULL DEFAULT 'pending',
  `jumlah` int(11) NOT NULL DEFAULT 7000,
  `tanggal` datetime NOT NULL,
  `kode_invoice` varchar(100) DEFAULT NULL,
  `metode_pembayaran` varchar(100) DEFAULT NULL,
  `waktu_buat` datetime DEFAULT NULL,
  `waktu_bayar` datetime DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `payment_number` text DEFAULT NULL,
  `total_payment` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi_premium`
--

INSERT INTO `transaksi_premium` (`id_transaksi`, `id_user`, `kode_pembayaran`, `status`, `jumlah`, `tanggal`, `kode_invoice`, `metode_pembayaran`, `waktu_buat`, `waktu_bayar`, `expired_at`, `payment_number`, `total_payment`) VALUES
(1, 1, 'KAS-PREMIUM-20260504050907-0ba5e1', '', 7000, '2026-05-04 10:09:07', 'KAS-PREMIUM-20260504050907-0ba5e1', NULL, '2026-05-04 10:09:07', NULL, '2026-05-04 11:09:07', NULL, NULL),
(2, 4, 'KAS-INV-20260504083057-6E64', 'expired', 7000, '2026-05-04 15:30:57', 'KAS-INV-20260504083057-6E64', 'qris', '2026-05-04 15:30:57', NULL, '2026-05-04 09:30:57', 'THIS.IS.JUST.AN.EXAMPLE.FOR.SANDBOX.00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW.11111', 7359),
(3, 4, 'KAS-INV-20260528131223-8E07', 'gagal', 7000, '2026-05-28 20:12:24', 'KAS-INV-20260528131223-8E07', NULL, '2026-05-28 20:12:24', NULL, '2026-05-28 14:12:23', NULL, NULL),
(4, 4, 'KAS-INV-20260528131234-79FB', 'gagal', 7000, '2026-05-28 20:12:35', 'KAS-INV-20260528131234-79FB', NULL, '2026-05-28 20:12:35', NULL, '2026-05-28 14:12:34', NULL, NULL),
(5, 4, 'KAS-INV-20260528131355-F60E', 'gagal', 7000, '2026-05-28 20:13:55', 'KAS-INV-20260528131355-F60E', NULL, '2026-05-28 20:13:55', NULL, '2026-05-28 14:13:55', NULL, NULL),
(6, 4, 'KAS-INV-20260528131403-A042', 'gagal', 7000, '2026-05-28 20:14:04', 'KAS-INV-20260528131403-A042', NULL, '2026-05-28 20:14:04', NULL, '2026-05-28 14:14:03', NULL, NULL),
(7, 4, 'KAS-INV-20260528131519-0FC3', 'gagal', 7000, '2026-05-28 20:15:19', 'KAS-INV-20260528131519-0FC3', NULL, '2026-05-28 20:15:19', NULL, '2026-05-28 14:15:19', NULL, NULL),
(8, 4, 'KAS-INV-20260528225028-71CC', 'gagal', 7000, '2026-05-29 05:50:29', 'KAS-INV-20260528225028-71CC', NULL, '2026-05-29 05:50:29', NULL, '2026-05-28 23:50:28', NULL, NULL),
(9, 4, 'KAS-INV-20260528225504-EE03', 'gagal', 7000, '2026-05-29 05:55:05', 'KAS-INV-20260528225504-EE03', NULL, '2026-05-29 05:55:05', NULL, '2026-05-28 23:55:04', NULL, NULL),
(10, 4, 'KAS-INV-20260528231151-75FB', 'expired', 7000, '2026-05-29 06:11:52', 'KAS-INV-20260528231151-75FB', 'qris', '2026-05-29 06:11:52', NULL, '2026-05-29 00:11:52', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054077359.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181153388731592789056304C7E0', 7359),
(11, 3, 'KAS-INV-20260528231235-C6DC', 'expired', 7000, '2026-05-29 06:12:36', 'KAS-INV-20260528231235-C6DC', 'qris', '2026-05-29 06:12:36', NULL, '2026-05-29 00:12:36', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054077359.005802ID5907Pakasir6012KAB. KEBUMEN61055439262220518123974793327445982630427A5', 7359),
(12, 3, 'KAS-INV-20260528231351-68C5', 'expired', 7000, '2026-05-29 06:13:51', 'KAS-INV-20260528231351-68C5', 'qris', '2026-05-29 06:13:51', NULL, '2026-05-29 00:13:52', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054077359.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181035614536737606076304376A', 7359),
(13, 3, 'KAS-INV-20260528233148-5880', 'expired', 7000, '2026-05-29 06:31:49', 'KAS-INV-20260528233148-5880', 'qris', '2026-05-29 06:31:49', NULL, '2026-05-29 00:31:49', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054077359.005802ID5907Pakasir6012KAB. KEBUMEN6105543926222051815575253065614731163045A8C', 7359),
(14, 3, 'KAS-INV-20260528233617-30E9', 'expired', 2000, '2026-05-29 06:36:18', 'KAS-INV-20260528233617-30E9', 'qris', '2026-05-29 06:36:18', NULL, '2026-05-29 00:36:18', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN6105543926222051813680877304944098763049C7C', 2324),
(15, 3, 'KAS-INV-20260528233903-55D7', 'expired', 2000, '2026-05-29 06:39:04', 'KAS-INV-20260528233903-55D7', 'qris', '2026-05-29 06:39:04', NULL, '2026-05-29 00:39:04', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN6105543926222051812223057798269210763048A08', 2324),
(16, 3, 'KAS-INV-20260529064327-1108', 'berhasil', 2000, '2026-05-29 06:43:28', 'KAS-INV-20260529064327-1108', 'qris', '2026-05-29 06:43:28', '2026-05-29 06:43:44', '2026-05-29 07:43:28', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181409286966936084466304569C', 2324),
(17, 14, 'KAS-INV-20260529070741-1BA5', 'expired', 2000, '2026-05-29 07:07:42', 'KAS-INV-20260529070741-1BA5', 'qris', '2026-05-29 07:07:42', NULL, '2026-05-29 08:07:42', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181111599666744854486304D031', 2324),
(18, 15, 'KAS-INV-20260529073624-82CC', 'berhasil', 2000, '2026-05-29 07:36:25', 'KAS-INV-20260529073624-82CC', 'qris', '2026-05-29 07:36:25', '2026-05-29 07:36:33', '2026-05-29 08:36:25', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181539774050867616876304C461', 2324),
(19, 14, 'KAS-INV-20260529092005-B464', 'expired', 2000, '2026-05-29 09:20:06', 'KAS-INV-20260529092005-B464', 'qris', '2026-05-29 09:20:06', NULL, '2026-05-29 10:20:06', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181005588874570208736304C453', 2324),
(20, 4, 'KAS-INV-20260529094125-43C3', 'expired', 2000, '2026-05-29 09:41:26', 'KAS-INV-20260529094125-43C3', 'qris', '2026-05-29 09:41:26', NULL, '2026-05-29 10:41:26', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054072324.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181132480986837200766304C3C2', 2324),
(21, 4, 'KAS-INV-20260602102610-722A', 'expired', 3000, '2026-06-02 10:26:10', 'KAS-INV-20260602102610-722A', 'qris', '2026-06-02 10:26:10', NULL, '2026-06-02 11:26:10', '00020101021226610016ID.CO.SHOPEE.WWW01189360091800216005230208216005230303UME51440014ID.CO.QRIS.WWW0215ID10243228429300303UME52047929530336054073331.005802ID5907Pakasir6012KAB. KEBUMEN610554392622205181354285401421906876304524C', 3331),
(22, 4, 'KAS-INV-20260709190508-AF10', 'berhasil', 3000, '2026-07-09 19:05:08', 'KAS-INV-20260709190508-AF10', 'qris', '2026-07-09 19:05:08', '2026-07-09 19:05:59', '2026-07-09 20:05:08', '00020101021226670016COM.NOBUBANK.WWW01189360050300000862280214518371731478540303UKE51440014ID.CO.QRIS.WWW0303UKE0215ID202326604433852047372530336054073331.005802ID5906ZoneID6007KEBUMEN61055439262720108MA8XKVBD0619NQ26V71ORGHHWT478HH0703A010804POSP99180002000108202607096304FC63', 3331);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `tanggal_daftar` datetime NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `preferensi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferensi`)),
  `status_user` enum('biasa','premium') NOT NULL DEFAULT 'biasa',
  `sisa_prompt` int(11) NOT NULL DEFAULT 15,
  `tanggal_reset_prompt` date DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `premium_expired` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `kata_sandi`, `tanggal_daftar`, `foto`, `preferensi`, `status_user`, `sisa_prompt`, `tanggal_reset_prompt`, `role`, `premium_expired`) VALUES
(1, 'jashub', 'jas@gmail.com', '$2y$10$JsDaYTetXA1SN0zYkcFkw.rE7p5opVr5QUKXWkmZGkp6Ty5LMfab.', '2026-05-04 08:21:19', NULL, NULL, 'premium', -1, '2026-05-04', 'admin', NULL),
(2, 'Rifky Demo', 'demo@finansku.local', '$2y$10$pieBvBTBYqjy/lcp9yhxPOsCC7/5RTai8r0xcNSCgofhXhb6ip5VO', '2026-05-04 08:52:38', 'assets/uploads/profil/profil_2_1777860194.jpeg', NULL, 'biasa', 15, NULL, 'user', NULL),
(3, 'sohib', 'sohib@gmail.com', '$2y$10$c1hqA63MOJzbDVDx5YruCeU79eRqmYyUhHXCLZ720G68ukbNaz34q', '2026-05-04 11:44:32', 'assets/uploads/profil/profil_3_1780011868.jpg', '{\"tema\":\"dark\",\"notifikasi\":true,\"mata_uang\":\"IDR\"}', 'premium', -1, '2026-05-04', 'user', NULL),
(4, 'baru', 'baru@gmail.com', '$2y$10$kcpluqU51xumRBHkQUyQieZyNlUZNxsCuT/FQqMyZZ0hBOI5/IEB2', '2026-05-04 13:03:32', 'assets/uploads/profil/profil_4_1779973923.jpg', NULL, 'premium', -1, '2026-06-20', 'user', NULL),
(5, 'gg', 'wahyuhidayatullah290903@gmail.com', '$2y$10$YRB765H.1FCrU2rR6ptnDON8skzK2JXIw6e..OGGdyXFI8.xrP6Rq', '2026-05-04 13:51:20', NULL, NULL, 'biasa', 15, '2026-05-04', 'user', NULL),
(6, 'Anjing', 'aakkaaka@gmail.commmmmmmm', '$2y$10$kHk3NtJOPfQJamGj8WoqmOVq4ikEAHv90JePW1ihkXmFgEhTyWJA2', '2026-05-04 13:54:33', NULL, '{\"tema\":\"dark\",\"notifikasi\":true,\"mata_uang\":\"IDR\"}', 'biasa', 0, '2026-05-04', 'user', NULL),
(7, 'jajaja', 'jashub@gmai.com', '$2y$10$sylUlAGLlNvNoYzNWzPVnOVdfskYn/gWIsPEajSCXt.snWd88q9m6', '2026-05-04 14:42:42', NULL, NULL, 'biasa', 15, '2026-05-04', 'user', NULL),
(8, 'jashub store', 'jashubpremium@gmail.com', '$2y$10$GXWufeujT9YZauAQBm177eau6NBES.soZFnFcNn2zQAiC6xjXTXu2', '2026-05-04 14:43:09', NULL, NULL, 'biasa', 15, '2026-05-04', 'user', NULL),
(9, 'Husain', 'husain@mail.com', '$2y$10$F1qPTVClFjNL99o1Zj7htulTe/HVVwsyo6/62dfMJwQiO9zkx5/mK', '2026-05-04 16:15:15', 'assets/uploads/profil/profil_9_1777886670.jpg', NULL, 'biasa', 15, '2026-05-04', 'user', NULL),
(10, 'kevin', 'marvinsteve60@gmail.com', '$2y$10$eL3nTDsxPF2k3GqnEP6Fku64n44bIQ13Iu9/Yy6XMDg/Z8ZHs0JU.', '2026-05-06 08:36:21', NULL, NULL, 'biasa', 15, '2026-05-06', 'user', NULL),
(11, 'owi', 'tembokratapansoloo@gmail.com', '$2y$10$GRq/rXYTIbtEWW1MNjjm5eneUK4S6L34KGZAfTCR798.v7pC55H9S', '2026-05-07 18:38:12', NULL, '{\"tema\":\"dark\",\"notifikasi\":true,\"mata_uang\":\"IDR\"}', 'biasa', 13, '2026-05-09', 'user', NULL),
(12, 'Angga Baitur rahman', 'm.anggabaiturrahman@gmail.com', '$2y$10$bnF7NEuzDH0cN7Jzo/6CRumJHJRxOacrYVpKzRtdukI4cxGloK4/2', '2026-05-09 11:07:09', NULL, NULL, 'biasa', 1, '2026-05-09', 'user', NULL),
(13, 'Rikitolol', 'rikitolol@jajjqj.com', '$2y$10$7XxmHywuUtjqQ6GOV/DyEujlPMtg.9dDgBYgYN6ZoT0sjbePeNUDi', '2026-05-09 11:09:53', NULL, NULL, 'biasa', 13, '2026-05-09', 'user', NULL),
(14, 'jashub', 'jashubprem@gmail.com', '$2y$10$PhMExoYiW9BaAtRKf.nzMe7scN7uC2oy0J6NMVsRMcnlNfAEEYuy6', '2026-05-29 06:46:03', NULL, NULL, 'biasa', 12, '2026-05-29', 'user', NULL),
(15, 'kastra', 'kastra@gmail.com', '$2y$10$asX.436LQ0XvdBN6USInZ.aSax7Mzl1uUXBRWICgPGh5swXFao7KS', '2026-05-29 07:33:23', 'assets/uploads/profil/profil_15_1780188007.jpg', '{\"tema\":\"dark\",\"notifikasi\":true,\"mata_uang\":\"IDR\"}', 'premium', -1, '2026-05-29', 'user', NULL),
(16, 'Fahry Khusain', 'fahrykhusain77@gmail.com', '$2y$10$4Wzfr2TUBQOqirOOypKnK.jn8Ge.hQbcWlXx2HE665El7vYZYIR5a', '2026-05-29 08:52:19', NULL, NULL, 'biasa', 15, '2026-05-29', 'user', NULL),
(17, 'eew2', 'eh@gmail.com', '$2y$10$zBfzOBZyVZl8jdmNUpeOfe7gArm3.Bb6z6GHYu6Rk.l88gD76vPO.', '2026-06-04 18:11:28', NULL, NULL, 'biasa', 15, '2026-06-04', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_ai`
--
ALTER TABLE `chat_ai`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `fk_chat_ai_user` (`id_user`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `fk_kategori_user` (`id_user`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id_reset`),
  ADD KEY `idx_password_reset_email` (`email`),
  ADD KEY `idx_password_reset_expired` (`expired_at`),
  ADD KEY `fk_password_reset_user` (`id_user`);

--
-- Indexes for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  ADD PRIMARY KEY (`id_pengaturan`),
  ADD UNIQUE KEY `nama_pengaturan` (`nama_pengaturan`);

--
-- Indexes for table `tabungan`
--
ALTER TABLE `tabungan`
  ADD PRIMARY KEY (`id_tabungan`),
  ADD KEY `fk_tabungan_user` (`id_user`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_user` (`id_user`);

--
-- Indexes for table `transaksi_premium`
--
ALTER TABLE `transaksi_premium`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_premium_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_ai`
--
ALTER TABLE `chat_ai`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id_reset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7466;

--
-- AUTO_INCREMENT for table `tabungan`
--
ALTER TABLE `tabungan`
  MODIFY `id_tabungan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;

--
-- AUTO_INCREMENT for table `transaksi_premium`
--
ALTER TABLE `transaksi_premium`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_ai`
--
ALTER TABLE `chat_ai`
  ADD CONSTRAINT `fk_chat_ai_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `kategori`
--
ALTER TABLE `kategori`
  ADD CONSTRAINT `fk_kategori_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_reset_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tabungan`
--
ALTER TABLE `tabungan`
  ADD CONSTRAINT `fk_tabungan_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_premium`
--
ALTER TABLE `transaksi_premium`
  ADD CONSTRAINT `fk_transaksi_premium_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
