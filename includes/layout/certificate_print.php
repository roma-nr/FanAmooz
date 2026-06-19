<?php
/** @var array<string, mixed> $cert */
$issueDate = $cert['reviewed_at'] ? format_date($cert['reviewed_at']) : format_date($cert['requested_at']);
$presidentName = setting('president_name', 'دکتر سید محمد حسینی');
$certNumber = $cert['certificate_number'] ?? '---';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گواهی پایان دوره | <?= e($cert['student_name']) ?></title>
    <!-- فونت‌های زیبا و سنتی -->
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            background: #d4c9b0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .cert-sheet {
            max-width: 900px;
            width: 100%;
            background: #fcf8f0;
            border: 12px double #8b4513;
            border-radius: 8px;
            padding: 2.8rem 2.5rem 2rem;
            position: relative;
            box-shadow: 0 16px 48px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        /* حاشیه داخلی سنتی */
        .cert-sheet::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            border: 2px solid #8b4513;
            border-radius: 4px;
            pointer-events: none;
            opacity: 0.3;
        }

        /* واترمارک */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }
        .cert-watermark img { max-width: 300px; max-height: 300px; object-fit: contain; }

        /* هدر: لوگو چپ، بسم‌الله وسط، پرچم راست */
        .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.3rem;
            position: relative;
            z-index: 1;
        }
        .cert-logo {
            max-height: 80px;
            max-width: 140px;
            object-fit: contain;
        }
        .cert-flag {
            max-height: 150px;
            max-width: 180px;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.12));
        }
        .cert-bismillah {
            font-family: 'Noto Nastaliq Urdu', 'Vazirmatn', serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: #2c1810;
            text-align: center;
            line-height: 2.3;
            letter-spacing: 2px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.05);
            background: linear-gradient(180deg, #2c1810 0%, #5a3a2a 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
			margin-right:70px
        }

        .cert-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c1810;
            margin: 0.2rem 0 1rem;
            position: relative;
            z-index: 1;
            letter-spacing: 3px;
            border-bottom: 3px double #8b4513;
            border-top: 3px double #8b4513;
            padding: 0.4rem 0;
            display: inline-block;
            width: auto;
            margin-left: auto;
            margin-right: auto;
            width: fit-content;
        }
        .cert-title-wrap { text-align: center; }

        /* متن اصلی */
        .cert-body {
            position: relative;
            z-index: 1;
            text-align: justify;
            line-height: 2.6;
            font-size: 1.1rem;
            padding: 0.5rem 0.8rem;
            color: #2c1810;
            font-weight: 500;
        }
        .cert-body .highlight {
            font-weight: 700;
            color: #6b2e0a;
            font-size: 1.15rem;
        }
        .cert-body .grade {
            font-weight: 700;
            color: #1a5e2a;
        }
        .cert-body .cert-number {
            font-weight: 700;
            color: #8b4513;
            font-family: monospace;
            font-size: 1.05rem;
        }

        /* فوتر */
        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 2rem;
            position: relative;
            z-index: 1;
            padding-top: 1.2rem;
            border-top: 3px double #8b4513;
        }
        .cert-signature {
            text-align: center;
        }
        .cert-signature .sig-line {
            width: 160px;
            height: 2px;
            background: #2c1810;
            margin: 0.5rem auto;
        }
        .cert-signature .sig-name {
            font-weight: 700;
            font-size: 1.2rem;
            color: #2c1810;
            font-family: 'Noto Nastaliq Urdu', 'Vazirmatn', serif;
        }
        .cert-signature .sig-title {
            font-size: 0.85rem;
            color: #5a3a2a;
            font-weight: 600;
        }
        .cert-signature .sig-stamp {
            font-size: 0.75rem;
            color: #888;
            margin-top: 0.3rem;
        }
        .cert-meta {
            text-align: left;
            direction: ltr;
            font-size: 0.8rem;
            color: #5a3a2a;
            line-height: 1.8;
            font-weight: 500;
        }
        .cert-meta strong { color: #2c1810; }

        /* دکمه چاپ در پایین */
        .no-print {
            text-align: center;
            margin-top: 2rem;
            position: relative;
            z-index: 1;
        }
        .btn-print {
            padding: 0.7rem 2.5rem;
            background: #6b2e0a;
            color: #fcf8f0;
            border: 2px solid #4a1e06;
            border-radius: 50px;
            cursor: pointer;
            font-family: 'Vazirmatn', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-print:hover {
            background: #8b4513;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(107,46,10,0.3);
        }
        .btn-close-print {
            padding: 0.7rem 1.5rem;
            margin-right: 1rem;
            border: 2px solid #8b4513;
            border-radius: 50px;
            cursor: pointer;
            font-family: 'Vazirmatn', sans-serif;
            font-size: 1rem;
            background: transparent;
            color: #6b2e0a;
            transition: all 0.3s ease;
        }
        .btn-close-print:hover {
            background: #f0e8d8;
        }

        @media print {
            body { background: #fcf8f0; padding: 0.3rem; }
            .no-print { display: none !important; }
            .cert-sheet { box-shadow: none; border-width: 8px; padding: 1.8rem; }
            .cert-body { font-size: 0.95rem; line-height: 2.2; }
            .cert-bismillah { font-size: 1.8rem; }
            .cert-title { font-size: 1.4rem; }
            .cert-watermark img { max-width: 200px; }
            .cert-signature .sig-line { width: 120px; }
        }
        @media (max-width: 640px) {
            .cert-header { flex-direction: column; gap: 0.5rem; }
            .cert-sheet { padding: 1.5rem 1rem; }
            .cert-bismillah { font-size: 1.6rem; }
            .cert-body { font-size: 0.9rem; line-height: 2; }
            .cert-footer { flex-direction: column; align-items: center; gap: 1rem; }
            .cert-meta { text-align: center; }
            .cert-logo { max-height: 55px; }
            .cert-flag { max-height: 60px; }
        }
    </style>
</head>
<body>

<div class="cert-sheet">

    <!-- واترمارک -->
    <div class="cert-watermark">
        <img src="<?= e(logo_url(2)) ?>" alt="لوگو دانشگاه">
    </div>

    <!-- هدر -->
    <div class="cert-header">
        <img src="<?= e(logo_url(2)) ?>" alt="لوگو" class="cert-logo">
        <div class="cert-bismillah">به نام خداوند جان و خرد</div>
        <img src="<?= e(asset_url('images/flag1.png')) ?>" alt="پرچم ایران" class="cert-flag">
    </div>

    <!-- عنوان -->
    <div class="cert-title-wrap">
        <div class="cert-title">گواهی پایان دوره آموزشی</div>
    </div>

    <!-- متن اصلی -->
    <div class="cert-body">
        <p>
            گواهی میشود <span class="highlight">آقا / خانم <?= e($cert['student_name']) ?></span>
            با کد ملی <span class="highlight"><?= e($cert['national_id'] ?? '---') ?></span>
            در دوره آموزشی با عنوان <span class="highlight">«<?= e($cert['course_title']) ?>»</span>
            که بر اساس مجوز شماره <span class="cert-number"><?= e($certNumber) ?></span>
            مورخ <span class="highlight"><?= e($issueDate) ?></span>
            دانشگاه ملی مهارت، به مدت <span class="highlight"><?= e($cert['duration_hours'] ?? '---') ?></span> ساعت
            در مؤسسه فن‌آموز به صورت الکترونیکی برگزار شده است،
            شرکت کرده و این دوره را با موفقیت به پایان رسانده است.
        </p>
        <p style="margin-top:0.5rem;text-align:center;font-size:0.95rem;color:#4a2a1a;">
            نمره کسب شده: <span class="grade"><?= e($cert['final_grade']) ?></span> از ۱۰۰
            
        </p>
    </div>

    <!-- فوتر -->
    <div class="cert-footer">
        <div class="cert-signature">
            <div class="sig-name"><?= e($presidentName) ?></div>
            <div class="sig-line"></div>
            <div class="sig-title">رئیس دانشگاه ملی مهارت</div>
            <div class="sig-stamp">امضاء و مهر</div>
        </div>
        <div class="cert-meta">
            <div>شماره گواهی: <strong><?= e($certNumber) ?></strong></div>
            <div>تاریخ صدور: <strong><?= e($issueDate) ?></strong></div>
        </div>
    </div>

</div>
<br><br>
<!-- دکمه چاپ در پایین -->
<div class="no-print">
    <button type="button" onclick="window.print()" class="btn-print">🖨️ چاپ گواهی</button>
    <button type="button" onclick="window.close()" class="btn-close-print">✕ بستن</button>
</div>

</body>
</html>