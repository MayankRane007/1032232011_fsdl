<?php
session_start();
require 'db.php';

function clean($conn, $val) {
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($val)));
}

$message = "";
$msg_type = "success";
$edit_data = null;
$active_tab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'insert';

// ── INSERT ──────────────────────────────────────────────────────────
if (isset($_POST['insert'])) {
    $active_tab = 'insert';
    $first   = clean($conn, $_POST['first_name']);
    $last    = clean($conn, $_POST['last_name']);
    $roll    = clean($conn, $_POST['roll_no']);
    $pwd     = $_POST['password'];
    $cpwd    = $_POST['confirm_password'];
    $contact = clean($conn, $_POST['contact']);
    $errors  = [];

    if (empty($first) || !preg_match("/^[a-zA-Z ]+$/", $first)) $errors[] = "First name must contain only letters.";
    if (empty($last)  || !preg_match("/^[a-zA-Z ]+$/", $last))  $errors[] = "Last name must contain only letters.";
    if (empty($roll))                                             $errors[] = "Roll No / ID is required.";
    if (strlen($pwd) < 6)                                         $errors[] = "Password must be at least 6 characters.";
    if ($pwd !== $cpwd)                                           $errors[] = "Passwords do not match.";
    if (!preg_match("/^[0-9]{10}$/", $contact))                  $errors[] = "Contact must be exactly 10 digits.";

    if ($errors) { $message = implode("<br>", $errors); $msg_type = "error"; }
    else {
        $hashed = password_hash($pwd, PASSWORD_DEFAULT);
        $sql = "INSERT INTO students (first_name,last_name,roll_no,password,contact) VALUES ('$first','$last','$roll','$hashed','$contact')";
        if (mysqli_query($conn, $sql)) { $message = "Student <strong>$first $last</strong> added successfully!"; $msg_type = "success"; }
        else                           { $message = mysqli_error($conn); $msg_type = "error"; }
    }
}

// ── DELETE ──────────────────────────────────────────────────────────
if (isset($_POST['delete'])) {
    $active_tab = 'delete';
    $roll = clean($conn, $_POST['del_roll']);
    if (empty($roll)) { $message = "Please enter a Roll No to delete."; $msg_type = "error"; }
    else {
        $sql = "DELETE FROM students WHERE roll_no='$roll'";
        if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) { $message = "Student with Roll No <strong>$roll</strong> deleted."; $msg_type = "success"; }
        else { $message = "No student found with Roll No: <strong>$roll</strong>"; $msg_type = "error"; }
    }
}

// ── SEARCH ──────────────────────────────────────────────────────────
if (isset($_POST['search'])) {
    $active_tab = 'update';
    $roll = clean($conn, $_POST['search_roll']);
    $res  = mysqli_query($conn, "SELECT * FROM students WHERE roll_no='$roll'");
    if ($res && mysqli_num_rows($res) > 0) { $edit_data = mysqli_fetch_assoc($res); $message = "Record found. Edit and save below."; $msg_type = "info"; }
    else { $message = "No student found with Roll No: <strong>$roll</strong>"; $msg_type = "error"; }
}

// ── UPDATE ──────────────────────────────────────────────────────────
if (isset($_POST['update'])) {
    $active_tab = 'update';
    $roll    = clean($conn, $_POST['edit_roll']);
    $first   = clean($conn, $_POST['edit_first']);
    $last    = clean($conn, $_POST['edit_last']);
    $contact = clean($conn, $_POST['edit_contact']);
    $errors  = [];

    if (empty($first) || !preg_match("/^[a-zA-Z ]+$/", $first)) $errors[] = "First name must contain only letters.";
    if (empty($last)  || !preg_match("/^[a-zA-Z ]+$/", $last))  $errors[] = "Last name must contain only letters.";
    if (!preg_match("/^[0-9]{10}$/", $contact))                  $errors[] = "Contact must be exactly 10 digits.";

    if ($errors) { $message = implode("<br>", $errors); $msg_type = "error"; }
    else {
        $sql = "UPDATE students SET first_name='$first',last_name='$last',contact='$contact' WHERE roll_no='$roll'";
        if (mysqli_query($conn, $sql)) { $message = "Student record updated successfully!"; $msg_type = "success"; }
        else                           { $message = mysqli_error($conn); $msg_type = "error"; }
    }
}

// ── VIEW ALL ────────────────────────────────────────────────────────
$all   = mysqli_query($conn, "SELECT id,first_name,last_name,roll_no,contact,created_at FROM students ORDER BY id DESC");
$count = $all ? mysqli_num_rows($all) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Portal – FSD Lab 4</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0a0e1a;
  --surface:#111827;
  --card:#161e2e;
  --border:#1e293b;
  --accent:#00e5c3;
  --accent2:#7c6bff;
  --danger:#ff4d6d;
  --warn:#f59e0b;
  --info:#38bdf8;
  --text:#e2e8f0;
  --muted:#64748b;
  --radius:16px;
  --font-head:'Syne',sans-serif;
  --font-body:'DM Sans',sans-serif;
}
html{scroll-behavior:smooth}
body{
  background:var(--bg);
  color:var(--text);
  font-family:var(--font-body);
  min-height:100vh;
  display:flex;
  overflow-x:hidden;
}

/* SIDEBAR */
.sidebar{
  width:240px;min-height:100vh;
  background:var(--surface);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;
  z-index:100;padding:32px 0 24px;
}
.logo{padding:0 24px 28px;border-bottom:1px solid var(--border)}
.logo-icon{
  width:44px;height:44px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  border-radius:12px;display:flex;align-items:center;
  justify-content:center;font-size:1.3rem;margin-bottom:12px;
  box-shadow:0 0 24px rgba(0,229,195,.3);
}
.logo h2{font-family:var(--font-head);font-size:1.05rem;color:#fff;line-height:1.2}
.logo p{font-size:.72rem;color:var(--muted);margin-top:4px}
.nav{flex:1;padding:20px 12px;display:flex;flex-direction:column;gap:4px}
.nav-item{
  display:flex;align-items:center;gap:12px;
  padding:11px 14px;border-radius:10px;cursor:pointer;
  font-size:.88rem;font-weight:500;color:var(--muted);
  border:none;background:transparent;transition:all .2s;
  width:100%;text-align:left;position:relative;
}
.nav-item:hover{color:var(--text);background:rgba(255,255,255,.04)}
.nav-item.active{color:#fff;background:rgba(0,229,195,.1)}
.nav-item.active::before{
  content:'';position:absolute;left:0;top:22%;bottom:22%;
  width:3px;border-radius:2px;background:var(--accent);
}
.nav-item .icon{font-size:1rem;width:20px;text-align:center}
.badge{
  margin-left:auto;background:var(--accent);color:#000;
  font-size:.68rem;font-weight:700;padding:2px 7px;
  border-radius:20px;
}
.sidebar-footer{padding:16px 24px 0;border-top:1px solid var(--border);font-size:.72rem;color:var(--muted);text-align:center}

/* MAIN */
.main{margin-left:240px;flex:1;min-height:100vh;padding:40px;animation:fadeIn .35s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

/* PAGE HEADER */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:32px}
.page-header h1{font-family:var(--font-head);font-size:2rem;color:#fff;line-height:1.1}
.page-header p{color:var(--muted);font-size:.88rem;margin-top:6px}
.header-stat{
  display:flex;align-items:center;gap:10px;
  background:var(--card);border:1px solid var(--border);
  border-radius:12px;padding:14px 22px;min-width:120px;
}
.header-stat .num{font-family:var(--font-head);font-size:2rem;color:var(--accent);line-height:1}
.header-stat .lbl{font-size:.75rem;color:var(--muted);margin-top:2px}

/* TOAST */
.toast{
  display:flex;align-items:flex-start;gap:12px;
  padding:14px 18px;border-radius:12px;margin-bottom:28px;
  font-size:.88rem;animation:slideIn .3s ease;
  border:1px solid transparent;
}
@keyframes slideIn{from{opacity:0;transform:translateX(-10px)}to{opacity:1;transform:translateX(0)}}
.toast.success{background:rgba(0,229,195,.07);border-color:rgba(0,229,195,.2);color:var(--accent)}
.toast.error  {background:rgba(255,77,109,.07);border-color:rgba(255,77,109,.2);color:var(--danger)}
.toast.info   {background:rgba(56,189,248,.07);border-color:rgba(56,189,248,.2);color:var(--info)}
.toast-icon{font-size:1.1rem}

/* PANELS */
.panel{display:none}
.panel.active{display:block;animation:fadeIn .3s ease}

/* CARD */
.card{
  background:var(--card);border:1px solid var(--border);
  border-radius:var(--radius);padding:28px;margin-bottom:22px;
}
.card-title{
  font-family:var(--font-head);font-size:1rem;color:#fff;
  margin-bottom:22px;display:flex;align-items:center;gap:10px;
}
.card-title .ico{
  width:30px;height:30px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;font-size:.9rem;
}
.ico.g{background:rgba(0,229,195,.12)}
.ico.r{background:rgba(255,77,109,.12)}
.ico.w{background:rgba(245,158,11,.12)}
.ico.b{background:rgba(56,189,248,.12)}

/* FORM */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-group{display:flex;flex-direction:column;gap:7px}
.form-group.full{grid-column:1/-1}
label{font-size:.75rem;font-weight:500;color:var(--muted);letter-spacing:.05em;text-transform:uppercase}
input[type=text],input[type=password],input[type=tel]{
  background:var(--bg);border:1px solid var(--border);
  border-radius:10px;padding:11px 14px;
  color:var(--text);font-family:var(--font-body);
  font-size:.92rem;transition:border-color .2s,box-shadow .2s;outline:none;
}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,229,195,.08)}
input::placeholder{color:var(--muted);font-size:.85rem}
input[readonly]{color:var(--muted);cursor:not-allowed;opacity:.7}
small{font-size:.74rem;color:var(--muted)}

/* BUTTONS */
.btn{
  padding:11px 24px;border:none;border-radius:10px;
  font-family:var(--font-body);font-weight:500;font-size:.9rem;
  cursor:pointer;transition:all .2s;display:inline-flex;
  align-items:center;gap:8px;letter-spacing:.01em;
}
.btn-accent{background:var(--accent);color:#000}
.btn-accent:hover{box-shadow:0 0 24px rgba(0,229,195,.45);transform:translateY(-1px)}
.btn-danger{background:var(--danger);color:#fff}
.btn-danger:hover{box-shadow:0 0 24px rgba(255,77,109,.45);transform:translateY(-1px)}
.btn-warn{background:var(--warn);color:#000}
.btn-warn:hover{box-shadow:0 0 20px rgba(245,158,11,.35);transform:translateY(-1px)}
.btn-info{background:var(--info);color:#000}
.btn-info:hover{box-shadow:0 0 20px rgba(56,189,248,.35);transform:translateY(-1px)}

/* INPUT ROW */
.input-row{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
.input-row .form-group{flex:1;min-width:220px}

/* TABLE */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.87rem}
thead{background:rgba(0,0,0,.3)}
thead th{
  padding:13px 18px;text-align:left;
  font-size:.7rem;text-transform:uppercase;
  letter-spacing:.08em;color:var(--muted);font-weight:500;
}
tbody tr{border-top:1px solid var(--border);transition:background .15s}
tbody tr:hover{background:rgba(255,255,255,.025)}
tbody td{padding:13px 18px}
.roll-badge{
  background:rgba(124,107,255,.15);color:#a89bff;
  font-size:.78rem;font-weight:600;padding:3px 10px;
  border-radius:20px;font-family:monospace;letter-spacing:.04em;
}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state .big{font-size:3rem;margin-bottom:10px}

/* DIVIDER */
.divider{
  display:flex;align-items:center;gap:12px;
  margin:20px 0;color:var(--muted);font-size:.78rem;
}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}

/* WARN BOX */
.warn-box{
  background:rgba(255,77,109,.04);border:1px solid rgba(255,77,109,.18);
  border-radius:12px;padding:16px 18px;font-size:.85rem;color:#ff8fa3;
}

/* GLOWS */
body::before{
  content:'';position:fixed;top:-200px;right:-150px;
  width:500px;height:500px;
  background:radial-gradient(circle,rgba(0,229,195,.06) 0%,transparent 70%);
  pointer-events:none;
}
body::after{
  content:'';position:fixed;bottom:-200px;left:200px;
  width:400px;height:400px;
  background:radial-gradient(circle,rgba(124,107,255,.05) 0%,transparent 70%);
  pointer-events:none;
}

/* RESPONSIVE */
@media(max-width:768px){
  .sidebar{width:100%;min-height:auto;position:relative;padding:16px}
  .logo{border:none;padding-bottom:0;display:flex;align-items:center;gap:10px;margin-bottom:12px}
  .logo-icon{width:32px;height:32px;font-size:1rem;margin:0}
  .nav{flex-direction:row;padding:0;flex-wrap:wrap}
  .nav-item{padding:8px 10px;font-size:.78rem}
  .nav-item.active::before{display:none}
  .main{margin-left:0;padding:20px}
  .form-grid{grid-template-columns:1fr}
  .form-group.full{grid-column:1}
  .page-header{flex-direction:column;gap:16px}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="logo">
    <div class="logo-icon">🎓</div>
    <h2>Student Portal</h2>
    <p>FSD Lab 4 &nbsp;·&nbsp; MIT-WPU</p>
  </div>

  <nav class="nav">
    <button class="nav-item <?= $active_tab==='insert'?'active':'' ?>" onclick="switchTab('insert',this)">
      <span class="icon">＋</span> Insert Student
    </button>
    <button class="nav-item <?= $active_tab==='delete'?'active':'' ?>" onclick="switchTab('delete',this)">
      <span class="icon">✕</span> Delete Record
    </button>
    <button class="nav-item <?= $active_tab==='update'?'active':'' ?>" onclick="switchTab('update',this)">
      <span class="icon">↺</span> Update Record
    </button>
    <button class="nav-item <?= $active_tab==='view'?'active':'' ?>" onclick="switchTab('view',this)">
      <span class="icon">☰</span> View All
      <?php if($count>0): ?><span class="badge"><?= $count ?></span><?php endif; ?>
    </button>
  </nav>

  <div class="sidebar-footer">PHP · MySQL · XAMPP</div>
</aside>

<!-- MAIN CONTENT -->
<main class="main">

  <div class="page-header">
    <div>
      <h1>Student Registration<br>System</h1>
      <p>Insert · Delete · Update · View all records</p>
    </div>
    <div class="header-stat">
      <div>
        <div class="num"><?= $count ?></div>
        <div class="lbl">Students</div>
      </div>
    </div>
  </div>

  <?php if($message): ?>
  <div class="toast <?= $msg_type ?>">
    <div class="toast-icon"><?= $msg_type==='success'?'✓':($msg_type==='error'?'✕':'ℹ') ?></div>
    <div><?= $message ?></div>
  </div>
  <?php endif; ?>

  <!-- ═══ INSERT ═══════════════════════════════════════════ -->
  <div id="panel-insert" class="panel <?= $active_tab==='insert'?'active':'' ?>">
    <div class="card">
      <div class="card-title"><span class="ico g">＋</span> Add New Student</div>
      <form method="POST" onsubmit="return validateInsert()">
        <input type="hidden" name="active_tab" value="insert">
        <div class="form-grid">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" id="fn" placeholder="e.g. Rahul">
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" id="ln" placeholder="e.g. Sharma">
          </div>
          <div class="form-group">
            <label>Roll No / ID</label>
            <input type="text" name="roll_no" id="rn" placeholder="e.g. MIT2024001">
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="contact" id="ct" placeholder="10-digit number">
            <small>Exactly 10 digits, no spaces</small>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="pw" placeholder="Min. 6 characters">
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="cpw" placeholder="Re-enter password">
          </div>
          <div class="form-group full">
            <button class="btn btn-accent" name="insert" type="submit">＋ &nbsp;Insert Student</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══ DELETE ═══════════════════════════════════════════ -->
  <div id="panel-delete" class="panel <?= $active_tab==='delete'?'active':'' ?>">
    <div class="card">
      <div class="card-title"><span class="ico r">✕</span> Delete Student Record</div>
      <form method="POST" onsubmit="return confirm('Permanently delete this student record?')">
        <input type="hidden" name="active_tab" value="delete">
        <div class="input-row">
          <div class="form-group">
            <label>Roll No / ID</label>
            <input type="text" name="del_roll" placeholder="Enter Roll No to delete" required>
          </div>
          <button class="btn btn-danger" name="delete" type="submit">✕ &nbsp;Delete</button>
        </div>
      </form>
    </div>
    <div class="warn-box">
      ⚠ &nbsp;This action is <strong>permanent</strong> and cannot be undone. Double-check the Roll No before proceeding.
    </div>
  </div>

  <!-- ═══ UPDATE ═══════════════════════════════════════════ -->
  <div id="panel-update" class="panel <?= $active_tab==='update'?'active':'' ?>">
    <div class="card">
      <div class="card-title"><span class="ico b">⌕</span> Step 1 — Search Student</div>
      <form method="POST">
        <input type="hidden" name="active_tab" value="update">
        <div class="input-row">
          <div class="form-group">
            <label>Roll No / ID</label>
            <input type="text" name="search_roll" placeholder="Enter Roll No to search" required>
          </div>
          <button class="btn btn-info" name="search" type="submit">⌕ &nbsp;Search</button>
        </div>
      </form>
    </div>

    <?php if($edit_data): ?>
    <div class="divider">Record found — edit below</div>
    <div class="card" style="border-color:rgba(245,158,11,.2)">
      <div class="card-title"><span class="ico w">✎</span> Step 2 — Edit &amp; Save</div>
      <form method="POST" onsubmit="return validateUpdate()">
        <input type="hidden" name="active_tab" value="update">
        <div class="form-grid">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="edit_first" id="ef" value="<?= htmlspecialchars($edit_data['first_name']) ?>">
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="edit_last" id="el" value="<?= htmlspecialchars($edit_data['last_name']) ?>">
          </div>
          <div class="form-group">
            <label>Roll No (locked)</label>
            <input type="text" name="edit_roll" value="<?= htmlspecialchars($edit_data['roll_no']) ?>" readonly>
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="edit_contact" id="ec" value="<?= htmlspecialchars($edit_data['contact']) ?>">
          </div>
          <div class="form-group full">
            <button class="btn btn-warn" name="update" type="submit">↺ &nbsp;Save Changes</button>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <!-- ═══ VIEW ══════════════════════════════════════════════ -->
  <div id="panel-view" class="panel <?= $active_tab==='view'?'active':'' ?>">
    <div class="card" style="padding:0;overflow:hidden">
      <div style="padding:22px 26px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px">
        <span class="ico b" style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center">☰</span>
        <span style="font-family:var(--font-head);color:#fff;font-size:1rem">All Students</span>
        <span style="margin-left:auto;font-size:.78rem;color:var(--muted)"><?= $count ?> record<?= $count!=1?'s':'' ?></span>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Roll No / ID</th>
              <th>Contact</th>
              <th>Registered On</th>
            </tr>
          </thead>
          <tbody>
          <?php
          if ($all && mysqli_num_rows($all) > 0):
            $i = 1;
            while ($row = mysqli_fetch_assoc($all)):
          ?>
            <tr>
              <td style="color:var(--muted)"><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['first_name']) ?></td>
              <td><?= htmlspecialchars($row['last_name']) ?></td>
              <td><span class="roll-badge"><?= htmlspecialchars($row['roll_no']) ?></span></td>
              <td><?= htmlspecialchars($row['contact']) ?></td>
              <td style="color:var(--muted);font-size:.82rem"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="6">
              <div class="empty-state">
                <div class="big">🎓</div>
                <p>No students yet. Use <strong>Insert Student</strong> to add records.</p>
              </div>
            </td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</main>

<script>
function switchTab(id, btn) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
  document.getElementById('panel-' + id).classList.add('active');
  btn.classList.add('active');
}
function validateInsert() {
  const nameR=/^[a-zA-Z ]+$/,telR=/^[0-9]{10}$/;
  const fn=document.getElementById('fn').value.trim();
  const ln=document.getElementById('ln').value.trim();
  const rn=document.getElementById('rn').value.trim();
  const pw=document.getElementById('pw').value;
  const cp=document.getElementById('cpw').value;
  const ct=document.getElementById('ct').value.trim();
  if(!nameR.test(fn)){alert('First name must contain only letters.');return false;}
  if(!nameR.test(ln)){alert('Last name must contain only letters.');return false;}
  if(!rn){alert('Roll No / ID is required.');return false;}
  if(pw.length<6){alert('Password must be at least 6 characters.');return false;}
  if(pw!==cp){alert('Passwords do not match.');return false;}
  if(!telR.test(ct)){alert('Contact must be exactly 10 digits.');return false;}
  return true;
}
function validateUpdate() {
  const nameR=/^[a-zA-Z ]+$/,telR=/^[0-9]{10}$/;
  const fn=document.getElementById('ef').value.trim();
  const ln=document.getElementById('el').value.trim();
  const ct=document.getElementById('ec').value.trim();
  if(!nameR.test(fn)){alert('First name must contain only letters.');return false;}
  if(!nameR.test(ln)){alert('Last name must contain only letters.');return false;}
  if(!telR.test(ct)){alert('Contact must be exactly 10 digits.');return false;}
  return true;
}
</script>
</body>
</html>
