<?php
// edit_profile.php — Edit name/email and change password
session_start();
require_once 'User.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php?mode=login");
    exit;
}

$userObj  = new User($pdo);
$userData = $userObj->getById($_SESSION['user_id']);
$infoMsg  = "";
$infoType = "";
$passMsg  = "";
$passType = "";

// Handle profile update (name & email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update_profile') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($name) || empty($email)) {
            $infoMsg = "Name and email are required.";
            $infoType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $infoMsg = "Please enter a valid email address.";
            $infoType = "error";
        } else {
            $result = $userObj->updateProfile($_SESSION['user_id'], $name, $email);
            $infoMsg  = $result['message'];
            $infoType = $result['success'] ? "success" : "error";
            if ($result['success']) {
                $_SESSION['user_name'] = $name;
                $userData = $userObj->getById($_SESSION['user_id']); // refresh
            }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $passMsg = "All password fields are required.";
            $passType = "error";
        } elseif (strlen($new) < 6) {
            $passMsg = "New password must be at least 6 characters.";
            $passType = "error";
        } elseif ($new !== $confirm) {
            $passMsg = "New passwords do not match.";
            $passType = "error";
        } else {
            $result = $userObj->updatePassword($_SESSION['user_id'], $current, $new);
            $passMsg  = $result['message'];
            $passType = $result['success'] ? "success" : "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova – Edit Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --navy: #0a0f2e; --gold: #c9a84c; --gold-light: #e8c97e;
      --white: #ffffff; --glass: rgba(255,255,255,0.07);
      --glass-border: rgba(201,168,76,0.25);
    }
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--navy); color: var(--white);
      min-height: 100vh; display: flex; flex-direction: column;
    }
    nav {
      display: flex; align-items: center; justify-content: space-between;
      padding: 1.2rem 5%;
      background: rgba(10,15,46,0.85);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--glass-border);
    }
    .logo { display: flex; align-items: center; gap: 0.7rem; text-decoration: none; }
    .logo-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      border-radius: 8px; display: grid; place-items: center; font-size: 1.2rem;
    }
    .logo-text { font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; font-weight: 700; color: var(--white); }
    .logo-text span { color: var(--gold); }
    .nav-right { display: flex; gap: 0.8rem; }
    .btn-link {
      padding: 0.5rem 1.1rem;
      border: 1.5px solid var(--glass-border);
      border-radius: 4px; background: transparent;
      color: rgba(255,255,255,0.55);
      font-family: 'DM Sans', sans-serif; font-size: 0.82rem;
      cursor: pointer; text-decoration: none; transition: all 0.3s;
    }
    .btn-link:hover { border-color: var(--gold); color: var(--gold); }

    main {
      flex: 1; display: flex; justify-content: center;
      align-items: flex-start; padding: 3rem 1rem; position: relative;
    }
    main::before {
      content: ''; position: absolute; top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(201,168,76,0.06), transparent 70%);
      pointer-events: none;
    }

    .edit-wrap { width: 100%; max-width: 580px; position: relative; z-index: 1; }

    .page-label { font-size: 0.72rem; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); margin-bottom: 0.5rem; }
    .page-title { font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; font-weight: 700; margin-bottom: 0.4rem; }
    .page-sub   { font-size: 0.85rem; color: rgba(255,255,255,0.4); margin-bottom: 2rem; }

    .card {
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--glass-border);
      border-radius: 20px; padding: 2.5rem;
      backdrop-filter: blur(12px); margin-bottom: 1.5rem;
    }
    .card-heading {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.35rem; font-weight: 700; margin-bottom: 1.8rem;
      padding-bottom: 1rem; border-bottom: 1px solid var(--glass-border);
      display: flex; align-items: center; gap: 0.5rem;
    }

    .alert {
      padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem;
      margin-bottom: 1.4rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .alert.error   { background: rgba(220,50,50,0.12); border: 1px solid rgba(220,50,50,0.3); color: #ff8080; }
    .alert.success { background: rgba(50,200,100,0.1); border: 1px solid rgba(50,200,100,0.3); color: #7dffb0; }

    .form-group  { margin-bottom: 1.3rem; }
    .form-label  {
      display: block; font-size: 0.78rem; font-weight: 600;
      letter-spacing: 0.8px; text-transform: uppercase;
      color: rgba(255,255,255,0.55); margin-bottom: 0.5rem;
    }
    .form-input {
      width: 100%; padding: 0.75rem 1rem;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(201,168,76,0.2);
      border-radius: 8px; color: var(--white);
      font-family: 'DM Sans', sans-serif; font-size: 0.9rem;
      transition: border-color 0.3s, box-shadow 0.3s; outline: none;
    }
    .form-input:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
    }
    .btn-gold {
      padding: 0.75rem 2rem;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: var(--navy); border: none; border-radius: 8px;
      font-family: 'DM Sans', sans-serif; font-size: 0.9rem; font-weight: 700;
      cursor: pointer; transition: all 0.3s;
      box-shadow: 0 0 20px rgba(201,168,76,0.2);
    }
    .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 0 35px rgba(201,168,76,0.35); }
    .back-row { margin-bottom: 1.5rem; }
    .back-link {
      font-size: 0.82rem; color: rgba(255,255,255,0.4);
      text-decoration: none; transition: color 0.3s;
      display: inline-flex; align-items: center; gap: 0.3rem;
    }
    .back-link:hover { color: var(--gold); }

    footer {
      text-align: center; padding: 1.5rem;
      font-size: 0.75rem; color: rgba(255,255,255,0.2);
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
    <div class="nav-right">
      <a href="profile.php" class="btn-link">← My Profile</a>
      <a href="logout.php"  class="btn-link">Logout</a>
    </div>
  </nav>

  <main>
    <div class="edit-wrap">

      <div class="back-row">
        <a href="profile.php" class="back-link">← Back to Profile</a>
      </div>

      <div class="page-label">◆ Student Portal</div>
      <h1 class="page-title">Edit Profile</h1>
      <p class="page-sub">Update your personal information or change your password.</p>

      <!-- ── SECTION 1: Profile Info ── -->
      <div class="card">
        <div class="card-heading">👤 Personal Information</div>

        <?php if ($infoMsg): ?>
          <div class="alert <?= $infoType ?>"><?= $infoType === 'success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($infoMsg) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_profile.php">
          <input type="hidden" name="action" value="update_profile"/>
          <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input class="form-input" type="text" id="name" name="name"
                   value="<?= htmlspecialchars($userData['name']) ?>" required/>
          </div>
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input class="form-input" type="email" id="email" name="email"
                   value="<?= htmlspecialchars($userData['email']) ?>" required/>
          </div>
          <button type="submit" class="btn-gold">💾 Save Changes</button>
        </form>
      </div>

      <!-- ── SECTION 2: Change Password ── -->
      <div class="card">
        <div class="card-heading">🔒 Change Password</div>

        <?php if ($passMsg): ?>
          <div class="alert <?= $passType ?>"><?= $passType === 'success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($passMsg) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_profile.php">
          <input type="hidden" name="action" value="change_password"/>
          <div class="form-group">
            <label class="form-label" for="current_password">Current Password</label>
            <input class="form-input" type="password" id="current_password"
                   name="current_password" placeholder="Enter your current password" required/>
          </div>
          <div class="form-group">
            <label class="form-label" for="new_password">New Password</label>
            <input class="form-input" type="password" id="new_password"
                   name="new_password" placeholder="Minimum 6 characters" required/>
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm New Password</label>
            <input class="form-input" type="password" id="confirm_password"
                   name="confirm_password" placeholder="Repeat new password" required/>
          </div>
          <button type="submit" class="btn-gold">🔑 Update Password</button>
        </form>
      </div>

    </div>
  </main>

  <footer>© 2025 EduNova Academy. All Rights Reserved.</footer>

</body>
</html>
