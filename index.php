<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova Academy – Homepage</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --navy: #0a0f2e;
      --gold: #c9a84c;
      --gold-light: #e8c97e;
      --cream: #f8f4ec;
      --white: #ffffff;
      --glass: rgba(255,255,255,0.07);
      --glass-border: rgba(201,168,76,0.25);
    }

    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--navy);
      color: var(--white);
      overflow-x: hidden;
    }

    /* ── STARFIELD CANVAS ── */
    #stars {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
    }

    /* ── NAVBAR ── */
    nav {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1.2rem 5%;
      background: rgba(10,15,46,0.75);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--glass-border);
      animation: slideDown 0.8s ease both;
    }

    @keyframes slideDown {
      from { transform: translateY(-60px); opacity: 0; }
      to   { transform: translateY(0);     opacity: 1; }
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }

    .logo-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      border-radius: 8px;
      display: grid;
      place-items: center;
      font-size: 1.2rem;
    }

    .logo-text {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .logo-text span { color: var(--gold); }

    .nav-links {
      display: flex;
      gap: 2.2rem;
      list-style: none;
    }

    .nav-links a {
      color: rgba(255,255,255,0.75);
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 500;
      letter-spacing: 0.5px;
      transition: color 0.3s;
      position: relative;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -4px; left: 0; right: 0;
      height: 1px;
      background: var(--gold);
      transform: scaleX(0);
      transition: transform 0.3s;
    }

    .nav-links a:hover { color: var(--gold); }
    .nav-links a:hover::after { transform: scaleX(1); }

    .nav-btn {
      padding: 0.55rem 1.4rem;
      border: 1.5px solid var(--gold);
      background: transparent;
      color: var(--gold);
      border-radius: 4px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      letter-spacing: 0.5px;
    }

    .nav-btn:hover {
      background: var(--gold);
      color: var(--navy);
    }

    /* ── HERO ── */
    .hero {
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 0 5%;
      overflow: hidden;
    }

    /* Diagonal golden beam */
    .hero::before {
      content: '';
      position: absolute;
      top: -20%; left: 30%;
      width: 2px; height: 200%;
      background: linear-gradient(180deg, transparent, rgba(201,168,76,0.15), transparent);
      transform: rotate(25deg);
      animation: beamMove 6s ease-in-out infinite alternate;
    }

    @keyframes beamMove {
      from { left: 25%; }
      to   { left: 55%; }
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.4rem 1rem;
      border: 1px solid var(--glass-border);
      border-radius: 50px;
      background: var(--glass);
      font-size: 0.78rem;
      color: var(--gold-light);
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 1.8rem;
      animation: fadeUp 0.9s 0.2s ease both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(3rem, 7vw, 6.5rem);
      font-weight: 700;
      line-height: 1.05;
      max-width: 850px;
      animation: fadeUp 0.9s 0.4s ease both;
    }

    .hero h1 em {
      font-style: italic;
      color: var(--gold);
    }

    .hero p {
      max-width: 560px;
      margin: 1.8rem auto 0;
      font-size: 1.05rem;
      color: rgba(255,255,255,0.6);
      line-height: 1.75;
      animation: fadeUp 0.9s 0.6s ease both;
    }

    .hero-cta {
      display: flex;
      gap: 1.2rem;
      margin-top: 2.8rem;
      flex-wrap: wrap;
      justify-content: center;
      animation: fadeUp 0.9s 0.8s ease both;
    }

    .btn-primary {
      padding: 0.9rem 2.4rem;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: var(--navy);
      border: none;
      border-radius: 4px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      letter-spacing: 0.5px;
      box-shadow: 0 0 40px rgba(201,168,76,0.35);
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
      position: relative;
      overflow: hidden;
    }

    .btn-primary::after {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0.2);
      transform: translateX(-100%);
      transition: transform 0.4s;
    }

    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 60px rgba(201,168,76,0.5); }
    .btn-primary:hover::after { transform: translateX(100%); }

    .btn-outline {
      padding: 0.9rem 2.4rem;
      background: transparent;
      color: var(--white);
      border: 1.5px solid rgba(255,255,255,0.25);
      border-radius: 4px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-outline:hover { border-color: var(--gold); color: var(--gold); }

    /* Stats bar */
    .stats-bar {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: center;
      gap: 0;
      margin: 0 5% 4rem;
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      background: var(--glass);
      backdrop-filter: blur(10px);
      overflow: hidden;
      animation: fadeUp 0.9s 1s ease both;
    }

    .stat {
      flex: 1;
      padding: 1.8rem 2rem;
      text-align: center;
      border-right: 1px solid var(--glass-border);
    }
    .stat:last-child { border-right: none; }

    .stat-num {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.4rem;
      font-weight: 700;
      color: var(--gold);
      line-height: 1;
    }

    .stat-label {
      font-size: 0.78rem;
      color: rgba(255,255,255,0.5);
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-top: 0.4rem;
    }

    /* ── PROGRAMS SECTION ── */
    .section {
      position: relative;
      z-index: 1;
      padding: 5rem 5%;
    }

    .section-label {
      font-size: 0.75rem;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 0.8rem;
    }

    .section-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2rem, 4vw, 3.2rem);
      font-weight: 700;
      max-width: 480px;
      line-height: 1.15;
    }

    .programs-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      margin-top: 3rem;
    }

    .program-card {
      padding: 2rem;
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      background: var(--glass);
      backdrop-filter: blur(8px);
      transition: all 0.4s;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .program-card::before {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 2px;
      background: linear-gradient(90deg, var(--gold), var(--gold-light));
      transform: scaleX(0);
      transition: transform 0.4s;
      transform-origin: left;
    }

    .program-card:hover { transform: translateY(-6px); border-color: var(--gold-light); }
    .program-card:hover::before { transform: scaleX(1); }

    .card-icon {
      width: 48px; height: 48px;
      background: rgba(201,168,76,0.12);
      border-radius: 10px;
      display: grid;
      place-items: center;
      font-size: 1.4rem;
      margin-bottom: 1.2rem;
      border: 1px solid rgba(201,168,76,0.2);
    }

    .card-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 0.6rem;
    }

    .card-desc {
      font-size: 0.85rem;
      color: rgba(255,255,255,0.5);
      line-height: 1.65;
    }

    .card-link {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      margin-top: 1.2rem;
      font-size: 0.82rem;
      color: var(--gold);
      font-weight: 600;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: gap 0.3s;
    }

    .program-card:hover .card-link { gap: 0.8rem; }

    /* ── WHY US ── */
    .why-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
      align-items: center;
      margin-top: 1rem;
    }

    .why-features {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      margin-top: 2.5rem;
    }

    .feature-item {
      display: flex;
      gap: 1rem;
      align-items: flex-start;
    }

    .feature-dot {
      width: 36px; height: 36px;
      min-width: 36px;
      background: rgba(201,168,76,0.1);
      border: 1px solid var(--glass-border);
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 0.9rem;
    }

    .feature-text h4 {
      font-size: 0.95rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .feature-text p {
      font-size: 0.82rem;
      color: rgba(255,255,255,0.45);
      line-height: 1.6;
    }

    /* Visual card on right */
    .why-visual {
      position: relative;
    }

    .visual-card {
      background: linear-gradient(135deg, rgba(201,168,76,0.08), rgba(201,168,76,0.02));
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 3rem 2.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .visual-card::before {
      content: '🎓';
      position: absolute;
      top: -30px; right: -20px;
      font-size: 8rem;
      opacity: 0.04;
    }

    .visual-card-num {
      font-family: 'Cormorant Garamond', serif;
      font-size: 5rem;
      font-weight: 700;
      color: var(--gold);
      line-height: 1;
    }

    .visual-card-label {
      font-size: 0.85rem;
      color: rgba(255,255,255,0.5);
      margin-top: 0.5rem;
      letter-spacing: 1px;
    }

    .visual-tag {
      display: inline-block;
      padding: 0.35rem 1rem;
      background: rgba(201,168,76,0.12);
      border: 1px solid var(--glass-border);
      border-radius: 50px;
      font-size: 0.75rem;
      color: var(--gold-light);
      margin: 0.4rem;
    }

    /* ── TESTIMONIALS ── */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      margin-top: 3rem;
    }

    .testimonial-card {
      padding: 2rem;
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      background: var(--glass);
    }

    .stars { color: var(--gold); font-size: 0.85rem; margin-bottom: 1rem; letter-spacing: 3px; }

    .testimonial-text {
      font-family: 'Playfair Display', serif;
      font-style: italic;
      font-size: 0.95rem;
      line-height: 1.7;
      color: rgba(255,255,255,0.75);
    }

    .testimonial-author {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      margin-top: 1.5rem;
    }

    .author-avatar {
      width: 38px; height: 38px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      display: grid;
      place-items: center;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--navy);
    }

    .author-name { font-size: 0.88rem; font-weight: 600; }
    .author-role { font-size: 0.75rem; color: rgba(255,255,255,0.4); }

    /* ── CTA BANNER ── */
    .cta-section {
      position: relative;
      z-index: 1;
      margin: 2rem 5% 5rem;
      border-radius: 20px;
      background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.03));
      border: 1px solid var(--glass-border);
      padding: 5rem 3rem;
      text-align: center;
      overflow: hidden;
    }

    .cta-section::before {
      content: '';
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(201,168,76,0.08), transparent 70%);
      pointer-events: none;
    }

    .cta-section h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2rem, 4vw, 3.5rem);
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .cta-section p {
      color: rgba(255,255,255,0.55);
      font-size: 1rem;
      max-width: 480px;
      margin: 0 auto 2.5rem;
      line-height: 1.7;
    }

    /* ── FOOTER ── */
    footer {
      position: relative;
      z-index: 1;
      border-top: 1px solid var(--glass-border);
      padding: 3rem 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .footer-copy {
      font-size: 0.78rem;
      color: rgba(255,255,255,0.3);
    }

    .footer-links {
      display: flex;
      gap: 2rem;
      list-style: none;
    }

    .footer-links a {
      font-size: 0.78rem;
      color: rgba(255,255,255,0.35);
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-links a:hover { color: var(--gold); }

    /* ── SCROLL INDICATOR ── */
    .scroll-indicator {
      position: absolute;
      bottom: 2.5rem;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      animation: fadeUp 1s 1.2s ease both;
    }

    .scroll-indicator span {
      font-size: 0.7rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: rgba(255,255,255,0.35);
    }

    .scroll-line {
      width: 1px;
      height: 40px;
      background: linear-gradient(180deg, var(--gold), transparent);
      animation: scrollPulse 2s ease-in-out infinite;
    }

    @keyframes scrollPulse {
      0%, 100% { opacity: 0.3; transform: scaleY(0.8); }
      50% { opacity: 1; transform: scaleY(1); }
    }

    /* ── SEPARATOR ── */
    .sep {
      width: 60px; height: 2px;
      background: linear-gradient(90deg, var(--gold), transparent);
      margin: 1rem 0 2rem;
    }

    /* ── FLOATING ORB ── */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      pointer-events: none;
      z-index: 0;
    }
    .orb-1 {
      width: 400px; height: 400px;
      background: rgba(201,168,76,0.06);
      top: 10%; right: -100px;
      animation: float1 12s ease-in-out infinite alternate;
    }
    .orb-2 {
      width: 300px; height: 300px;
      background: rgba(100,120,220,0.05);
      bottom: 20%; left: -80px;
      animation: float2 15s ease-in-out infinite alternate;
    }
    @keyframes float1 { from { transform: translateY(0); } to { transform: translateY(40px); } }
    @keyframes float2 { from { transform: translateY(0); } to { transform: translateY(-40px); } }

    /* PATHWAY SECTION */
    .pathway {
      position: relative;
      z-index: 1;
      padding: 4rem 5%;
      background: rgba(255,255,255,0.02);
      border-top: 1px solid var(--glass-border);
      border-bottom: 1px solid var(--glass-border);
    }

    .pathway-steps {
      display: flex;
      gap: 0;
      margin-top: 3rem;
      position: relative;
    }

    .pathway-steps::before {
      content: '';
      position: absolute;
      top: 28px; left: 0; right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
    }

    .step {
      flex: 1;
      text-align: center;
      padding: 0 1rem;
      position: relative;
    }

    .step-num {
      width: 56px; height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: var(--navy);
      font-weight: 800;
      font-size: 1.1rem;
      display: grid;
      place-items: center;
      margin: 0 auto 1.2rem;
      box-shadow: 0 0 25px rgba(201,168,76,0.3);
      font-family: 'Cormorant Garamond', serif;
    }

    .step h4 {
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.4rem;
    }

    .step p {
      font-size: 0.78rem;
      color: rgba(255,255,255,0.4);
      line-height: 1.5;
    }
  </style>
</head>
<body>

  <!-- Floating orbs -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>

  <!-- Starfield canvas -->
  <canvas id="stars"></canvas>

  <!-- NAVBAR -->
  <nav>
    <div class="logo">
      <div class="logo-icon">🎓</div>
      <div class="logo-text">Edu<span>Nova</span></div>
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="#programs">Programs</a></li>
      <li><a href="#admissions">Admissions</a></li>
      <li><a href="#about">About</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
    <div style="display:flex;gap:.6rem;align-items:center;">
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" style="padding:.5rem 1.1rem;border:1.5px solid rgba(201,168,76,.35);border-radius:4px;background:transparent;color:rgba(255,255,255,.7);font-family:'DM Sans',sans-serif;font-size:.82rem;text-decoration:none;transition:all .3s;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(201,168,76,.35)';this.style.color='rgba(255,255,255,.7)'">
          👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </a>
        <a href="logout.php" style="padding:.5rem 1.1rem;border:1.5px solid rgba(201,168,76,.35);border-radius:4px;background:transparent;color:rgba(255,255,255,.55);font-family:'DM Sans',sans-serif;font-size:.82rem;text-decoration:none;transition:all .3s;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(201,168,76,.35)';this.style.color='rgba(255,255,255,.55)'">
          Logout
        </a>
      <?php else: ?>
        <a href="register.php?mode=login" style="padding:.5rem 1.1rem;border:1.5px solid rgba(255,255,255,.2);border-radius:4px;background:transparent;color:rgba(255,255,255,.65);font-family:'DM Sans',sans-serif;font-size:.82rem;text-decoration:none;transition:all .3s;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(255,255,255,.2)';this.style.color='rgba(255,255,255,.65)'">
          Login
        </a>
        <a href="register.php" class="nav-btn">Register Now →</a>
        <a href="admin_login.php" style="padding:.5rem 1.1rem;border:1.5px solid rgba(201,168,76,.3);border-radius:4px;background:transparent;color:rgba(201,168,76,.7);font-family:'DM Sans',sans-serif;font-size:.82rem;text-decoration:none;transition:all .3s;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(201,168,76,.3)';this.style.color='rgba(201,168,76,.7)'">🔐 Admin</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-badge">🏆 Ranked #1 in Academic Excellence 2025</div>
    <h1>Shape Your Future<br/>With <em>World-Class</em> Education</h1>
    <p>EduNova Academy provides an unmatched learning experience that blends academic rigor with creative freedom — preparing students for tomorrow's world.</p>
    <div class="hero-cta">
      <a href="register.php" class="btn-primary">✦ Register Now</a>
      <a href="#programs" class="btn-outline">Explore Programs</a>
    </div>
    <div class="scroll-indicator">
      <span>Scroll</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- STATS BAR -->
  <div class="stats-bar">
    <div class="stat">
      <div class="stat-num">12K+</div>
      <div class="stat-label">Students Enrolled</div>
    </div>
    <div class="stat">
      <div class="stat-num">98%</div>
      <div class="stat-label">Placement Rate</div>
    </div>
    <div class="stat">
      <div class="stat-num">240+</div>
      <div class="stat-label">Faculty Members</div>
    </div>
    <div class="stat">
      <div class="stat-num">50+</div>
      <div class="stat-label">Programs Offered</div>
    </div>
    <div class="stat">
      <div class="stat-num">35+</div>
      <div class="stat-label">Years of Excellence</div>
    </div>
  </div>

  <!-- PROGRAMS -->
  <section class="section" id="programs">
    <div class="section-label">◆ What We Offer</div>
    <h2 class="section-title">Academic Programs Designed for Leaders</h2>
    <div class="sep"></div>
    <div class="programs-grid">
      <div class="program-card">
        <div class="card-icon">💻</div>
        <div class="card-title">Computer Science & AI</div>
        <div class="card-desc">Master algorithms, machine learning, and software engineering from industry experts.</div>
        <a href="apply.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;font-size:.82rem;color:var(--gold);font-weight:600;letter-spacing:.5px;cursor:pointer;transition:gap .3s;text-decoration:none;" class="card-link">Apply Now →</a>
      </div>
      <div class="program-card">
        <div class="card-icon">📊</div>
        <div class="card-title">Business Administration</div>
        <div class="card-desc">Develop leadership, strategy, and entrepreneurial thinking for global markets.</div>
        <a href="apply.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;font-size:.82rem;color:var(--gold);font-weight:600;letter-spacing:.5px;cursor:pointer;transition:gap .3s;text-decoration:none;" class="card-link">Apply Now →</a>
      </div>
      <div class="program-card">
        <div class="card-icon">🔬</div>
        <div class="card-title">Sciences & Research</div>
        <div class="card-desc">Cutting-edge labs, renowned faculty, and research opportunities across disciplines.</div>
        <a href="apply.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;font-size:.82rem;color:var(--gold);font-weight:600;letter-spacing:.5px;cursor:pointer;transition:gap .3s;text-decoration:none;" class="card-link">Apply Now →</a>
      </div>
      <div class="program-card">
        <div class="card-icon">🎨</div>
        <div class="card-title">Arts & Design</div>
        <div class="card-desc">Unleash creativity through fine arts, digital design, media, and architecture.</div>
        <a href="apply.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;font-size:.82rem;color:var(--gold);font-weight:600;letter-spacing:.5px;cursor:pointer;transition:gap .3s;text-decoration:none;" class="card-link">Apply Now →</a>
      </div>
      <div class="program-card">
        <div class="card-icon">⚖️</div>
        <div class="card-title">Law & Social Sciences</div>
        <div class="card-desc">Build critical thinking and analytical skills essential for today's legal landscape.</div>
        <a href="apply.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;font-size:.82rem;color:var(--gold);font-weight:600;letter-spacing:.5px;cursor:pointer;transition:gap .3s;text-decoration:none;" class="card-link">Apply Now →</a>
      </div>
      <div class="program-card">
        <div class="card-icon">🏥</div>
        <div class="card-title">Health & Medicine</div>
        <div class="card-desc">Join a community of compassionate healthcare professionals shaping the future.</div>
        <a href="apply.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1.2rem;font-size:.82rem;color:var(--gold);font-weight:600;letter-spacing:.5px;cursor:pointer;transition:gap .3s;text-decoration:none;" class="card-link">Apply Now →</a>
      </div>
    </div>
  </section>

  <!-- WHY US -->
  <section class="section" id="about">
    <div class="why-grid">
      <div>
        <div class="section-label">◆ Why EduNova</div>
        <h2 class="section-title">A Legacy of Excellence</h2>
        <div class="sep"></div>
        <div class="why-features">
          <div class="feature-item">
            <div class="feature-dot">🏛️</div>
            <div class="feature-text">
              <h4>World-Class Infrastructure</h4>
              <p>State-of-the-art labs, libraries, and smart classrooms designed for 21st century learning.</p>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-dot">🌍</div>
            <div class="feature-text">
              <h4>Global Partnerships</h4>
              <p>Collaboration with 100+ universities across 40 countries for exchange programs.</p>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-dot">🎯</div>
            <div class="feature-text">
              <h4>Personalized Learning</h4>
              <p>Mentorship-driven education with small class sizes and one-on-one faculty access.</p>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-dot">💼</div>
            <div class="feature-text">
              <h4>Career Placement Cell</h4>
              <p>Dedicated team connecting students to top recruiters, internships, and job fairs.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="why-visual">
        <div class="visual-card">
          <div class="visual-card-num">35+</div>
          <div class="visual-card-label">Years Shaping Futures</div>
          <div style="height:1.5rem"></div>
          <div class="visual-tag">🏆 Accredited</div>
          <div class="visual-tag">🌍 International</div>
          <div class="visual-tag">📚 Research-Led</div>
          <div class="visual-tag">💡 Innovation Hub</div>
          <div class="visual-tag">🤝 Industry Tied</div>
          <div class="visual-tag">🎓 Alumni Network</div>
          <div style="height:2rem"></div>
          <div style="font-size:0.8rem;color:rgba(255,255,255,0.35)">Trusted by families across 28 states</div>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW TO JOIN PATHWAY -->
  <section class="pathway" id="admissions">
    <div class="section-label" style="text-align:center">◆ Admissions Pathway</div>
    <h2 class="section-title" style="text-align:center;margin:0 auto;max-width:100%">Your Journey Starts Here</h2>
    <div class="sep" style="margin:1rem auto 0"></div>
    <div class="pathway-steps">
      <div class="step">
        <div class="step-num">1</div>
        <h4>Register Online</h4>
        <p>Fill the registration form with your basic details</p>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <h4>Choose Program</h4>
        <p>Select your desired course and specialization</p>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <h4>Upload Documents</h4>
        <p>Submit academic certificates and identity proof</p>
      </div>
      <div class="step">
        <div class="step-num">4</div>
        <h4>Entrance / Interview</h4>
        <p>Attend the entrance test or counseling session</p>
      </div>
      <div class="step">
        <div class="step-num">5</div>
        <h4>Get Admitted 🎉</h4>
        <p>Receive your offer letter and join the community</p>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="section">
    <div class="section-label">◆ Student Voices</div>
    <h2 class="section-title">Stories of Success</h2>
    <div class="sep"></div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <div class="testimonial-text">"EduNova completely transformed how I think. The faculty pushed me beyond limits I thought I had."</div>
        <div class="testimonial-author">
          <div class="author-avatar">A</div>
          <div>
            <div class="author-name">Ariana Mehta</div>
            <div class="author-role">CS Graduate, Batch 2024</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <div class="testimonial-text">"The global exchange program changed my life. I studied in three countries and landed my dream job."</div>
        <div class="testimonial-author">
          <div class="author-avatar">R</div>
          <div>
            <div class="author-name">Rohan Kapoor</div>
            <div class="author-role">Business Admin, Batch 2023</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <div class="testimonial-text">"From day one, I felt supported. The career cell helped me get placed at a Fortune 500 company."</div>
        <div class="testimonial-author">
          <div class="author-avatar">S</div>
          <div>
            <div class="author-name">Sophia Nkosi</div>
            <div class="author-role">Law Graduate, Batch 2024</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA BANNER -->
  <div class="cta-section">
    <h2>Ready to Begin Your Story?</h2>
    <p>Join thousands of students who chose excellence. Applications for 2025–26 are now open.</p>
    <a href="register.php" class="btn-primary" style="font-size:1.05rem;padding:1rem 3rem">✦ Register Now — It's Free</a>
  </div>

  <!-- FOOTER -->
  <footer>
    <div>
      <div class="logo">
        <div class="logo-icon">🎓</div>
        <div class="logo-text">Edu<span>Nova</span></div>
      </div>
      <div class="footer-copy" style="margin-top:0.6rem">© 2025 EduNova Academy. All Rights Reserved.</div>
    </div>
    <ul class="footer-links">
      <li><a href="#">Privacy</a></li>
      <li><a href="#">Terms</a></li>
      <li><a href="#">Sitemap</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
  </footer>

  <!-- STARFIELD SCRIPT -->
  <script>
    const canvas = document.getElementById('stars');
    const ctx = canvas.getContext('2d');
    let stars = [];

    function resize() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }

    function createStars(n) {
      stars = [];
      for (let i = 0; i < n; i++) {
        stars.push({
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
          r: Math.random() * 1.2,
          alpha: Math.random(),
          speed: 0.002 + Math.random() * 0.004
        });
      }
    }

    function drawStars() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      stars.forEach(s => {
        s.alpha += s.speed;
        if (s.alpha > 1 || s.alpha < 0) s.speed *= -1;
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(201,168,76,${s.alpha * 0.5})`;
        ctx.fill();
      });
      requestAnimationFrame(drawStars);
    }

    resize();
    createStars(180);
    drawStars();
    window.addEventListener('resize', () => { resize(); createStars(180); });

    // Scroll-reveal
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.program-card, .testimonial-card, .feature-item, .step').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(30px)';
      el.style.transition = 'all 0.6s ease';
      observer.observe(el);
    });
  </script>
</body>
</html>