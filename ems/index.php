<?php
include("includes/db.php");

function countValue($conn, $sql) {
    $r = mysqli_query($conn, $sql);
    if (!$r) return 0;
    $row = mysqli_fetch_assoc($r);
    return (int)($row['total'] ?? 0);
}

$total_events        = countValue($conn, "SELECT COUNT(*) AS total FROM events");
$total_students       = countValue($conn, "SELECT COUNT(*) AS total FROM users");
$total_registrations  = countValue($conn, "SELECT COUNT(*) AS total FROM registrations");
$total_attendance     = countValue($conn, "SELECT COUNT(*) AS total FROM attendance WHERE attendance_status='Present'");

$events = mysqli_query($conn, "SELECT * FROM events WHERE event_date IS NOT NULL AND event_date!='0000-00-00' ORDER BY event_date ASC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>JPP Event Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ===== Root variables & base reset ===== */
        :root {
            --maroon: #800020;
            --deep: #1b000a;
            --wine: #43000f;
            --gold: #d4af37;
            --gold2: #e0c476;
            --cream: #fbf7ef;
            --ink: #271d20;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            background: #f8f5f0;
            overflow-x: hidden;
        }
        body:before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image:
                linear-gradient(rgba(100,0,25,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(100,0,25,.025) 1px, transparent 1px);
            background-size: 55px 55px;
            mask-image: linear-gradient(to bottom, black, transparent 85%);
        }

        /* ===== Navbar ===== */
        .navbar {
            background: rgba(27,0,10,.84);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(212,175,55,.2);
            padding: 13px 0;
            position: fixed;
            z-index: 1000;
        }
        .navbar-brand {
            font-weight: 800;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 11px;
        }
        .navbar-brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            padding: 4px;
            box-shadow: 0 0 0 2px rgba(212,175,55,.45);
        }
        .nav-link {
            color: #fff !important;
            font-weight: 600;
            margin: 0 9px;
            opacity: .9;
        }
        .nav-link:hover { color: var(--gold2) !important; }

        .social-icons { display: flex; gap: 6px; margin: 0 12px; }
        .social-icons a {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            transition: .3s;
        }
        .social-icons a:hover { transform: translateY(-3px) scale(1.08); }
        .social-facebook  { background: #1877f2; }
        .social-instagram { background: #e4405f; }
        .social-tiktok    { background: #000; }
        .social-youtube   { background: #f00; }
        .social-whatsapp  { background: #20c76a; }
        .social-email     { background: #8d001f; }

        .btn-login {
            background: linear-gradient(135deg, var(--gold), var(--gold2));
            color: #520015;
            border: 0;
            border-radius: 30px;
            padding: 10px 23px;
            font-weight: 800;
        }
        .btn-login:hover { color: #520015; transform: translateY(-2px); }

        .btn-register {
            border: 1px solid rgba(255,255,255,.7);
            color: #fff;
            padding: 9px 22px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-register:hover { background: #fff; color: var(--maroon); }

        /* ===== Hero section ===== */
        .hero {
            min-height: 820px;
            padding: 150px 0 100px;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 75% 30%, rgba(212,175,55,.12), transparent 20%),
                radial-gradient(circle at 20% 90%, rgba(212,175,55,.08), transparent 25%),
                linear-gradient(135deg, #120006, #43000f 46%, #70001e 100%);
            color: #fff;
            isolation: isolate;
        }
        .hero:before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: .7;
        }
        .hero:after {
            content: "";
            position: absolute;
            width: 700px;
            height: 700px;
            border: 1px solid rgba(212,175,55,.14);
            border-radius: 50%;
            right: -280px;
            top: 60px;
            box-shadow: 0 0 0 50px rgba(212,175,55,.025), 0 0 0 100px rgba(212,175,55,.02);
            animation: spinSlow 30s linear infinite;
        }
        .hero-content { position: relative; z-index: 3; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--gold2);
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 18px;
        }
        .eyebrow::before {
            content: "";
            display: inline-block;
            width: 30px; /* atau sebarang value */
            height: 2px;
            background: ...;
            margin-right: 10px;
        
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(48px, 6vw, 84px);
            line-height: 1.02;
            margin: 0 0 22px;
            max-width: 750px;
        }

        .hero h1 span { color: var(--gold2); }
        .hero p {
            font-size: 18px;
            line-height: 1.8;
            color: rgba(255,255,255,.78);
            max-width: 690px;
        }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 30px; }

        .hero-gold {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--gold);
            color: #4b0012;
            text-decoration: none;
            font-weight: 800;
            padding: 14px 25px;
            border-radius: 13px;
            box-shadow: 0 15px 35px rgba(212,175,55,.2);
            transition: .3s;
        }
        .hero-gold:hover {
            color: #4b0012;
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(212,175,55,.28);
        }

        .hero-outline {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,.5);
            padding: 13px 24px;
            border-radius: 13px;
            font-weight: 700;
        }
        .hero-outline:hover { background: #fff; color: var(--maroon); }

        .hero-visual {
            position: relative;
            z-index: 2;
            display: grid;
            place-items: center;
            min-height: 480px;
        }
        .orb {
            width: min(430px, 75vw);
            aspect-ratio: 1;
            border-radius: 50%;
            background:
                radial-gradient(circle at 38% 30%, rgba(255,255,255,.22), transparent 13%),
                radial-gradient(circle, #8d0030 0, #650019 42%, #27000c 75%);
            box-shadow: 0 0 90px rgba(212,175,55,.11), inset 0 0 80px rgba(0,0,0,.45);
            border: 1px solid rgba(212,175,55,.4);
            display: grid;
            place-items: center;
            position: relative;
            animation: orbFloat 8s ease-in-out infinite;
        }
        .orb:before, .orb:after {
            content: "";
            position: absolute;
            border: 1px solid rgba(212,175,55,.26);
            border-radius: 50%;
            inset: -24px;
            animation: orbit 26s linear infinite;
        }
        .orb:after {
            inset: -55px;
            border-color: rgba(255,255,255,.08);
            animation-direction: reverse;
            animation-duration: 38s;
        }
        .orb img {
            width: 70%;
            max-width: 330px;
            filter: drop-shadow(0 20px 35px rgba(0,0,0,.35));
            animation: logoPulse 6s ease-in-out infinite;
        }

        /* ===== Generic section styling ===== */
        .section { padding: 95px 0; position: relative; z-index: 1; }
        .section-dark {
            background: linear-gradient(145deg, #180007, #520014);
            color: #fff;
            overflow: hidden;
        }
        .section-head { text-align: center; margin-bottom: 42px; }
        .kicker { color: #9b7a1d; letter-spacing: 2px; text-transform: uppercase; font-size: 12px; font-weight: 800; }
        .section-dark .kicker { color: var(--gold2); }
        .title {
            font-family: 'Playfair Display', serif;
            color: var(--maroon);
            font-size: clamp(34px, 4vw, 48px);
            margin: 8px 0;
        }
        .section-dark .title { color: #fff; }
        .subtitle { color: #756c6e; max-width: 700px; margin: 0 auto; }
        .section-dark .subtitle { color: rgba(255,255,255,.68); }

        /* ===== About slider ===== */
        .about-shell {
            background: #fff;
            border-radius: 28px;
            padding: 12px;
            box-shadow: 0 25px 60px rgba(55,0,15,.09);
            border: 1px solid #eee4d8;
        }
        .about-slider { position: relative; overflow: hidden; border-radius: 20px; }
        .about-slider-track {
            display: flex;
            gap: 14px;
            transition: transform .7s cubic-bezier(.2,.7,.2,1);
        }
        .about-slide {
            flex: 0 0 320px;
            position: relative;
            height: 290px;
            overflow: hidden;
            border-radius: 18px;
            background: #30000d;
        }
        .about-slide img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s; }
        .about-slide:hover img { transform: scale(1.06); }
        .about-slide:after {
            content: "";
            position: absolute;
            inset: 45% 0 0;
            background: linear-gradient(transparent, rgba(18,0,6,.95));
        }
        .about-slide-text { position: absolute; left: 18px; right: 18px; bottom: 17px; z-index: 2; color: #fff; }
        .about-slide-text h4 { font-family: 'Playfair Display', serif; font-size: 18px; margin: 0; }

        .about-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
            width: 45px;
            height: 45px;
            border: 1px solid #ddd2c4;
            border-radius: 50%;
            background: rgba(255,255,255,.94);
            color: var(--maroon);
            box-shadow: 0 8px 20px rgba(0,0,0,.15);
        }
        .about-arrow:hover { background: var(--maroon); color: #fff; }
        .about-prev { left: 15px; }
        .about-next { right: 15px; }

        /* ===== Feature spotlight ===== */
        .feature-stage {
            position: relative;
            min-height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .feature-card {
            width: min(760px, 92%);
            background: linear-gradient(145deg, rgba(255,255,255,.08), rgba(255,255,255,.035));
            border: 1px solid rgba(212,175,55,.2);
            border-radius: 28px;
            padding: 42px;
            text-align: center;
            backdrop-filter: blur(15px);
            box-shadow: 0 25px 70px rgba(0,0,0,.22);
            transition: .9s cubic-bezier(.22,.61,.36,1);
            position: absolute;
            opacity: 0;
            transform: translateX(80px) scale(.94);
            pointer-events: none;
        }
        .feature-card.active { opacity: 1; transform: translateX(0) scale(1); pointer-events: auto; }
        .feature-card i { font-size: 48px; color: var(--gold2); margin-bottom: 18px; }
        .feature-card h3 { font-family: 'Playfair Display', serif; font-size: 30px; margin-bottom: 10px; }
        .feature-card p { color: rgba(255,255,255,.7); margin: 0 auto; max-width: 560px; line-height: 1.7; }

        .feature-dots { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
        .feature-dot {
            width: 34px;
            height: 4px;
            border: 0;
            border-radius: 5px;
            background: rgba(255,255,255,.22);
            transition: .3s;
        }
        .feature-dot.active { background: var(--gold); }

        /* ===== Stats ===== */
        .stats-wrap {
            background: linear-gradient(145deg, #650019, #30000c);
            border: 1px solid rgba(212,175,55,.18);
            border-radius: 30px;
            padding: 14px;
            box-shadow: 0 25px 70px rgba(44,0,12,.2);
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: rgba(255,255,255,.1);
            border-radius: 22px;
            overflow: hidden;
        }
        .stat-card {
            padding: 34px 20px;
            text-align: center;
            background: rgba(255,255,255,.035);
            position: relative;
            overflow: hidden;
        }
        .stat-card:after {
            content: "";
            position: absolute;
            width: 90px;
            height: 90px;
            border: 1px solid rgba(212,175,55,.13);
            border-radius: 50%;
            right: -35px;
            top: -35px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            margin: 0 auto 13px;
            background: rgba(212,175,55,.12);
            color: var(--gold2);
            display: grid;
            place-items: center;
        }
        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 44px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .stat-label { color: rgba(255,255,255,.65); font-size: 13px; margin-top: 7px; }

        /* ===== Event cards ===== */
        .event-card {
            height: 100%;
            background: #fff;
            border: 1px solid #eee4d8;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(55,0,15,.07);
            transition: .35s;
        }
        .event-card:hover { transform: translateY(-7px); box-shadow: 0 25px 55px rgba(55,0,15,.13); }
        .event-poster {
            height: 245px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #650019, #2b000b);
        }
        .event-poster img { width: 100%; height: 100%; object-fit: cover; transition: .5s; }
        .event-card:hover .event-poster img { transform: scale(1.05); }
        .event-date-badge {
            position: absolute;
            left: 14px;
            top: 14px;
            background: rgba(255,255,255,.94);
            border-radius: 12px;
            padding: 8px 11px;
            color: var(--maroon);
            font-weight: 800;
            font-size: 12px;
        }
        .event-body { padding: 20px; }
        .event-body h5 { font-family: 'Playfair Display', serif; color: var(--maroon); font-size: 21px; }
        .event-info { color: #73696c; font-size: 13px; margin: 7px 0; }
        .event-btn {
            display: block;
            text-align: center;
            text-decoration: none;
            background: #650019;
            color: #fff;
            border-radius: 12px;
            padding: 10px;
            margin-top: 15px;
            font-weight: 700;
        }
        .event-btn:hover { background: #8b0a2d; color: #fff; }

        /* ===== CTA & footer ===== */
        .cta {
            background: linear-gradient(135deg, #2a000b, #70001f);
            border-radius: 30px;
            padding: 55px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212,175,55,.18);
        }
        .cta:after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            border: 1px solid rgba(212,175,55,.2);
            border-radius: 50%;
            right: -100px;
            top: -120px;
        }
        .cta h2 { font-family: 'Playfair Display', serif; font-size: 38px; }
        .cta p { color: rgba(255,255,255,.7); }

        footer {
            background: #160006;
            color: #fff;
            padding: 55px 0 25px;
            border-top: 1px solid rgba(212,175,55,.18);
        }
        footer h4, footer h5 { font-family: 'Playfair Display', serif; }
        footer p, footer a { color: rgba(255,255,255,.65) !important; }

        /* ===== Chat widget ===== */
        .miuassist-toggle {
            position: fixed;
            right: 25px;
            bottom: 25px;
            z-index: 2000;
            width: 62px;
            height: 62px;
            border: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff7baa, #ffb3cf);
            box-shadow: 0 12px 35px rgba(90,0,30,.3);
            font-size: 27px;
        }
        .miuassist-toggle:hover { transform: scale(1.08); }
        .miuassist-framewrap {
            position: fixed;
            right: 25px;
            bottom: 100px;
            width: 370px;
            height: 520px;
            z-index: 1999;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 70px rgba(0,0,0,.28);
            overflow: hidden;
            display: none;
        }
        .miuassist-framewrap.active { display: block; }
        .miuassist-iframe { width: 100%; height: 100%; border: 0; }

        /* ===== Keyframes ===== */
        @keyframes orbFloat  { 50% { transform: translateY(-11px); } }
        @keyframes logoPulse { 50% { transform: scale(1.018); } }
        @keyframes orbit     { to { transform: rotate(360deg); } }
        @keyframes spinSlow  { to { transform: rotate(360deg); } }

        /* ===== Responsive ===== */
        @media (max-width: 991px) {
            .navbar .social-icons { display: none; }
            .hero { min-height: auto; padding-top: 135px; }
            .hero-visual { min-height: 400px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .section { padding: 70px 0; }
            .about-slide { flex-basis: 270px; height: 260px; }
            .feature-card { padding: 30px 20px; }
            .feature-card h3 { font-size: 25px; }
            .stat-number { font-size: 36px; }
        }
        @media (max-width: 576px) {
            .hero { padding-top: 120px; }
            .hero h1 { font-size: 46px; }
            .hero p { font-size: 16px; }
            .hero-visual { min-height: 330px; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .miuassist-framewrap { right: 10px; left: 10px; width: auto; }
            .miuassist-toggle { right: 15px; bottom: 15px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *:before, *:after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

<!-- ===== Navbar ===== -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <img src="assets/images/logo.png" alt="JPP Logo">
            <span>JPP EMS</span>
        </a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
                <li class="nav-item">
                    <div class="social-icons">
                        <a href="https://www.facebook.com/mpp.puo.7?" target="_blank" class="social-facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/jpppuoofficial" target="_blank" class="social-instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@jpppuoofficial?_r" target="_blank" class="social-tiktok"><i class="fab fa-tiktok"></i></a>
                        <a href="https://www.youtube.com/@jpppuo" target="_blank" class="social-youtube"><i class="fab fa-youtube"></i></a>
                        <a href="https://whatsapp.com/channel/0029Vb7b0GR0AgWL5q96G52Y" target="_blank" class="social-whatsapp"><i class="fab fa-whatsapp"></i></a>
                        <a href="mailto:jpppuo30@gmail.com" class="social-email"><i class="fas fa-envelope"></i></a>
                    </div>
                </li>
                <li class="nav-item ms-lg-2"><a href="login.php" class="btn btn-login"><i class="fa fa-right-to-bracket"></i> Login</a></li>
                <li class="nav-item ms-lg-2"><a href="user/register.php" class="btn-register">Register</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===== Hero ===== -->
<section class="hero" id="home">
    <div class="container hero-content">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="eyebrow">Jabatan Perwakilan Pelajar PUO</div>
                <h1>Event Management<br><span>with Purpose </span></h1>
                <p>A refined digital platform for managing student programmes, from registration and QR attendance to event monitoring, survey completion and official documents.</p>
                <div class="hero-actions">
                    <a href="#events" class="hero-gold"><i class="fa fa-calendar-days"></i> Explore Events</a>
                    <a href="login.php" class="hero-outline"><i class="fa fa-right-to-bracket"></i> Student Login</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-visual">
                    <div class="orb"><img src="assets/images/logo.png" alt="JPP Logo"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== About ===== -->
<section class="section" id="about">
    <div class="container">
        <div class="section-head">
            <div class="kicker">About JPP EMS</div>
            <h2 class="title">A Digital Space for Student Programmes</h2>
            <p class="subtitle">Designed to keep programme management organised, clear and accessible for students and administrators.</p>
        </div>
        <div class="about-shell">
            <div class="about-slider">
                <button class="about-arrow about-prev" onclick="aboutPrev()"><i class="fa fa-chevron-left"></i></button>
                <div class="about-slider-track" id="aboutSliderTrack">
                    <?php
                    $about = [
                        ['about1.JPG', 'Pelancaran Bulan Kebangsaan dan Perarakan Merdeka'],
                        ['about2.JPG', 'Kejohanan E-Football'],
                        ['about3.JPG', 'MTS Diploma Sesi 2026/2027'],
                        ['about4.JPG', 'Imarah Ramadhan PTPTN'],
                        ['about5.jpg', 'Majlis Solat Hajat'],
                        ['about6.JPG', 'Malam Kelab Dan Persatuan'],
                    ];
                    foreach ($about as $a): ?>
                        <div class="about-slide">
                            <img src="uploads/<?= htmlspecialchars($a[0]) ?>" alt="<?= htmlspecialchars($a[1]) ?>">
                            <div class="about-slide-text"><h4><?= htmlspecialchars($a[1]) ?></h4></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="about-arrow about-next" onclick="aboutNext()"><i class="fa fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>

<!-- ===== Features ===== -->
<section class="section section-dark" id="features">
    <div class="container">
        <div class="section-head">
            <div class="kicker">System Features</div>
            <h2 class="title">One Platform with four core functions </h2>
            <p class="subtitle">The spotlight moves automatically so the system capabilities are presented dynamically instead of remaining static cards.</p>
        </div>
        <div class="feature-stage" id="featureStage">
            <div class="feature-card active">
                <i class="fa fa-calendar-days"></i>
                <h3>Event Management</h3>
                <p>Create, organise and monitor student programmes with event details, dates, venues and registration information in one structured system.</p>
            </div>
            <div class="feature-card">
                <i class="fa fa-qrcode"></i>
                <h3>QR Attendance</h3>
                <p>Speed up check-in with QR-based attendance and keep participation records connected to each event.</p>
            </div>
            <div class="feature-card">
                <i class="fa fa-users"></i>
                <h3>Participant Management</h3>
                <p>View registered students, their programme information and attendance status from a centralised participant record.</p>
            </div>
            <div class="feature-card">
                <i class="fa fa-chart-column"></i>
                <h3>Event Reports</h3>
                <p>Open a detailed report by event to review registration totals, attendance performance and participant details without printing.</p>
            </div>
        </div>
        <div class="feature-dots">
            <button class="feature-dot active"></button>
            <button class="feature-dot"></button>
            <button class="feature-dot"></button>
            <button class="feature-dot"></button>
        </div>
    </div>
</section>

<!-- ===== Stats ===== -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div class="kicker">System Overview</div>
            <h2 class="title">Live Programme Statistics</h2>
            <p class="subtitle">Key system figures are presented as animated counters when the section enters the screen.</p>
        </div>
        <div class="stats-wrap">
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-calendar-days"></i></div>
                    <div class="stat-number counter" data-target="<?= $total_events ?>">0</div>
                    <div class="stat-label">Total Events</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-user-graduate"></i></div>
                    <div class="stat-number counter" data-target="<?= $total_students ?>">0</div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-user-plus"></i></div>
                    <div class="stat-number counter" data-target="<?= $total_registrations ?>">0</div>
                    <div class="stat-label">Registrations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-user-check"></i></div>
                    <div class="stat-number counter" data-target="<?= $total_attendance ?>">0</div>
                    <div class="stat-label">Present Attendance</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Events ===== -->
<section class="section" id="events" style="padding-top:35px">
    <div class="container">
        <div class="section-head">
            <div class="kicker">Programmes</div>
            <h2 class="title">Upcoming & Recent Events</h2>
            <p class="subtitle">Explore programmes and continue to registration through the student portal.</p>
        </div>
        <div class="row g-4">
            <?php if ($events && mysqli_num_rows($events) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($events)): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="event-card">
                            <div class="event-poster">
                                <div class="event-date-badge">
                                    <i class="fa fa-calendar"></i> <?= date('d M Y', strtotime($row['event_date'])) ?>
                                </div>
                                <?php if (!empty($row['poster'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['poster']) ?>" alt="Event Poster">
                                <?php else: ?>
                                    <div style="height:100%;display:grid;place-items:center;color:#d4af37;font-size:45px">
                                        <i class="fa fa-calendar-days"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="event-body">
                                <h5><?= htmlspecialchars($row['event_title']) ?></h5>
                                <div class="event-info">
                                    <i class="fa fa-clock"></i>
                                    <?= date('h:i A', strtotime($row['start_time'])) ?> – <?= date('h:i A', strtotime($row['end_time'])) ?>
                                </div>
                                <div class="event-info">
                                    <i class="fa fa-location-dot"></i> <?= htmlspecialchars($row['venue']) ?>
                                </div>
                                <a href="login.php" class="event-btn">Login to Register <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12"><div class="text-center py-5 text-muted">No events available at the moment.</div></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="section" style="padding-top:20px">
    <div class="container">
        <div class="cta">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="kicker">Ready to participate?</div>
                    <h2>Register for your next JPP programme.</h2>
                    <p class="mb-0">Create a student account, register for an event and manage your event participation digitally.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="user/register.php" class="hero-gold"><i class="fa fa-user-plus"></i> Create Student Account</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Footer ===== -->
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h4>JPP Event Management System</h4>
                <p class="mt-3">A formal digital platform for student programme registration, attendance, participant management and event reporting.</p>
            </div>
            <div class="col-lg-3">
                <h5>Quick Links</h5>
                <div class="d-grid gap-2 mt-3">
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#features">Features</a>
                    <a href="#events">Events</a>
                </div>
            </div>
            <div class="col-lg-4">
                <h5>Contact</h5>
                <p class="mt-3"><i class="fa fa-envelope me-2"></i> jpppuo30@gmail.com</p>
                <p><i class="fa fa-building me-2"></i> Politeknik Ungku Omar</p>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.1);margin:35px 0 20px">
        <div class="text-center small" style="color:rgba(255,255,255,.45)">
            © <?= date('Y') ?> JPP Event Management System. All Rights Reserved.
        </div>
    </div>
</footer>

<!-- ===== Chat widget ===== -->
<button class="miuassist-toggle" id="miuassistToggle" title="Miu Assist" aria-label="Open Miu Assist chat">💬</button>
<div class="miuassist-framewrap" id="miuassistFramewrap">
    <iframe class="miuassist-iframe" id="miuassistIframe" title="Miu Assist" src="about:blank"></iframe>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Feature spotlight: one feature at a time with automatic motion.
    const featureCards = [...document.querySelectorAll('.feature-card')];
    const featureDots = [...document.querySelectorAll('.feature-dot')];
    let featureIndex = 0;

    function showFeature(i) {
        featureIndex = i;
        featureCards.forEach((c, n) => c.classList.toggle('active', n === i));
        featureDots.forEach((d, n) => d.classList.toggle('active', n === i));
    }
    featureDots.forEach((d, i) => d.addEventListener('click', () => showFeature(i)));
    setInterval(() => showFeature((featureIndex + 1) % featureCards.length), 6000);

    // Animated counters when visible.
    const counters = [...document.querySelectorAll('.counter')];
    let counted = false;
    const statsObserver = new IntersectionObserver(entries => {
        if (!entries.some(e => e.isIntersecting) || counted) return;
        counted = true;
        counters.forEach(el => {
            const target = Number(el.dataset.target) || 0;
            const duration = 1300;
            const start = performance.now();
            function tick(now) {
                const p = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.floor(target * eased).toLocaleString();
                if (p < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });
    }, { threshold: .35 });
    const statSection = document.querySelector('.stats-wrap');
    if (statSection) statsObserver.observe(statSection);

    // About carousel.
    let aboutIndex = 0;
    const aboutTrack = document.getElementById('aboutSliderTrack');
    const aboutSlides = [...document.querySelectorAll('.about-slide')];

    function aboutVisible() {
        return window.innerWidth < 700 ? 1 : window.innerWidth < 1100 ? 2 : 3;
    }
    function aboutMove() {
        const gap = 14;
        const width = aboutSlides[0]?.getBoundingClientRect().width || 320;
        aboutTrack.style.transform = `translateX(-${aboutIndex * (width + gap)}px)`;
    }
    function aboutNext() {
        const max = Math.max(aboutSlides.length - aboutVisible(), 0);
        aboutIndex = aboutIndex < max ? aboutIndex + 1 : 0;
        aboutMove();
    }
    function aboutPrev() {
        const max = Math.max(aboutSlides.length - aboutVisible(), 0);
        aboutIndex = aboutIndex > 0 ? aboutIndex - 1 : max;
        aboutMove();
    }
    let aboutTimer = setInterval(aboutNext, 3200);
    document.querySelector('.about-slider')?.addEventListener('mouseenter', () => clearInterval(aboutTimer));
    document.querySelector('.about-slider')?.addEventListener('mouseleave', () => aboutTimer = setInterval(aboutNext, 3200));
    window.addEventListener('resize', aboutMove);

    // Chat iframe loads only when opened.
    const toggle = document.getElementById('miuassistToggle');
    const wrap = document.getElementById('miuassistFramewrap');
    const frame = document.getElementById('miuassistIframe');
    let loaded = false;
    toggle.addEventListener('click', () => {
        if (!loaded) {
            frame.src = 'miu_assist_frame.html';
            loaded = true;
        }
        wrap.classList.toggle('active');
    });
</script>
</body>
</html>