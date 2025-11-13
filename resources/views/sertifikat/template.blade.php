<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Penghargaan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        /* Background with Geometric Pattern */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            background: #1a1a2e;
            top: 0;
            left: 0;
        }

        /* Geometric Shapes - Simplified for DomPDF */
        .shape {
            position: absolute;
            background: #2a2a3e;
        }

        .shape-tl {
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
        }

        .shape-tr {
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
        }

        .shape-bl {
            bottom: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
        }

        .shape-br {
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
        }

        /* Decorative Elements - Simplified */
        .corner-accent {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 2px solid #d4af37;
        }

        .corner-accent-tl {
            top: 20px;
            left: 20px;
            border-right: none;
            border-bottom: none;
        }

        .corner-accent-tr {
            top: 20px;
            right: 20px;
            border-left: none;
            border-bottom: none;
        }

        .corner-accent-bl {
            bottom: 20px;
            left: 20px;
            border-right: none;
            border-top: none;
        }

        .corner-accent-br {
            bottom: 20px;
            right: 20px;
            border-left: none;
            border-top: none;
        }

        /* Certificate Container */
        .certificate {
            position: absolute;
            top: 15mm;
            left: 15mm;
            width: 267mm;
            height: 180mm;
            background: #ffffff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        /* Border Design */
        .border-outer {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #d4af37;
        }

        .border-inner {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 1px solid #d4af37;
        }

        /* Corner Decorations */
        .corner-line {
            position: absolute;
            background: #d4af37;
        }

        .corner-tl-h {
            top: 25px;
            left: 25px;
            width: 50px;
            height: 2px;
        }

        .corner-tl-v {
            top: 25px;
            left: 25px;
            width: 2px;
            height: 50px;
        }

        .corner-tr-h {
            top: 25px;
            right: 25px;
            width: 50px;
            height: 2px;
        }

        .corner-tr-v {
            top: 25px;
            right: 25px;
            width: 2px;
            height: 50px;
        }

        .corner-bl-h {
            bottom: 25px;
            left: 25px;
            width: 50px;
            height: 2px;
        }

        .corner-bl-v {
            bottom: 25px;
            left: 25px;
            width: 2px;
            height: 50px;
        }

        .corner-br-h {
            bottom: 25px;
            right: 25px;
            width: 50px;
            height: 2px;
        }

        .corner-br-v {
            bottom: 25px;
            right: 25px;
            width: 2px;
            height: 50px;
        }

        /* Content Container */
        .content {
            position: relative;
            padding: 35px 50px 30px;
            height: 100%;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .title {
            font-size: 48pt;
            font-weight: bold;
            letter-spacing: 10px;
            color: #1a1a2e;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 14pt;
            font-style: italic;
            color: #666;
            letter-spacing: 3px;
        }

        /* Decorative Line with Diamond */
        .divider {
            text-align: center;
            margin: 15px 0 20px;
            height: 20px;
            position: relative;
        }

        .divider:before {
            content: '';
            position: absolute;
            left: 30%;
            top: 50%;
            width: 15%;
            height: 1px;
            background: #d4af37;
        }

        .divider:after {
            content: '';
            position: absolute;
            right: 30%;
            top: 50%;
            width: 15%;
            height: 1px;
            background: #d4af37;
        }

        .divider-diamond {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #d4af37;
            transform: rotate(45deg);
            margin-top: 4px;
        }

        /* Body */
        .body {
            text-align: center;
            padding: 20px 40px;
        }

        .intro {
            font-size: 12pt;
            color: #333;
            margin-bottom: 10px;
        }

        .name {
            font-size: 26pt;
            font-weight: bold;
            color: #1a1a2e;
            margin: 10px 0 15px;
            font-style: italic;
            letter-spacing: 2px;
        }

        .description {
            font-size: 15pt;
            color: #444;
            line-height: 1.6;
            margin-bottom: 15px;
            padding: 0 60px;
        }

        .score {
            font-size: 22pt;
            font-weight: bold;
            color: #001275ff;
            margin: 10px 0;
            letter-spacing: 2px;
        }

        /* Golden Seal with Ribbon */
        .seal-wrapper {
            text-align: center;
            margin: 15px 0;
            position: relative;
            bottom:315px;
        }

        .seal-circle {
            display: inline-block;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #ffd700;
            border: 4px solid #d4af37;
            position: relative;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.5);
        }

        .seal-inner-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 72px;
            height: 72px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
        }

        .seal-star {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 36pt;
            color: #fff;
            font-weight: bold;
            line-height: 1;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
        }

        .seal-ribbon {
            text-align: center;
            margin-top: -10px;
            position: relative;
        }

        .ribbon-piece {
            display: inline-block;
            width: 18px;
            height: 36px;
            background: #c0392b;
            margin: 0 1px;
            vertical-align: top;
        }

        .ribbon-piece:nth-child(2) {
            background: #a93226;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 105px;
            left: 50px;
            right: 50px;
            bottom: 20px;
        }

        .footer-row {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .footer-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .sig-line {
            width: 150px;
            height: 1px;
            background: #333;
            margin: 0 auto 10px;
        }

        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 3px;
        }

        .sig-title {
            font-size: 8pt;
            font-style: italic;
            color: #666;
        }

        .date-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }

        .date-value {
            font-size: 10pt;
            font-weight: bold;
            color: #1a1a2e;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            opacity: 0.2;
            z-index: 0;
        }

        .watermark img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <!-- Background Pattern -->
    <div class="background">
        <!-- Simplified Geometric Shapes for DomPDF -->
        <div class="shape shape-tl"></div>
        <div class="shape shape-tr"></div>
        <div class="shape shape-bl"></div>
        <div class="shape shape-br"></div>
        
        <!-- Corner Accents -->
        <div class="corner-accent corner-accent-tl"></div>
        <div class="corner-accent corner-accent-tr"></div>
        <div class="corner-accent corner-accent-bl"></div>
        <div class="corner-accent corner-accent-br"></div>
    </div>

    <!-- Certificate Container -->
    <div class="certificate">
        <!-- Watermark -->
        @if(file_exists(public_path('logo/pglsmid.png')))
        <div class="watermark">
            <img src="{{ public_path('logo/pglsmid.png') }}" alt="Watermark">
        </div>
        @endif

        <!-- Borders -->
        <div class="border-outer"></div>
        <div class="border-inner"></div>

        <!-- Corner Lines -->
        <div class="corner-line corner-tl-h"></div>
        <div class="corner-line corner-tl-v"></div>
        <div class="corner-line corner-tr-h"></div>
        <div class="corner-line corner-tr-v"></div>
        <div class="corner-line corner-bl-h"></div>
        <div class="corner-line corner-bl-v"></div>
        <div class="corner-line corner-br-h"></div>
        <div class="corner-line corner-br-v"></div>

        <!-- Content -->
        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="title">SERTIFIKAT</div>
                <div class="subtitle">Penghargaan</div>
            </div>

            <!-- Divider -->
            <div class="divider">
                <span class="divider-diamond"></span>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="intro">Penghargaan ini diberikan kepada:</p>
                <div class="name">{{ $nama }}</div>
                <p class="description">
                    Telah mengikuti ujian post test BIND yang diselenggarakan 
                    pada tanggal {{ \Carbon\Carbon::parse($tanggal_ujian)->translatedFormat('d F Y') }} 
                    dengan pencapaian standar nilai yang ditentukan
                    dan memperoleh nilai akhir:
                </p>
                <div class="score">{{ $nilai }}</div>
            </div>
        </div>

        <!-- Golden Seal -->
            <div class="seal-wrapper">
                <div class="seal-circle">
                    <div class="seal-inner-circle"></div>
                    <span class="seal-star">★</span>
                </div>
                <div class="seal-ribbon">
                    <span class="ribbon-piece"></span>
                    <span class="ribbon-piece"></span>
                </div>
            </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-row">
                <!-- <div class="footer-col">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ 'Samira Hadid' }}</div>
                    <div class="sig-title">{{ 'Ketua Pensiun' }}</div>
                </div> -->
                <div class="footer-col">
                    <div class="date-label">Tanggal</div>
                    <div class="date-value">{{ $tanggal_terbit }}</div>
                </div>
                <!-- <div class="footer-col">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ 'Ketut Susilo' }}</div>
                    <div class="sig-title">{{ 'Kepala Keluarga' }}</div>
                </div> -->
            </div>
        </div>
    </div>
</body>
</html>