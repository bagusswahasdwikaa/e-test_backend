<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ujian Baru</title>
</head>
<body>
    <h2>Halo</h2>


    <p>Anda telah ditugaskan untuk mengikuti ujian berikut:</p>
    <ul>
        <li><strong>Nama Ujian:</strong> {{ $ujian->nama_ujian }}</li>
        <li><strong>Waktu Mulai:</strong> {{ $ujian->tanggal_mulai }}</li>
        <li><strong>Waktu Akhir:</strong> {{ $ujian->tanggal_akhir }}</li>
        <li><strong>Kode :</strong> {{ $ujian->kode_soal }}</li>
    </ul>

    
    <p>Silakan login E-TEST http://localhost:3000/authentication/login untuk mengerjakan ujian tersebut. Semoga sukses!</p>
</body>
</html>
