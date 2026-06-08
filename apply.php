<?php
session_start();
require_once 'User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php?mode=login");
    exit;
}

$userObj = new User($pdo);
$existing = $userObj->getApplication($_SESSION['user_id']);
$message = "";
$msgType = "";

// Pre-fill program if coming from homepage card
$preProgram = $_GET['program'] ?? ($existing['program'] ?? '');

$programs = [
    "Computer Science & AI",
    "Business Administration",
    "Sciences & Research",
    "Arts & Design",
    "Law & Social Sciences",
    "Health & Medicine"
];
$degrees = ["Bachelor's", "Master's", "PhD", "Diploma", "Certificate"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program    = trim($_POST['program'] ?? '');
    $degree     = trim($_POST['degree'] ?? '');
    $dob        = trim($_POST['dob'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $prevSchool = trim($_POST['prev_school'] ?? '');
    $prevGrade  = trim($_POST['prev_grade'] ?? '');
    $statement  = trim($_POST['statement'] ?? '');

    if (empty($program) || empty($degree) || empty($dob) || empty($phone) || empty($address) || empty($prevSchool) || empty($prevGrade) || empty($statement)) {
        $message = "Please fill in all fields.";
        $msgType = "error";
    } elseif (strlen($statement) < 50) {
        $message = "Personal statement must be at least 50 characters.";
        $msgType = "error";
    } else {
        if ($existing) {
            $result = $userObj->updateApplication($_SESSION['user_id'], $program, $degree, $dob, $phone, $address, $prevSchool, $prevGrade, $statement);
        } else {
            $result = $userObj->applyForProgram($_SESSION['user_id'], $program, $degree, $dob, $phone, $address, $prevSchool, $prevGrade, $statement);
        }
        $message = $result['message'];
        $msgType = $result['success'] ? "success" : "error";
        if ($result['success']) {
            $existing = $userObj->getApplication($_SESSION['user_id']); // refresh
        }
    }
}

// Handle withdraw
if (isset($_GET['withdraw']) && $existing) {
    $userObj->withdrawApplication($_SESSION['user_id']);
    header("Location: apply.php?withdrawn=1");
    exit;
}
if (isset($_GET['withdrawn'])) {
    $message = "Your application has been withdrawn.";
    $msgType = "success";
    $existing = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova – Apply for a Program</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root { --navy:#0a0f2e; --gold:#c9a84c; --gold-light:#e8c97e; --white:#fff; --glass:rgba(255,255,255,0.06); --glass-border:rgba(201,168,76,0.25); }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--navy);color:var(--white);min-height:100vh;display:flex;flex-direction:column;}

    nav{display:flex;align-items:center;justify-content:space-between;padding:1.2rem 5%;background:rgba(10,15,46,0.85);backdrop-filter:blur(18px);border-bottom:1px solid var(--glass-border);}
    .logo{display:flex;align-items:center;gap:.7rem;text-decoration:none;}
    .logo-icon{width:38px;height:38px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:8px;display:grid;place-items:center;font-size:1.2rem;}
    .logo-text{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:700;color:var(--white);}
    .logo-text span{color:var(--gold);}
    .nav-right{display:flex;gap:.8rem;align-items:center;}
    .btn-link{padding:.5rem 1.1rem;border:1.5px solid var(--glass-border);border-radius:4px;background:transparent;color:rgba(255,255,255,.55);font-family:'DM Sans',sans-serif;font-size:.82rem;cursor:pointer;text-decoration:none;transition:all .3s;}
    .btn-link:hover{border-color:var(--gold);color:var(--gold);}

    main{flex:1;display:flex;justify-content:center;padding:3rem 1rem;position:relative;}
    main::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:700px;height:700px;background:radial-gradient(circle,rgba(201,168,76,0.06),transparent 70%);pointer-events:none;}

    .wrap{width:100%;max-width:680px;position:relative;z-index:1;}
    .page-label{font-size:.72rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:.5rem;}
    .page-title{font-family:'Cormorant Garamond',serif;font-size:2.3rem;font-weight:700;margin-bottom:.4rem;}
    .page-sub{font-size:.85rem;color:rgba(255,255,255,.4);margin-bottom:2rem;line-height:1.6;}

    /* Status badge */
    .status-banner{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;background:rgba(201,168,76,.07);border:1px solid rgba(201,168,76,.25);}
    .status-info{font-size:.84rem;color:rgba(255,255,255,.7);}
    .status-info strong{color:var(--gold-light);}
    .status-badge{padding:.3rem .9rem;border-radius:50px;font-size:.7rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;}
    .status-pending  {background:rgba(200,150,50,.15);border:1px solid rgba(200,150,50,.3);color:#ffd080;}
    .status-reviewed {background:rgba(80,130,220,.15);border:1px solid rgba(80,130,220,.3);color:#90c8ff;}
    .status-accepted {background:rgba(50,200,100,.15);border:1px solid rgba(50,200,100,.3);color:#7dffb0;}
    .status-rejected {background:rgba(200,80,80,.15); border:1px solid rgba(200,80,80,.3); color:#ff9090;}

    .card{background:rgba(255,255,255,.04);border:1px solid var(--glass-border);border-radius:20px;padding:2.5rem;backdrop-filter:blur(12px);margin-bottom:1.5rem;}
    .card-heading{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:700;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--glass-border);}

    .alert{padding:.75rem 1rem;border-radius:8px;font-size:.85rem;margin-bottom:1.4rem;display:flex;align-items:center;gap:.5rem;}
    .alert.error  {background:rgba(220,50,50,.12);border:1px solid rgba(220,50,50,.3);color:#ff8080;}
    .alert.success{background:rgba(50,200,100,.1);border:1px solid rgba(50,200,100,.3);color:#7dffb0;}

    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-bottom:1.2rem;}
    .form-group{margin-bottom:1.2rem;}
    .form-label{display:block;font-size:.76rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:.5rem;}
    .form-input,.form-select,.form-textarea{width:100%;padding:.75rem 1rem;background:rgba(255,255,255,.05);border:1px solid rgba(201,168,76,.2);border-radius:8px;color:var(--white);font-family:'DM Sans',sans-serif;font-size:.9rem;transition:border-color .3s,box-shadow .3s;outline:none;}
    .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.1);}
    .form-input::placeholder,.form-textarea::placeholder{color:rgba(255,255,255,.2);}
    .form-select option{background:var(--navy);color:var(--white);}
    .form-textarea{resize:vertical;min-height:110px;line-height:1.6;}
    .char-count{font-size:.7rem;color:rgba(255,255,255,.25);text-align:right;margin-top:.25rem;}

    .btn-gold{padding:.8rem 2rem;background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--navy);border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:700;cursor:pointer;transition:all .3s;box-shadow:0 0 20px rgba(201,168,76,.2);text-decoration:none;display:inline-block;}
    .btn-gold:hover{transform:translateY(-2px);box-shadow:0 0 35px rgba(201,168,76,.35);}
    .btn-danger{padding:.75rem 1.5rem;background:transparent;color:rgba(255,100,100,.7);border:1.5px solid rgba(255,100,100,.3);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.85rem;cursor:pointer;transition:all .3s;text-decoration:none;}
    .btn-danger:hover{border-color:#ff6b6b;color:#ff6b6b;background:rgba(255,100,100,.05);}

    .action-row{display:flex;gap:.8rem;flex-wrap:wrap;align-items:center;margin-top:.5rem;}

    footer{text-align:center;padding:1.5rem;font-size:.75rem;color:rgba(255,255,255,.2);border-top:1px solid var(--glass-border);}
    @media(max-width:600px){.form-row{grid-template-columns:1fr;}}
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
    <a href="logout.php" class="btn-link">Logout</a>
  </div>
</nav>

<main>
  <div class="wrap">
    <div class="page-label">◆ Admissions</div>
    <h1 class="page-title"><?= $existing ? 'Update Your Application' : 'Apply for a Program' ?></h1>
    <p class="page-sub">Complete the form below. You can update your application at any time before the deadline.</p>

    <?php if ($existing): ?>
    <div class="status-banner">
      <div class="status-info">
        You applied for <strong><?= htmlspecialchars($existing['program']) ?></strong>
        (<?= htmlspecialchars($existing['degree']) ?>)
      </div>
      <span class="status-badge status-<?= $existing['status'] ?>">
        <?= ucfirst($existing['status']) ?>
      </span>
    </div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="alert <?= $msgType ?>"><?= $msgType === 'success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-heading">🎓 Program Selection</div>
      <form method="POST" action="apply.php">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="program">Program</label>
            <select class="form-select" id="program" name="program" required>
              <option value="">— Select a Program —</option>
              <?php foreach ($programs as $p): ?>
                <option value="<?= $p ?>" <?= ($preProgram === $p) ? 'selected' : '' ?>><?= $p ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="degree">Degree Level</label>
            <select class="form-select" id="degree" name="degree" required>
              <option value="">— Select Degree —</option>
              <?php foreach ($degrees as $d): ?>
                <option value="<?= $d ?>" <?= (($existing['degree'] ?? '') === $d) ? 'selected' : '' ?>><?= $d ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div><!-- close card early, open new one -->

    <div class="card">
      <div class="card-heading">👤 Personal Details</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="dob">Date of Birth</label>
            <input class="form-input" type="date" id="dob" name="dob" value="<?= htmlspecialchars($existing['dob'] ?? '') ?>" required/>
          </div>
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input class="form-input" type="tel" id="phone" name="phone" placeholder="+92 300 0000000" value="<?= htmlspecialchars($existing['phone'] ?? '') ?>" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="address">Home Address</label>
          <input class="form-input" type="text" id="address" name="address" placeholder="Street, City, Country" value="<?= htmlspecialchars($existing['address'] ?? '') ?>" required/>
        </div>
    </div>

    <div class="card">
      <div class="card-heading">📚 Academic Background</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="prev_school">Previous School / Institution</label>
            <input class="form-input" type="text" id="prev_school" name="prev_school" placeholder="e.g. Lahore Grammar School" value="<?= htmlspecialchars($existing['prev_school'] ?? '') ?>" required/>
          </div>
          <div class="form-group">
            <label class="form-label" for="prev_grade">Final Grade / GPA</label>
            <input class="form-input" type="text" id="prev_grade" name="prev_grade" placeholder="e.g. A+ / 3.8 GPA / 90%" value="<?= htmlspecialchars($existing['prev_grade'] ?? '') ?>" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="statement">Personal Statement</label>
          <textarea class="form-textarea" id="statement" name="statement" placeholder="Tell us why you want to join this program and what you hope to achieve... (minimum 50 characters)" oninput="updateCount(this)" required><?= htmlspecialchars($existing['statement'] ?? '') ?></textarea>
          <div class="char-count" id="charCount">0 characters</div>
        </div>

        <div class="action-row">
          <button type="submit" class="btn-gold">
            <?= $existing ? '💾 Update Application' : '🚀 Submit Application' ?>
          </button>
          <?php if ($existing): ?>
            <a href="apply.php?withdraw=1" class="btn-danger" onclick="return confirm('Withdraw your application? This cannot be undone.')">🗑 Withdraw</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

  </div>
</main>
<footer>© 2025 EduNova Academy. All Rights Reserved.</footer>
<script>
function updateCount(el) {
  document.getElementById('charCount').textContent = el.value.length + ' characters';
}
// init on load
window.addEventListener('load', () => {
  const ta = document.getElementById('statement');
  if (ta) updateCount(ta);
});
</script>
</body>
</html>
