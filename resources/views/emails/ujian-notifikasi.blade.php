<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Undangan Ujian</title>
</head>
<body>
    <h2>Halo {{ $namaPeserta }},</h2>
    <p>Anda diundang untuk mengikuti ujian berikut:</p>
    <ul>
        <li><strong>Nama Ujian:</strong> {{ $namaUjian }}</li>
        <li><strong>Tanggal:</strong> {{ $tanggal }}</li>
        <li><strong>Kode Soal:</strong> {{ $kodeSoal }}</li>
    </ul>
    <p>Silakan login ke sistem untuk memulai ujian.</p>
    <p>Terima kasih.</p>
</body>
</html>
