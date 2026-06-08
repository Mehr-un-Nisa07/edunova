<?php
// register.php — Registration & Login page
session_start();
require_once 'User.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

$user = new User($pdo);
$mode = isset($_GET['mode']) && $_GET['mode'] === 'login' ? 'login' : 'register';
$message = "";
$msgType = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $message = "All fields are required.";
            $msgType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address.";
            $msgType = "error";
        } elseif (strlen($password) < 6) {
            $message = "Password must be at least 6 characters.";
            $msgType = "error";
        } elseif ($password !== $confirm) {
            $message = "Passwords do not match.";
            $msgType = "error";
        } else {
            $result = $user->register($name, $email, $password);
            $message = $result['message'];
            $msgType = $result['success'] ? "success" : "error";
            if ($result['success']) $mode = 'login';
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $message = "Please enter your email and password.";
            $msgType = "error";
        } else {
            $result = $user->login($email, $password);
            if ($result['success']) {
                $_SESSION['user_id']   = $result['user']['id'];
                $_SESSION['user_name'] = $result['user']['name'];
                header("Location: profile.php");
                exit;
            } else {
                $message = $result['message'];
                $msgType = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova Academy – <?= $mode === 'login' ? 'Login' : 'Register' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
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
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ── NAVBAR ── */
    nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1.2rem 5%;
      background: rgba(10,15,46,0.85);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--glass-border);
    }
    .logo { display: flex; align-items: center; gap: 0.7rem; text-decoration: none; }
    .logo-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      border-radius: 8px;
      display: grid; place-items: center;
      font-size: 1.2rem;
    }
    .logo-text {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem; font-weight: 700;
      color: var(--white);
    }
    .logo-text span { color: var(--gold); }
    .back-btn {
      padding: 0.5rem 1.2rem;
      border: 1.5px solid var(--glass-border);
      border-radius: 4px;
      background: transparent;
      color: rgba(255,255,255,0.6);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.82rem;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s;
    }
    .back-btn:hover { border-color: var(--gold); color: var(--gold); }

    /* ── MAIN ── */
    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem 1rem;
      position: relative;
      overflow: hidden;
    }
    /* Background glow */
    main::before {
      content: '';
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(201,168,76,0.07), transparent 70%);
      pointer-events: none;
    }

    /* ── CARD ── */
    .auth-card {
      width: 100%;
      max-width: 460px;
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 2.8rem 2.5rem;
      backdrop-filter: blur(12px);
      position: relative;
      z-index: 1;
    }
    .card-badge {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.3rem 0.9rem;
      border: 1px solid var(--glass-border);
      border-radius: 50px;
      background: var(--glass);
      font-size: 0.72rem;
      color: var(--gold-light);
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 1.2rem;
    }
    .card-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .card-subtitle {
      font-size: 0.84rem;
      color: rgba(255,255,255,0.45);
      margin-bottom: 2rem;
    }

    /* ── TABS ── */
    .tab-row {
      display: flex;
      gap: 0;
      margin-bottom: 2rem;
      border: 1px solid var(--glass-border);
      border-radius: 8px;
      overflow: hidden;
    }
    .tab-btn {
      flex: 1;
      padding: 0.7rem;
      background: transparent;
      border: none;
      color: rgba(255,255,255,0.45);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.88rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      text-align: center;
      letter-spacing: 0.3px;
    }
    .tab-btn.active {
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: var(--navy);
      font-weight: 700;
    }

    /* ── ALERT ── */
    .alert {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      margin-bottom: 1.4rem;
      display: flex; align-items: center; gap: 0.5rem;
    }
    .alert.error   { background: rgba(220,50,50,0.12); border: 1px solid rgba(220,50,50,0.3); color: #ff8080; }
    .alert.success { background: rgba(50,200,100,0.1); border: 1px solid rgba(50,200,100,0.3); color: #7dffb0; }

    /* ── FORM ── */
    .form-group { margin-bottom: 1.3rem; }
    .form-label {
      display: block;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: rgba(255,255,255,0.55);
      margin-bottom: 0.5rem;
    }
    .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(201,168,76,0.2);
      border-radius: 8px;
      color: var(--white);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      transition: border-color 0.3s, box-shadow 0.3s;
      outline: none;
    }
    .form-input:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
    }
    .form-input::placeholder { color: rgba(255,255,255,0.2); }

    .btn-submit {
      width: 100%;
      padding: 0.9rem;
      margin-top: 0.5rem;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: var(--navy);
      border: none;
      border-radius: 8px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      letter-spacing: 0.5px;
      transition: all 0.3s;
      box-shadow: 0 0 30px rgba(201,168,76,0.25);
    }
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 50px rgba(201,168,76,0.4);
    }

    .sep-text {
      text-align: center;
      font-size: 0.75rem;
      color: rgba(255,255,255,0.25);
      margin: 1.5rem 0 1rem;
      position: relative;
    }
    .sep-text::before, .sep-text::after {
      content: '';
      position: absolute;
      top: 50%; width: 38%;
      height: 1px;
      background: rgba(255,255,255,0.1);
    }
    .sep-text::before { left: 0; }
    .sep-text::after  { right: 0; }

    footer {
      text-align: center;
      padding: 1.5rem;
      font-size: 0.75rem;
      color: rgba(255,255,255,0.2);
      border-top: 1px solid var(--glass-border);
    }
  </style>
</head>
<body>

  <nav>
    <a href="index.php" class="logo">
      <div class="logo-icon">🎓</div>
      <div class="logo-text">Edu<span>Nova</span></div>
    </a>
    <a href="index.php" class="back-btn">← Back to Home</a>
  </nav>

  <main>
    <div class="auth-card">

      <div class="card-badge">🎓 Student Portal</div>
      <div class="card-title"><?= $mode === 'login' ? 'Welcome Back' : 'Join EduNova' ?></div>
      <div class="card-subtitle">
        <?= $mode === 'login'
            ? 'Sign in to access your student profile and dashboard.'
            : 'Create your account — it\'s free and takes 30 seconds.' ?>
      </div>

      <!-- TABS -->
      <div class="tab-row">
        <a href="register.php?mode=register" class="tab-btn <?= $mode === 'register' ? 'active' : '' ?>">Register</a>
        <a href="register.php?mode=login"    class="tab-btn <?= $mode === 'login'    ? 'active' : '' ?>">Login</a>
      </div>

      <!-- ALERT -->
      <?php if ($message): ?>
        <div class="alert <?= $msgType ?>">
          <?= $msgType === 'success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <!-- ── REGISTER FORM ── -->
      <?php if ($mode === 'register'): ?>
      <form method="POST" action="register.php?mode=register">
        <input type="hidden" name="action" value="register"/>
        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input class="form-input" type="text" id="name" name="name"
                 placeholder="e.g. Ariana Mehta"
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input class="form-input" type="email" id="email" name="email"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password"
                 placeholder="Minimum 6 characters" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm Password</label>
          <input class="form-input" type="password" id="confirm_password" name="confirm_password"
                 placeholder="Repeat your password" required/>
        </div>
        <button type="submit" class="btn-submit">✦ Create My Account</button>
      </form>

      <div class="sep-text">Already have an account?</div>
      <a href="register.php?mode=login" class="tab-btn active" style="display:block;border-radius:8px;padding:0.75rem;border:1px solid var(--glass-border);background:var(--glass);text-align:center;color:var(--gold);font-size:0.88rem;font-weight:600;text-decoration:none;">Sign In Instead →</a>

      <?php else: ?>
      <!-- ── LOGIN FORM ── -->
      <form method="POST" action="register.php?mode=login">
        <input type="hidden" name="action" value="login"/>
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input class="form-input" type="email" id="email" name="email"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password"
                 placeholder="Your password" required/>
        </div>
        <button type="submit" class="btn-submit">→ Sign In</button>
      </form>

      <div class="sep-text">Don't have an account?</div>
      <a href="register.php?mode=register" class="tab-btn active" style="display:block;border-radius:8px;padding:0.75rem;border:1px solid var(--glass-border);background:var(--glass);text-align:center;color:var(--gold);font-size:0.88rem;font-weight:600;text-decoration:none;">Register for Free →</a>
      <?php endif; ?>

    </div>
  </main>

  <footer>© 2025 EduNova Academy. All Rights Reserved.</footer>

</body>
</html>
