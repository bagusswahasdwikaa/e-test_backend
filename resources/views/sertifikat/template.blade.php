<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        /* === HALAMAN A4 LANDSCAPE TANPA MARGIN === */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        /* === BODY === */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;  /* tetap di tengah horizontal */
            align-items: center;      /* tetap di tengah vertikal */
        }

        /* === CONTAINER UTAMA (TENGAH SEMPURNA) === */
        .certificate-container {
            position: relative;
            width: 90%;
            height: 85%;
            background: #fff;
            border: 10px solid #f4f4f4;
            box-shadow: inset 0 0 0 4px #667eea;
            padding: 4% 3% 3%; /* naikkan padding atas dari 3% → 4% supaya isi turun sedikit */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            transform: translateX(2%); /* tetap sedikit ke kanan */
        }

        /* === WATERMARK === */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8vw;
            color: #d3d3d3;
            opacity: 0.05;
            font-weight: bold;
            z-index: 0;
        }

        /* === HEADER === */
        .certificate-header {
            text-align: center;
            z-index: 2;
        }

        .certificate-title {
            font-size: 7vw;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 0.25vw;
            text-transform: uppercase;
            margin-bottom: 0.3%;
        }

        .certificate-subtitle {
            font-size: 0.9vw;
            color: #666;
            font-style: italic;
            letter-spacing: 0.1vw;
        }

        /* === BODY === */
        .certificate-body {
            text-align: center;
            z-index: 2;
        }

        .certificate-text {
            font-size: 0.95vw;
            color: #333;
            margin-bottom: 0.8%;
        }

        .recipient-name {
            font-size: 1.7vw;
            font-weight: bold;
            color: #2c3e50;
            margin: 1% 0;
            padding-bottom: 0.4%;
            border-bottom: 3px solid #667eea;
            display: inline-block;
            max-width: 70%;
        }

        .certificate-description {
            font-size: 0.9vw;
            color: #555;
            margin: 1.2% auto;
            max-width: 70%;
            line-height: 1.5;
        }

        .exam-name {
            font-size: 1.2vw;
            font-weight: bold;
            color: #764ba2;
            margin: 1% 0;
        }

        .score-container {
            margin: 1.2% 0;
            padding: 1% 2%;
            background: linear-gradient(135deg, #667eea15, #764ba215);
            border-radius: 1vw;
            display: inline-block;
        }

        .score-label {
            font-size: 0.8vw;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.15vw;
        }

        .score-value {
            font-size: 1.4vw;
            font-weight: bold;
            color: #667eea;
        }

        /* === FOOTER === */
        .certificate-footer {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 6%;
            box-sizing: border-box;
            z-index: 2;

            /* Geser footer sedikit ke kiri */
            transform: translateX(-5%);
        }

        .date-section, .signature-section {
            text-align: center;
            justify-content: center;
            flex: 1;
        }

        .date-label, .signature-label {
            font-size: 0.8vw;
            color: #666;
            margin-bottom: 0.4%;
        }

        .date-value {
            font-size: 1vw;
            font-weight: bold;
            color: #2c3e50;
        }

        .signature-line {
            width: 45%;
            height: 2px;
            background: #333;
            margin: 3.5% auto 0.8%;
        }

        .signature-name {
            font-size: 1vw;
            font-weight: bold;
            color: #2c3e50;
        }

        .signature-title {
            font-size: 0.85vw;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Ornamen -->
        <div class="decorative-element decorative-top-left"></div>
        <div class="decorative-element decorative-top-right"></div>
        <div class="decorative-element decorative-bottom-left"></div>
        <div class="decorative-element decorative-bottom-right"></div>

        <!-- Watermark -->
        <div class="watermark">CERTIFIED</div>

        <!-- Header -->
        <div class="certificate-header">
            <div class="certificate-title">SERTIFIKAT</div>
            <div class="certificate-subtitle">Certificate of Achievement</div>
        </div>

        <!-- Body -->
        <div class="certificate-body">
            <p class="certificate-text">Diberikan kepada / This is to certify that</p>
            <div class="recipient-name">{{ $nama }}</div>
            <p class="certificate-description">
                Telah berhasil menyelesaikan dan lulus ujian post test
            </p>
            <div class="exam-name">"{{ $nama_ujian }}"</div>
            <div class="score-container">
                <div class="score-label">Nilai Akhir / Final Score</div>
                <div class="score-value">{{ number_format($nilai, 0) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="certificate-footer">
            <div class="date-section">
                <p class="date-label">Tanggal Terbit</p>
                <p class="date-value">{{ $tanggal }}</p>
            </div>
            <div class="signature-section">
                <div class="signature-line"></div>
                <p class="signature-name">Direktur Akademik</p>
                <p class="signature-title">Academic Director</p>
            </div>
        </div>
    </div>
</body>
</html>
