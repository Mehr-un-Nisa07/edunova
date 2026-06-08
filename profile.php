<?php
session_start();
require_once 'User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php?mode=login");
    exit;
}

$userObj  = new User($pdo);
$userData = $userObj->getById($_SESSION['user_id']);
$app      = $userObj->getApplication($_SESSION['user_id']);

$message = $_SESSION['success_msg'] ?? '';
unset($_SESSION['success_msg']);

$statusColors = [
    'pending'  => ['bg'=>'rgba(200,150,50,.1)', 'border'=>'rgba(200,150,50,.3)', 'color'=>'#ffd080', 'icon'=>'⏳'],
    'reviewed' => ['bg'=>'rgba(80,130,220,.1)',  'border'=>'rgba(80,130,220,.3)',  'color'=>'#90c8ff', 'icon'=>'🔍'],
    'accepted' => ['bg'=>'rgba(50,200,100,.1)',  'border'=>'rgba(50,200,100,.3)',  'color'=>'#7dffb0', 'icon'=>'🎉'],
    'rejected' => ['bg'=>'rgba(200,80,80,.1)',   'border'=>'rgba(200,80,80,.3)',   'color'=>'#ff9090', 'icon'=>'❌'],
];
$sc = $app ? $statusColors[$app['status']] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova – My Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--navy:#0a0f2e;--gold:#c9a84c;--gold-light:#e8c97e;--white:#fff;--glass:rgba(255,255,255,.06);--glass-border:rgba(201,168,76,.25);}
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--navy);color:var(--white);min-height:100vh;display:flex;flex-direction:column;}
    nav{display:flex;align-items:center;justify-content:space-between;padding:1.2rem 5%;background:rgba(10,15,46,.85);backdrop-filter:blur(18px);border-bottom:1px solid var(--glass-border);}
    .logo{display:flex;align-items:center;gap:.7rem;text-decoration:none;}
    .logo-icon{width:38px;height:38px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:8px;display:grid;place-items:center;font-size:1.2rem;}
    .logo-text{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:700;color:var(--white);}
    .logo-text span{color:var(--gold);}
    .nav-right{display:flex;align-items:center;gap:.8rem;}
    .nav-user{font-size:.82rem;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:.5rem;}
    .nav-user span{width:30px;height:30px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:50%;display:grid;place-items:center;font-size:.75rem;font-weight:700;color:var(--navy);}
    .btn-link{padding:.5rem 1.1rem;border:1.5px solid var(--glass-border);border-radius:4px;background:transparent;color:rgba(255,255,255,.55);font-family:'DM Sans',sans-serif;font-size:.82rem;cursor:pointer;text-decoration:none;transition:all .3s;}
    .btn-link:hover{border-color:var(--gold);color:var(--gold);}
    main{flex:1;display:flex;justify-content:center;align-items:flex-start;padding:3rem 1rem;position:relative;}
    main::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(201,168,76,.06),transparent 70%);pointer-events:none;}
    .wrap{width:100%;max-width:680px;position:relative;z-index:1;}
    .page-label{font-size:.72rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:.5rem;}
    .page-title{font-family:'Cormorant Garamond',serif;font-size:2.2rem;font-weight:700;margin-bottom:.4rem;}
    .page-sub{font-size:.85rem;color:rgba(255,255,255,.4);margin-bottom:2rem;}
    .alert{padding:.75rem 1rem;border-radius:8px;font-size:.85rem;margin-bottom:1.4rem;display:flex;align-items:center;gap:.5rem;background:rgba(50,200,100,.1);border:1px solid rgba(50,200,100,.3);color:#7dffb0;}
    .card{background:rgba(255,255,255,.04);border:1px solid var(--glass-border);border-radius:20px;padding:2.5rem;backdrop-filter:blur(12px);margin-bottom:1.5rem;}
    .avatar-row{display:flex;align-items:center;gap:1.5rem;padding-bottom:2rem;margin-bottom:2rem;border-bottom:1px solid var(--glass-border);}
    .avatar{width:72px;height:72px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:50%;display:grid;place-items:center;font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:700;color:var(--navy);box-shadow:0 0 30px rgba(201,168,76,.3);}
    .avatar-info h3{font-size:1.2rem;font-weight:600;margin-bottom:.25rem;}
    .avatar-info p{font-size:.8rem;color:rgba(255,255,255,.4);}
    .field-group{margin-bottom:1.4rem;}
    .field-label{font-size:.72rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:.4rem;}
    .field-value{font-size:.98rem;color:rgba(255,255,255,.85);padding:.7rem 1rem;background:rgba(255,255,255,.04);border:1px solid rgba(201,168,76,.1);border-radius:8px;}
    .action-row{display:flex;gap:.8rem;flex-wrap:wrap;margin-top:2rem;padding-top:2rem;border-top:1px solid var(--glass-border);}
    .btn-gold{padding:.75rem 1.8rem;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--navy);border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:700;cursor:pointer;text-decoration:none;transition:all .3s;box-shadow:0 0 20px rgba(201,168,76,.2);}
    .btn-gold:hover{transform:translateY(-2px);box-shadow:0 0 35px rgba(201,168,76,.35);}
    .btn-outline{padding:.75rem 1.8rem;background:transparent;color:rgba(255,255,255,.5);border:1.5px solid rgba(255,255,255,.15);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.88rem;cursor:pointer;text-decoration:none;transition:all .3s;}
    .btn-outline:hover{border-color:#ff6b6b;color:#ff6b6b;}

    /* Application card */
    .app-card{border-radius:16px;padding:1.8rem 2rem;margin-bottom:1.5rem;}
    .app-card-header{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.2rem;}
    .app-program{font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:700;}
    .app-degree{font-size:.8rem;color:rgba(255,255,255,.45);margin-top:.2rem;}
    .status-badge{padding:.3rem .9rem;border-radius:50px;font-size:.7rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;flex-shrink:0;}
    .app-detail-row{display:flex;gap:2rem;flex-wrap:wrap;margin-bottom:1.2rem;}
    .app-detail{font-size:.8rem;color:rgba(255,255,255,.5);}
    .app-detail strong{color:rgba(255,255,255,.75);display:block;font-size:.72rem;letter-spacing:.5px;text-transform:uppercase;margin-bottom:.2rem;}
    .app-actions{display:flex;gap:.8rem;flex-wrap:wrap;padding-top:1.2rem;border-top:1px solid rgba(255,255,255,.07);}
    .btn-sm-gold{padding:.55rem 1.3rem;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--navy);border:none;border-radius:6px;font-family:'DM Sans',sans-serif;font-size:.8rem;font-weight:700;cursor:pointer;text-decoration:none;transition:all .3s;}
    .btn-sm-gold:hover{transform:translateY(-1px);}
    .btn-sm-outline{padding:.55rem 1.3rem;background:transparent;color:rgba(255,255,255,.45);border:1px solid rgba(255,255,255,.15);border-radius:6px;font-family:'DM Sans',sans-serif;font-size:.8rem;text-decoration:none;transition:all .3s;}
    .btn-sm-outline:hover{border-color:var(--gold);color:var(--gold);}

    /* No application card */
    .no-app{background:rgba(201,168,76,.04);border:1px dashed rgba(201,168,76,.2);border-radius:16px;padding:2rem;text-align:center;margin-bottom:1.5rem;}
    .no-app p{font-size:.85rem;color:rgba(255,255,255,.4);margin-bottom:1.2rem;line-height:1.6;}

    footer{text-align:center;padding:1.5rem;font-size:.75rem;color:rgba(255,255,255,.2);border-top:1px solid var(--glass-border);}
  </style>
</head>
<body>
<nav>
  <a href="index.php" class="logo"><div class="logo-icon">🎓</div><div class="logo-text">Edu<span>Nova</span></div></a>
  <div class="nav-right">
    <div class="nav-user"><span><?= strtoupper(substr($userData['name'],0,1)) ?></span><?= htmlspecialchars($userData['name']) ?></div>
    <a href="logout.php" class="btn-link">Logout →</a>
  </div>
</nav>

<main>
  <div class="wrap">
    <div class="page-label">◆ Student Portal</div>
    <h1 class="page-title">My Profile</h1>
    <p class="page-sub">Your account details, application status, and student information.</p>

    <?php if ($message): ?>
      <div class="alert">✅ <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- ACCOUNT INFO CARD -->
    <div class="card">
      <div class="avatar-row">
        <div class="avatar"><?= strtoupper(substr($userData['name'],0,1)) ?></div>
        <div class="avatar-info">
          <h3><?= htmlspecialchars($userData['name']) ?></h3>
          <p>Student · EduNova Academy</p>
        </div>
      </div>
      <div class="field-group">
        <div class="field-label">Full Name</div>
        <div class="field-value"><?= htmlspecialchars($userData['name']) ?></div>
      </div>
      <div class="field-group">
        <div class="field-label">Email Address</div>
        <div class="field-value"><?= htmlspecialchars($userData['email']) ?></div>
      </div>
      <div class="field-group">
        <div class="field-label">Password</div>
        <div class="field-value">••••••••••••</div>
      </div>
      <div class="field-group">
        <div class="field-label">Account ID</div>
        <div class="field-value">#<?= str_pad($userData['id'],5,'0',STR_PAD_LEFT) ?></div>
      </div>
      <div class="action-row">
        <a href="edit_profile.php" class="btn-gold">✏️ Edit Profile</a>
        <a href="logout.php" class="btn-outline">🚪 Logout</a>
      </div>
    </div>

    <!-- APPLICATION SECTION -->
    <div class="page-label" style="margin-top:1rem;">◆ My Application</div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;margin-bottom:.3rem;">Program Application</h2>
    <p style="font-size:.84rem;color:rgba(255,255,255,.4);margin-bottom:1.2rem;">Track and manage your program application.</p>

    <?php if ($app): ?>
      <div class="app-card" style="background:<?= $sc['bg'] ?>;border:1px solid <?= $sc['border'] ?>;">
        <div class="app-card-header">
          <div>
            <div class="app-program"><?= htmlspecialchars($app['program']) ?></div>
            <div class="app-degree"><?= htmlspecialchars($app['degree']) ?> Program</div>
          </div>
          <span class="status-badge" style="background:<?= $sc['bg'] ?>;border:1px solid <?= $sc['border'] ?>;color:<?= $sc['color'] ?>">
            <?= $sc['icon'] ?> <?= ucfirst($app['status']) ?>
          </span>
        </div>
        <div class="app-detail-row">
          <div class="app-detail"><strong>Phone</strong><?= htmlspecialchars($app['phone']) ?></div>
          <div class="app-detail"><strong>Date of Birth</strong><?= htmlspecialchars($app['dob']) ?></div>
          <div class="app-detail"><strong>Previous School</strong><?= htmlspecialchars($app['prev_school']) ?></div>
          <div class="app-detail"><strong>Grade</strong><?= htmlspecialchars($app['prev_grade']) ?></div>
        </div>
        <div style="font-size:.82rem;color:rgba(255,255,255,.45);margin-bottom:.5rem;">
          <strong style="font-size:.7rem;letter-spacing:.5px;text-transform:uppercase;color:rgba(255,255,255,.3);display:block;margin-bottom:.3rem;">Personal Statement</strong>
          <?= nl2br(htmlspecialchars(substr($app['statement'],0,200))) ?><?= strlen($app['statement'])>200 ? '…' : '' ?>
        </div>
        <div class="app-actions">
          <a href="apply.php" class="btn-sm-gold">✏️ Edit Application</a>
          <a href="apply.php?withdraw=1" class="btn-sm-outline" onclick="return confirm('Withdraw your application?')">🗑 Withdraw</a>
        </div>
      </div>
    <?php else: ?>
      <div class="no-app">
        <div style="font-size:2.5rem;margin-bottom:.8rem;">📋</div>
        <p>You have not applied for any program yet.<br/>Choose a program and submit your application to get started.</p>
        <a href="apply.php" class="btn-gold">🚀 Apply for a Program</a>
      </div>
    <?php endif; ?>

  </div>
</main>
<footer>© 2025 EduNova Academy. All Rights Reserved.</footer>
</body>
</html>
