<?php
session_start();

// ── Admin credentials ──────────────────────────────────────────────────────
// Change these before going live!
define('ADMIN_EMAIL', 'admin@edunova.com');
define('ADMIN_PASS',  'edunova2025');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    if ($email === ADMIN_EMAIL && $pass === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova – Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--navy:#0a0f2e;--gold:#c9a84c;--gold-light:#e8c97e;--glass:rgba(255,255,255,0.06);--glass-border:rgba(201,168,76,0.25);}
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--navy);color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;}
    .box{width:100%;max-width:420px;background:rgba(255,255,255,0.04);border:1px solid var(--glass-border);border-radius:20px;padding:3rem 2.5rem;backdrop-filter:blur(12px);position:relative;}
    .box::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:400px;height:400px;background:radial-gradient(circle,rgba(201,168,76,0.08),transparent 70%);pointer-events:none;}
    .logo{display:flex;align-items:center;gap:0.7rem;justify-content:center;margin-bottom:0.5rem;}
    .logo-icon{width:42px;height:42px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:10px;display:grid;place-items:center;font-size:1.3rem;}
    .logo-text{font-family:'Cormorant Garamond',serif;font-size:1.7rem;font-weight:700;}
    .logo-text span{color:var(--gold);}
    .subtitle{text-align:center;font-size:0.8rem;color:rgba(255,255,255,0.35);letter-spacing:2px;text-transform:uppercase;margin-bottom:2.5rem;}
    .form-label{display:block;font-size:0.74rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;}
    .form-group{margin-bottom:1.3rem;position:relative;}
    .form-input{width:100%;padding:0.78rem 1rem;background:rgba(255,255,255,0.05);border:1px solid rgba(201,168,76,0.2);border-radius:8px;color:#fff;font-family:'DM Sans',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.3s;}
    .form-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,0.1);}
    .form-input::placeholder{color:rgba(255,255,255,0.2);}
    .btn{width:100%;padding:0.85rem;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--navy);border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.95rem;font-weight:700;cursor:pointer;transition:all 0.3s;margin-top:0.5rem;}
    .btn:hover{transform:translateY(-2px);box-shadow:0 0 30px rgba(201,168,76,0.3);}
    .error{background:rgba(220,50,50,0.1);border:1px solid rgba(220,50,50,0.3);color:#ff8080;padding:0.75rem 1rem;border-radius:8px;font-size:0.85rem;margin-bottom:1.2rem;text-align:center;}
    .back{display:block;text-align:center;margin-top:1.5rem;font-size:0.8rem;color:rgba(255,255,255,0.3);text-decoration:none;transition:color 0.3s;}
    .back:hover{color:var(--gold);}
    .hint{text-align:center;margin-top:1.2rem;font-size:0.75rem;color:rgba(255,255,255,0.2);line-height:1.6;}
  </style>
</head>
<body>
<div class="box">
  <div class="logo">
    <div class="logo-icon">🎓</div>
    <div class="logo-text">Edu<span>Nova</span></div>
  </div>
  <div class="subtitle">Admin Portal</div>

  <?php if ($error): ?>
    <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="admin_login.php">
    <div class="form-group">
      <label class="form-label" for="email">Admin Email</label>
      <input class="form-input" type="email" id="email" name="email"
             placeholder="Enter admin email" required autofocus
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
    </div>
    <div class="form-group">
      <label class="form-label" for="password">Password</label>
      <input class="form-input" type="password" id="password" name="password"
             placeholder="Enter password" required/>
    </div>
    <button class="btn" type="submit">🔐 Login to Admin Panel</button>
  </form>

  <a href="index.php" class="back">← Back to EduNova Homepage</a>
  <div class="hint">
    Default email: admin@edunova.com<br/>
    Default password: edunova2025<br/>
    Change these in admin_login.php before going live.
  </div>
</div>
</body>
</html>
