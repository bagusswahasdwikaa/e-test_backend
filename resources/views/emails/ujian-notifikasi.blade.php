<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Undangan Ujian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Styling daftar key-value */
        .kv {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .kv li {
            display: grid;
            grid-template-columns: max-content 10px auto; /* label | ":" | value */
            column-gap: .5rem;
            margin-bottom: 6px;
        }

        .kv li strong {
            grid-column: 1;
            font-weight: 600;
        }

        .kv li::after {
            content: ":";
            grid-column: 2;
        }

        .kv li span {
            grid-column: 3;
        }
    </style>
</head>
<body>
    <h2>Halo {{ $namaPeserta }},</h2>
    <p>Anda diundang untuk mengikuti ujian berikut:</p>

    <ul class="kv">
        <li><strong>Nama Ujian</strong><span>{{ $namaUjian }}</span></li>
        <li><strong>Di mulai</strong><span>{{ $tanggal_mulai }}</span></li>
        <li><strong>Berakhir</strong><span>{{ $tanggal_akhir }}</span></li>
        <li><strong>Kode Soal</strong><span>{{ $kodeSoal }}</span></li>
    </ul>

    <p>Silakan login ke sistem untuk memulai ujian.</p>
    <p>Terima kasih.</p>
</body>
</html>
