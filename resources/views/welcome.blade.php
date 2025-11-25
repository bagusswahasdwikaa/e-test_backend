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
        }

        /* Background with Geometric Pattern */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }

        /* Geometric Shapes - Top Left */
        .shape-tl {
            position: absolute;
            top: -80px;
            left: -80px;
            width: 280px;
            height: 280px;
        }

        .shape-tl .box1 {
            position: absolute;
            width: 200px;
            height: 200px;
            background: #2a2a3e;
            transform: rotate(45deg);
        }

        .shape-tl .box2 {
            position: absolute;
            top: 25px;
            left: 25px;
            width: 150px;
            height: 150px;
            border: 3px solid #d4af37;
            transform: rotate(45deg);
        }

        /* Geometric Shapes - Top Right */
        .shape-tr {
            position: absolute;
            top: -80px;
            right: -80px;
            width: 280px;
            height: 280px;
        }

        .shape-tr .box1 {
            position: absolute;
            right: 0;
            width: 200px;
            height: 200px;
            background: #2a2a3e;
            transform: rotate(45deg);
        }

        .shape-tr .box2 {
            position: absolute;
            top: 25px;
            right: 25px;
            width: 150px;
            height: 150px;
            border: 3px solid #d4af37;
            transform: rotate(45deg);
        }

        /* Geometric Shapes - Bottom Left */
        .shape-bl {
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 280px;
            height: 280px;
        }

        .shape-bl .box1 {
            position: absolute;
            bottom: 0;
            width: 200px;
            height: 200px;
            background: #2a2a3e;
            transform: rotate(45deg);
        }

        .shape-bl .box2 {
            position: absolute;
            bottom: 25px;
            left: 25px;
            width: 150px;
            height: 150px;
            border: 3px solid #d4af37;
            transform: rotate(45deg);
        }

        /* Geometric Shapes - Bottom Right */
        .shape-br {
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 280px;
            height: 280px;
        }

        .shape-br .box1 {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: #2a2a3e;
            transform: rotate(45deg);
        }

        .shape-br .box2 {
            position: absolute;
            bottom: 25px;
            right: 25px;
            width: 150px;
            height: 150px;
            border: 3px solid #d4af37;
            transform: rotate(45deg);
        }

        /* Decorative Dots */
        .dots {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .dot {
            position: absolute;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
        }

        /* Decorative Leaf Pattern */
        .leaf {
            position: absolute;
            width: 80px;
            height: 80px;
            opacity: 0.08;
        }

        .leaf svg {
            width: 100%;
            height: 100%;
            fill: #e8dcc4;
        }

        /* Certificate Container */
        .certificate {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 88%;
            height: 82%;
            background: #ffffff;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        /* Watermark - Logo Panasonic */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            opacity: 0.06;
            z-index: 1;
            background-image: url('{{ asset("public/logo/panasonic.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Border Design */
        .border-outer {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 2px solid #d4af37;
        }

        .border-inner {
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            border: 1px solid #d4af37;
        }

        /* Corner Decorations */
        .corner-line {
            position: absolute;
            background: #d4af37;
        }

        .corner-tl-h {
            top: 30px;
            left: 30px;
            width: 60px;
            height: 3px;
        }

        .corner-tl-v {
            top: 30px;
            left: 30px;
            width: 3px;
            height: 60px;
        }

        .corner-tr-h {
            top: 30px;
            right: 30px;
            width: 60px;
            height: 3px;
        }

        .corner-tr-v {
            top: 30px;
            right: 30px;
            width: 3px;
            height: 60px;
        }

        .corner-bl-h {
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 3px;
        }

        .corner-bl-v {
            bottom: 30px;
            left: 30px;
            width: 3px;
            height: 60px;
        }

        .corner-br-h {
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 3px;
        }

        .corner-br-v {
            bottom: 30px;
            right: 30px;
            width: 3px;
            height: 60px;
        }

        /* Content Container */
        .content {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px 60px 35px;
            z-index: 2;
        }

        /* Header */
        .header {
            text-align: center;
        }

        .title {
            font-size: 52pt;
            font-weight: 700;
            letter-spacing: 14px;
            color: #1a1a2e;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 16pt;
            font-style: italic;
            color: #666;
            letter-spacing: 4px;
            text-transform: lowercase;
        }

        /* Decorative Line with Diamond */
        .divider-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .divider-line {
            width: 180px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }

        .divider-diamond {
            width: 15px;
            height: 15px;
            background: #d4af37;
            transform: rotate(45deg);
            margin: 0 10px;
        }

        /* Body */
        .body {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 50px;
        }

        .intro {
            font-size: 13pt;
            color: #333;
            margin-bottom: 15px;
        }

        .name {
            font-size: 20pt;
            font-weight: 700;
            color: #1a1a2e;
            margin: 15px 0 20px;
            font-family: 'Brush Script MT', cursive, 'DejaVu Sans';
            font-style: italic;
        }

        .description {
            font-size: 10pt;
            color: #444;
            line-height: 1.7;
            margin-bottom: 20px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .score {
            font-size: 15pt;
            font-weight: 700;
            color: #d4af37;
            margin: 15px 0;
        }

        /* Golden Seal with Ribbon */
        .seal-wrapper {
            position: absolute;
            bottom: 75px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 110px;
            z-index: 10;
        }

        .seal-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: radial-gradient(circle, #ffd700 0%, #f1c40f 40%, #d4af37 100%);
            border: 4px solid #b8941e;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
        }

        .seal-inner {
            position: absolute;
            width: 72px;
            height: 72px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 50%;
        }

        .seal-star {
            font-size: 38pt;
            color: #fff;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .ribbon {
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
        }

        .ribbon-left {
            position: absolute;
            left: -20px;
            width: 18px;
            height: 35px;
            background: #c0392b;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 50% 100%, 0 80%);
        }

        .ribbon-right {
            position: absolute;
            right: -20px;
            width: 18px;
            height: 35px;
            background: #a93226;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 50% 100%, 0 80%);
        }

        /* Footer */
        .footer {
            display: table;
            width: 100%;
            table-layout: fixed;
            padding-top: 35px;
        }

        .footer-col {
            display: table-cell;
            vertical-align: bottom;
            text-align: center;
            padding: 0 15px;
            margin-top: 70px;
        }

        .sig-line {
            width: 170px;
            height: 1.5px;
            background: #333;
            margin: 0 auto 15px;
        }

        .sig-name {
            font-size: 10pt;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 7px;
        }

        .sig-title {
            font-size: 3pt;
            font-style: italic;
            color: #666;
        }

        .date-label {
            font-size: 10pt;
            color: #666;
            margin-bottom: 12px;
        }

        .date-value {
            font-size: 10pt;
            font-weight: 600;
            color: #1a1a2e;
        }
    </style>
</head>
<body>
    <!-- Background Pattern -->
    <div class="background">
        <!-- Geometric Shapes -->
        <div class="shape-tl">
            <div class="box1"></div>
            <div class="box2"></div>
        </div>
        <div class="shape-tr">
            <div class="box1"></div>
            <div class="box2"></div>
        </div>
        <div class="shape-bl">
            <div class="box1"></div>
            <div class="box2"></div>
        </div>
        <div class="shape-br">
            <div class="box1"></div>
            <div class="box2"></div>
        </div>

        <!-- Decorative Dots -->
        <div class="dots">
            <div class="dot" style="top: 18%; left: 14%;"></div>
            <div class="dot" style="top: 22%; left: 12%;"></div>
            <div class="dot" style="top: 20%; left: 17%;"></div>
            <div class="dot" style="top: 25%; left: 10%;"></div>
            <div class="dot" style="top: 18%; right: 14%;"></div>
            <div class="dot" style="top: 22%; right: 12%;"></div>
            <div class="dot" style="top: 20%; right: 17%;"></div>
            <div class="dot" style="top: 25%; right: 10%;"></div>
            <div class="dot" style="bottom: 18%; left: 14%;"></div>
            <div class="dot" style="bottom: 22%; left: 12%;"></div>
            <div class="dot" style="bottom: 20%; left: 17%;"></div>
            <div class="dot" style="bottom: 25%; left: 10%;"></div>
            <div class="dot" style="bottom: 18%; right: 14%;"></div>
            <div class="dot" style="bottom: 22%; right: 12%;"></div>
            <div class="dot" style="bottom: 20%; right: 17%;"></div>
            <div class="dot" style="bottom: 25%; right: 10%;"></div>
        </div>

        <!-- Decorative Leaves -->
        <div class="leaf" style="top: 25%; left: 8%;">
            <svg viewBox="0 0 100 100"><path d="M50,10 Q70,30 60,50 Q70,70 50,90 Q30,70 40,50 Q30,30 50,10 Z"/></svg>
        </div>
        <div class="leaf" style="top: 25%; right: 8%;">
            <svg viewBox="0 0 100 100"><path d="M50,10 Q70,30 60,50 Q70,70 50,90 Q30,70 40,50 Q30,30 50,10 Z"/></svg>
        </div>
        <div class="leaf" style="bottom: 25%; left: 8%;">
            <svg viewBox="0 0 100 100"><path d="M50,10 Q70,30 60,50 Q70,70 50,90 Q30,70 40,50 Q30,30 50,10 Z"/></svg>
        </div>
        <div class="leaf" style="bottom: 25%; right: 8%;">
            <svg viewBox="0 0 100 100"><path d="M50,10 Q70,30 60,50 Q70,70 50,90 Q30,70 40,50 Q30,30 50,10 Z"/></svg>
        </div>
    </div>

    <!-- Certificate Container -->
    <div class="certificate">
        <!-- Watermark Panasonic -->
        <div class="watermark"></div>

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
                <div class="divider-container">
                    <div class="divider-line"></div>
                    <div class="divider-diamond"></div>
                    <div class="divider-line"></div>
                </div>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="intro">Penghargaan ini diberikan kepada:</p>
                <div class="name">WAAJAH</div>
                <p class="description">
                    Telah mengikuti ujian post test BIND yang diselenggarakan 
                    pada tanggal 11-11-2025 dengan pencapaian standar nilai yang ditentukan
                    dan memperoleh nilai akhir:
                </p>
                <div class="score">{{ number_format(100, 0) }}</div>
            </div>

            <!-- Golden Seal -->
            <div class="seal-wrapper">
                <div class="seal-circle">
                    <div class="seal-inner"></div>
                    <div class="seal-star">★</div>
                </div>
                <div class="ribbon">
                    <div class="ribbon-left"></div>
                    <div class="ribbon-right"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-col">
                    <div class="sig-line"></div>
                    <div class="sig-name">Samira Hadid</div>
                    <div class="sig-title">Mentor Penulisan</div>
                </div>
                <div class="footer-col">
                    <div class="date-label">Tanggal</div>
                    <div class="date-value">11-11-2025</div>
                </div>
                <div class="footer-col">
                    <div class="sig-line"></div>
                    <div class="sig-name">Ketut Susilo</div>
                    <div class="sig-title">Ketua Organisasi</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>