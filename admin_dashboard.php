<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
require_once 'db.php';

$msg = '';
$msgType = 'success';

// ── POST ACTIONS ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Update application status
    if ($action === 'update_status' && isset($_POST['app_id'])) {
        $appId  = (int)$_POST['app_id'];
        $status = $_POST['status'] ?? '';
        $allowed = ['pending','reviewed','accepted','rejected'];
        if (in_array($status, $allowed)) {
            $stmt = $pdo->prepare("UPDATE applications SET status=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$status, $appId]);
            $msg = 'Application status updated successfully.';
        }
    }

    // Delete student (and their applications via CASCADE)
    if ($action === 'delete_user' && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        $stmt   = $pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$userId]);
        $msg = 'Student and all their applications deleted.';
        $msgType = 'warning';
    }

    // Redirect to avoid re-POST on refresh
    $tab = $_POST['tab'] ?? 'applications';
    $qs  = http_build_query(['tab'=>$tab,'msg'=>$msg,'msgType'=>$msgType]);
    header("Location: admin_dashboard.php?$qs");
    exit;
}

// ── READ msg from GET ─────────────────────────────────────────────────────
$msg     = $_GET['msg']     ?? '';
$msgType = $_GET['msgType'] ?? 'success';

// ── ACTIVE TAB ────────────────────────────────────────────────────────────
$tab = $_GET['tab'] ?? 'applications';

// ── FILTERS (applications tab) ────────────────────────────────────────────
$filterStatus = $_GET['status'] ?? 'all';
$search       = trim($_GET['search'] ?? '');

$where  = [];
$params = [];
if ($filterStatus !== 'all') { $where[] = 'a.status = ?'; $params[] = $filterStatus; }
if ($search !== '') {
    $where[] = '(u.name LIKE ? OR u.email LIKE ? OR a.program LIKE ?)';
    $s = "%$search%";
    $params[] = $s; $params[] = $s; $params[] = $s;
}
$whereSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';

$apps = $pdo->prepare("
    SELECT a.*, u.name AS student_name, u.email AS student_email
    FROM applications a
    JOIN users u ON u.id = a.user_id
    $whereSQL
    ORDER BY a.created_at DESC
");
$apps->execute($params);
$applications = $apps->fetchAll(PDO::FETCH_ASSOC);

// ── STUDENTS ──────────────────────────────────────────────────────────────
$userSearch = trim($_GET['usearch'] ?? '');
$userWhere  = $userSearch ? 'WHERE name LIKE ? OR email LIKE ?' : '';
$userParams = $userSearch ? ["%$userSearch%", "%$userSearch%"] : [];
$usersStmt  = $pdo->prepare("
    SELECT u.*, COUNT(a.id) AS app_count
    FROM users u
    LEFT JOIN applications a ON a.user_id = u.id
    $userWhere
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$usersStmt->execute($userParams);
$students = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// ── STATS ─────────────────────────────────────────────────────────────────
$stats = $pdo->query("SELECT status, COUNT(*) as cnt FROM applications GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
$counts = ['pending'=>0,'reviewed'=>0,'accepted'=>0,'rejected'=>0,'total'=>0];
foreach ($stats as $s) { $counts[$s['status']] = (int)$s['cnt']; $counts['total'] += (int)$s['cnt']; }
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduNova – Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--navy:#0a0f2e;--navy2:#0d1235;--gold:#c9a84c;--gold-light:#e8c97e;--white:#fff;--glass:rgba(255,255,255,0.05);--glass-border:rgba(201,168,76,0.2);}
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--navy);color:var(--white);min-height:100vh;}

    /* SIDEBAR */
    .layout{display:flex;min-height:100vh;}
    .sidebar{width:240px;min-height:100vh;background:rgba(255,255,255,0.03);border-right:1px solid var(--glass-border);padding:2rem 1.5rem;display:flex;flex-direction:column;gap:0.3rem;flex-shrink:0;}
    .sidebar-logo{display:flex;align-items:center;gap:0.6rem;margin-bottom:2rem;}
    .logo-icon{width:36px;height:36px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:8px;display:grid;place-items:center;font-size:1.1rem;}
    .logo-text{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:700;}
    .logo-text span{color:var(--gold);}
    .sidebar-label{font-size:0.68rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.25);margin:1.2rem 0 0.5rem 0.3rem;}
    .sidebar-link{display:flex;align-items:center;gap:0.7rem;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.85rem;color:rgba(255,255,255,0.5);text-decoration:none;transition:all 0.2s;}
    .sidebar-link:hover,.sidebar-link.active{background:rgba(201,168,76,0.08);color:var(--gold-light);}
    .sidebar-link .icon{font-size:1rem;width:20px;text-align:center;}
    .sidebar-bottom{margin-top:auto;}
    .logout-btn{display:flex;align-items:center;gap:0.7rem;padding:0.6rem 0.9rem;border-radius:8px;font-size:0.85rem;color:rgba(255,100,100,0.6);text-decoration:none;transition:all 0.2s;cursor:pointer;background:none;border:none;font-family:'DM Sans',sans-serif;width:100%;}
    .logout-btn:hover{background:rgba(255,80,80,0.08);color:#ff8080;}

    /* MAIN */
    .main{flex:1;overflow-x:auto;}
    .topbar{display:flex;align-items:center;justify-content:space-between;padding:1.5rem 2.5rem;border-bottom:1px solid var(--glass-border);background:rgba(255,255,255,0.02);}
    .topbar h1{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;}
    .admin-badge{padding:0.35rem 0.9rem;background:rgba(201,168,76,0.1);border:1px solid var(--glass-border);border-radius:50px;font-size:0.75rem;color:var(--gold-light);letter-spacing:0.5px;}

    .content{padding:2rem 2.5rem;}

    /* TABS */
    .tabs{display:flex;gap:0.5rem;margin-bottom:2rem;border-bottom:1px solid var(--glass-border);padding-bottom:0;}
    .tab-btn{padding:0.6rem 1.4rem;border:none;background:transparent;color:rgba(255,255,255,0.4);font-family:'DM Sans',sans-serif;font-size:0.88rem;font-weight:600;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all 0.2s;}
    .tab-btn.active{color:var(--gold);border-bottom-color:var(--gold);}
    .tab-btn:hover{color:var(--gold-light);}
    .tab-pane{display:none;}
    .tab-pane.active{display:block;}

    /* STAT CARDS */
    .stat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:2rem;}
    .stat-card{background:rgba(255,255,255,0.04);border:1px solid var(--glass-border);border-radius:12px;padding:1.2rem 1.4rem;}
    .stat-card-label{font-size:0.7rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.35);margin-bottom:0.4rem;}
    .stat-card-num{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:700;line-height:1;}
    .stat-card-num.gold{color:var(--gold);}
    .stat-card-num.yellow{color:#ffd080;}
    .stat-card-num.blue{color:#90c8ff;}
    .stat-card-num.green{color:#7dffb0;}
    .stat-card-num.red{color:#ff9090;}

    /* FILTERS */
    .filter-bar{display:flex;gap:0.8rem;align-items:center;flex-wrap:wrap;margin-bottom:1.5rem;}
    .filter-btn{padding:0.4rem 1rem;border-radius:50px;font-size:0.78rem;font-weight:600;border:1px solid var(--glass-border);background:transparent;color:rgba(255,255,255,0.45);cursor:pointer;transition:all 0.2s;font-family:'DM Sans',sans-serif;text-decoration:none;display:inline-block;}
    .filter-btn:hover,.filter-btn.active{background:rgba(201,168,76,0.12);border-color:var(--gold);color:var(--gold-light);}
    .search-input{padding:0.45rem 1rem;background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);border-radius:8px;color:#fff;font-family:'DM Sans',sans-serif;font-size:0.84rem;outline:none;min-width:220px;}
    .search-input::placeholder{color:rgba(255,255,255,0.2);}
    .search-input:focus{border-color:var(--gold);}

    /* TOAST */
    .toast{display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;border-radius:8px;font-size:0.82rem;margin-bottom:1.2rem;}
    .toast.success{background:rgba(50,200,100,0.1);border:1px solid rgba(50,200,100,0.3);color:#7dffb0;}
    .toast.warning{background:rgba(220,100,50,0.1);border:1px solid rgba(220,100,50,0.3);color:#ffb080;}

    /* TABLE */
    .table-wrap{background:rgba(255,255,255,0.03);border:1px solid var(--glass-border);border-radius:16px;overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead tr{background:rgba(201,168,76,0.06);border-bottom:1px solid var(--glass-border);}
    th{padding:0.9rem 1.2rem;text-align:left;font-size:0.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.35);}
    tbody tr{border-bottom:1px solid rgba(255,255,255,0.04);transition:background 0.2s;}
    tbody tr:hover{background:rgba(255,255,255,0.025);}
    tbody tr:last-child{border-bottom:none;}
    td{padding:1rem 1.2rem;font-size:0.84rem;vertical-align:middle;}
    .student-name{font-weight:600;color:var(--white);margin-bottom:0.15rem;}
    .student-email{font-size:0.75rem;color:rgba(255,255,255,0.35);}
    .program-cell{color:rgba(255,255,255,0.75);font-weight:500;}
    .degree-cell{font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:0.15rem;}
    .date-cell{font-size:0.78rem;color:rgba(255,255,255,0.35);}

    /* BADGES */
    .badge{padding:0.25rem 0.75rem;border-radius:50px;font-size:0.68rem;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;white-space:nowrap;}
    .badge-pending {background:rgba(200,150,50,0.15);border:1px solid rgba(200,150,50,0.3);color:#ffd080;}
    .badge-reviewed{background:rgba(80,130,220,0.15);border:1px solid rgba(80,130,220,0.3);color:#90c8ff;}
    .badge-accepted{background:rgba(50,200,100,0.15);border:1px solid rgba(50,200,100,0.3);color:#7dffb0;}
    .badge-rejected{background:rgba(200,80,80,0.15); border:1px solid rgba(200,80,80,0.3); color:#ff9090;}

    /* ACTION BUTTONS */
    .actions{display:flex;gap:0.4rem;flex-wrap:wrap;}
    .act-btn{padding:0.3rem 0.75rem;border-radius:6px;font-size:0.73rem;font-weight:600;cursor:pointer;border:none;font-family:'DM Sans',sans-serif;transition:all 0.2s;}
    .act-accept{background:rgba(50,200,100,0.12);color:#7dffb0;border:1px solid rgba(50,200,100,0.25);}
    .act-accept:hover{background:rgba(50,200,100,0.22);}
    .act-reject{background:rgba(200,80,80,0.12);color:#ff9090;border:1px solid rgba(200,80,80,0.25);}
    .act-reject:hover{background:rgba(200,80,80,0.22);}
    .act-review{background:rgba(80,130,220,0.12);color:#90c8ff;border:1px solid rgba(80,130,220,0.25);}
    .act-review:hover{background:rgba(80,130,220,0.22);}
    .act-pending{background:rgba(200,150,50,0.1);color:#ffd080;border:1px solid rgba(200,150,50,0.2);}
    .act-pending:hover{background:rgba(200,150,50,0.2);}
    .act-view{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.1);}
    .act-view:hover{background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.8);}
    .act-delete{background:rgba(200,50,50,0.12);color:#ff7070;border:1px solid rgba(200,50,50,0.25);}
    .act-delete:hover{background:rgba(200,50,50,0.25);}

    /* EMPTY STATE */
    .empty{text-align:center;padding:3rem;color:rgba(255,255,255,0.25);}
    .empty-icon{font-size:2.5rem;margin-bottom:0.8rem;}

    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:200;align-items:center;justify-content:center;padding:1rem;}
    .modal-overlay.open{display:flex;}
    .modal{background:#0e1330;border:1px solid var(--glass-border);border-radius:20px;padding:2.5rem;max-width:580px;width:100%;max-height:90vh;overflow-y:auto;position:relative;}
    .modal-close{position:absolute;top:1rem;right:1.2rem;background:none;border:none;color:rgba(255,255,255,0.4);font-size:1.3rem;cursor:pointer;transition:color 0.2s;}
    .modal-close:hover{color:#fff;}
    .modal-title{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:700;margin-bottom:1.5rem;}
    .modal-field{margin-bottom:1rem;}
    .modal-field-label{font-size:0.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.3);margin-bottom:0.3rem;}
    .modal-field-value{font-size:0.88rem;color:rgba(255,255,255,0.75);padding:0.6rem 0.9rem;background:rgba(255,255,255,0.04);border-radius:8px;line-height:1.5;}
    .modal-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}

    /* CONFIRM MODAL */
    .confirm-modal{background:#0e1330;border:1px solid rgba(200,50,50,0.3);border-radius:16px;padding:2rem;max-width:400px;width:100%;text-align:center;}
    .confirm-title{font-size:1.1rem;font-weight:600;margin-bottom:0.75rem;color:#ff9090;}
    .confirm-msg{font-size:0.85rem;color:rgba(255,255,255,0.5);margin-bottom:1.5rem;line-height:1.6;}
    .confirm-btns{display:flex;gap:0.75rem;justify-content:center;}
    .confirm-cancel{padding:0.5rem 1.3rem;border-radius:8px;border:1px solid var(--glass-border);background:transparent;color:rgba(255,255,255,0.5);cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.85rem;}
    .confirm-cancel:hover{color:#fff;}
    .confirm-delete-btn{padding:0.5rem 1.3rem;border-radius:8px;border:none;background:rgba(200,50,50,0.2);color:#ff7070;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.85rem;font-weight:600;}
    .confirm-delete-btn:hover{background:rgba(200,50,50,0.35);}

    @media(max-width:900px){.stat-grid{grid-template-columns:repeat(3,1fr);}.sidebar{display:none;}.topbar h1{font-size:1.2rem;}}
  </style>
</head>
<body>
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">🎓</div>
      <div class="logo-text">Edu<span>Nova</span></div>
    </div>
    <div class="sidebar-label">Dashboard</div>
    <a href="admin_dashboard.php?tab=applications" class="sidebar-link <?= $tab==='applications'?'active':'' ?>"><span class="icon">📋</span> Applications</a>
    <a href="admin_dashboard.php?tab=students"     class="sidebar-link <?= $tab==='students'?'active':'' ?>"><span class="icon">👩‍🎓</span> Students</a>
    <div class="sidebar-label">Filter Apps</div>
    <a href="admin_dashboard.php?tab=applications&status=pending"  class="sidebar-link"><span class="icon">⏳</span> Pending</a>
    <a href="admin_dashboard.php?tab=applications&status=reviewed" class="sidebar-link"><span class="icon">🔍</span> Reviewed</a>
    <a href="admin_dashboard.php?tab=applications&status=accepted" class="sidebar-link"><span class="icon">✅</span> Accepted</a>
    <a href="admin_dashboard.php?tab=applications&status=rejected" class="sidebar-link"><span class="icon">❌</span> Rejected</a>
    <div class="sidebar-label">Links</div>
    <a href="index.php" target="_blank" class="sidebar-link"><span class="icon">🌐</span> View Site</a>
    <div class="sidebar-bottom">
      <a href="admin_logout.php" class="logout-btn"><span class="icon">🚪</span> Logout</a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <h1>Admin Dashboard</h1>
      <div class="admin-badge">🔐 Administrator</div>
    </div>

    <div class="content">

      <?php if ($msg): ?>
        <div class="toast <?= $msgType === 'warning' ? 'warning' : 'success' ?>">
          <?= $msgType === 'warning' ? '⚠️' : '✅' ?> <?= htmlspecialchars($msg) ?>
        </div>
      <?php endif; ?>

      <!-- STATS -->
      <div class="stat-grid">
        <div class="stat-card"><div class="stat-card-label">Total Students</div><div class="stat-card-num gold"><?= $totalUsers ?></div></div>
        <div class="stat-card"><div class="stat-card-label">Applications</div><div class="stat-card-num gold"><?= $counts['total'] ?></div></div>
        <div class="stat-card"><div class="stat-card-label">Pending</div><div class="stat-card-num yellow"><?= $counts['pending'] ?></div></div>
        <div class="stat-card"><div class="stat-card-label">Accepted</div><div class="stat-card-num green"><?= $counts['accepted'] ?></div></div>
        <div class="stat-card"><div class="stat-card-label">Rejected</div><div class="stat-card-num red"><?= $counts['rejected'] ?></div></div>
      </div>

      <!-- TABS -->
      <div class="tabs">
        <button class="tab-btn <?= $tab==='applications'?'active':'' ?>" onclick="switchTab('applications')">📋 Applications</button>
        <button class="tab-btn <?= $tab==='students'?'active':'' ?>" onclick="switchTab('students')">👩‍🎓 Students</button>
      </div>

      <!-- ── APPLICATIONS TAB ── -->
      <div id="tab-applications" class="tab-pane <?= $tab==='applications'?'active':'' ?>">
        <form method="GET" action="admin_dashboard.php">
          <input type="hidden" name="tab" value="applications"/>
          <div class="filter-bar">
            <?php foreach(['all','pending','reviewed','accepted','rejected'] as $s): ?>
              <button type="submit" name="status" value="<?=$s?>" class="filter-btn <?= $filterStatus===$s?'active':'' ?>">
                <?= ucfirst($s) ?> <?= $s!=='all' ? '('.$counts[$s].')' : '' ?>
              </button>
            <?php endforeach; ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>"/>
            <input class="search-input" type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search name, email, program..."/>
            <button type="submit" class="filter-btn">Search</button>
            <?php if ($search): ?><a href="admin_dashboard.php?tab=applications" class="filter-btn">✕ Clear</a><?php endif; ?>
          </div>
        </form>

        <div class="table-wrap">
          <?php if (empty($applications)): ?>
            <div class="empty"><div class="empty-icon">📭</div><p>No applications found.</p></div>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>#</th><th>Student</th><th>Program</th><th>Status</th><th>Applied</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($applications as $app): ?>
              <tr>
                <td style="color:rgba(255,255,255,0.25);font-size:0.75rem;"><?= str_pad($app['id'],4,'0',STR_PAD_LEFT) ?></td>
                <td>
                  <div class="student-name"><?= htmlspecialchars($app['student_name']) ?></div>
                  <div class="student-email"><?= htmlspecialchars($app['student_email']) ?></div>
                </td>
                <td>
                  <div class="program-cell"><?= htmlspecialchars($app['program']) ?></div>
                  <div class="degree-cell"><?= htmlspecialchars($app['degree']) ?></div>
                </td>
                <td><span class="badge badge-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span></td>
                <td class="date-cell"><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                <td>
                  <div class="actions">
                    <button class="act-btn act-view" onclick='openModal(<?= htmlspecialchars(json_encode($app)) ?>)'>👁 View</button>
                    <!-- Status actions -->
                    <form method="POST" style="display:contents">
                      <input type="hidden" name="tab" value="applications"/>
                      <input type="hidden" name="action" value="update_status"/>
                      <input type="hidden" name="app_id" value="<?= $app['id'] ?>"/>
                      <?php if ($app['status'] !== 'accepted'): ?>
                        <button type="submit" name="status" value="accepted" class="act-btn act-accept">✓ Accept</button>
                      <?php endif; ?>
                      <?php if ($app['status'] !== 'rejected'): ?>
                        <button type="submit" name="status" value="rejected" class="act-btn act-reject">✕ Reject</button>
                      <?php endif; ?>
                      <?php if ($app['status'] !== 'reviewed'): ?>
                        <button type="submit" name="status" value="reviewed" class="act-btn act-review">🔍 Review</button>
                      <?php endif; ?>
                      <?php if ($app['status'] !== 'pending'): ?>
                        <button type="submit" name="status" value="pending" class="act-btn act-pending">⏳ Pending</button>
                      <?php endif; ?>
                    </form>

                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div><!-- /applications tab -->

      <!-- ── STUDENTS TAB ── -->
      <div id="tab-students" class="tab-pane <?= $tab==='students'?'active':'' ?>">
        <form method="GET" action="admin_dashboard.php">
          <input type="hidden" name="tab" value="students"/>
          <div class="filter-bar">
            <input class="search-input" type="text" name="usearch" value="<?= htmlspecialchars($userSearch) ?>" placeholder="🔍 Search name or email..."/>
            <button type="submit" class="filter-btn">Search</button>
            <?php if ($userSearch): ?><a href="admin_dashboard.php?tab=students" class="filter-btn">✕ Clear</a><?php endif; ?>
          </div>
        </form>

        <div class="table-wrap">
          <?php if (empty($students)): ?>
            <div class="empty"><div class="empty-icon">👥</div><p>No students found.</p></div>
          <?php else: ?>
          <table>
            <thead>
              <tr><th>#</th><th>Name</th><th>Email</th><th>Applications</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($students as $stu): ?>
              <tr>
                <td style="color:rgba(255,255,255,0.25);font-size:0.75rem;"><?= str_pad($stu['id'],4,'0',STR_PAD_LEFT) ?></td>
                <td><div class="student-name"><?= htmlspecialchars($stu['name']) ?></div></td>
                <td><div class="student-email" style="font-size:0.84rem;color:rgba(255,255,255,0.6)"><?= htmlspecialchars($stu['email']) ?></div></td>
                <td><span class="badge badge-reviewed" style="background:rgba(201,168,76,0.1);border-color:rgba(201,168,76,0.3);color:var(--gold-light);"><?= $stu['app_count'] ?> app<?= $stu['app_count'] != 1 ? 's' : '' ?></span></td>
                <td class="date-cell"><?= date('d M Y', strtotime($stu['created_at'])) ?></td>
                <td>
                  <div class="actions">
                    <a href="admin_dashboard.php?tab=applications&search=<?= urlencode($stu['email']) ?>" class="act-btn act-view" style="text-decoration:none;">📋 View Apps</a>
                    <button class="act-btn act-delete" onclick="confirmDeleteUser(<?= $stu['id'] ?>, '<?= htmlspecialchars(addslashes($stu['name'])) ?>', <?= (int)$stu['app_count'] ?>)">🗑 Delete</button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div><!-- /students tab -->

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /layout -->

<!-- VIEW APPLICATION MODAL -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div class="modal-title" id="modalTitle">Application Details</div>
    <div id="modalBody"></div>
  </div>
</div>

<!-- CONFIRM DELETE MODAL -->
<div class="modal-overlay" id="confirmOverlay">
  <div class="confirm-modal">
    <div class="confirm-title" id="confirmTitle">⚠️ Confirm Delete</div>
    <div class="confirm-msg" id="confirmMsg"></div>
    <div class="confirm-btns">
      <button class="confirm-cancel" onclick="closeConfirm()">Cancel</button>
      <form method="POST" id="confirmForm" style="display:inline">
        <input type="hidden" id="confirmAction" name="action" value=""/>
        <input type="hidden" id="confirmId" name="" value=""/>
        <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>"/>
        <button type="submit" class="confirm-delete-btn">Yes, Delete</button>
      </form>
    </div>
  </div>
</div>

<script>
// TAB SWITCH
function switchTab(name) {
  window.location.href = 'admin_dashboard.php?tab=' + name;
}

// APPLICATION DETAIL MODAL
function openModal(app) {
  document.getElementById('modalTitle').textContent = app.student_name + ' — ' + app.program;
  document.getElementById('modalBody').innerHTML = `
    <div class="modal-grid">
      <div class="modal-field"><div class="modal-field-label">Email</div><div class="modal-field-value">${app.student_email}</div></div>
      <div class="modal-field"><div class="modal-field-label">Phone</div><div class="modal-field-value">${app.phone}</div></div>
      <div class="modal-field"><div class="modal-field-label">Date of Birth</div><div class="modal-field-value">${app.dob}</div></div>
      <div class="modal-field"><div class="modal-field-label">Degree</div><div class="modal-field-value">${app.degree}</div></div>
      <div class="modal-field"><div class="modal-field-label">Previous School</div><div class="modal-field-value">${app.prev_school}</div></div>
      <div class="modal-field"><div class="modal-field-label">Grade / GPA</div><div class="modal-field-value">${app.prev_grade}</div></div>
    </div>
    <div class="modal-field"><div class="modal-field-label">Address</div><div class="modal-field-value">${app.address}</div></div>
    <div class="modal-field"><div class="modal-field-label">Personal Statement</div><div class="modal-field-value" style="white-space:pre-line">${app.statement}</div></div>
    <div class="modal-field"><div class="modal-field-label">Status</div><div class="modal-field-value"><span class="badge badge-${app.status}">${app.status.charAt(0).toUpperCase()+app.status.slice(1)}</span></div></div>
    <div class="modal-field"><div class="modal-field-label">Applied On</div><div class="modal-field-value">${app.created_at}</div></div>
  `;
  document.getElementById('modalOverlay').classList.add('open');
}
function closeModal() { document.getElementById('modalOverlay').classList.remove('open'); }
document.getElementById('modalOverlay').addEventListener('click', function(e){ if(e.target===this) closeModal(); });

// CONFIRM DELETE STUDENT
function confirmDeleteUser(userId, name, appCount) {
  document.getElementById('confirmTitle').textContent = '⚠️ Delete Student';
  document.getElementById('confirmMsg').textContent = 'Are you sure you want to delete "' + name + '"?' + (appCount > 0 ? ' This will also delete their ' + appCount + ' application(s).' : '') + ' This cannot be undone.';
  const form = document.getElementById('confirmForm');
  document.getElementById('confirmAction').value = 'delete_user';
  let inp = form.querySelector('input[name="user_id"]');
  if (!inp) { inp = document.createElement('input'); inp.type='hidden'; inp.name='user_id'; form.appendChild(inp); }
  inp.value = userId;
  let aid = form.querySelector('input[name="app_id"]');
  if (aid) aid.remove();
  document.getElementById('confirmOverlay').classList.add('open');
}

function closeConfirm() { document.getElementById('confirmOverlay').classList.remove('open'); }
document.getElementById('confirmOverlay').addEventListener('click', function(e){ if(e.target===this) closeConfirm(); });
</script>
</body>
</html>
