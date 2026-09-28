<?php
session_start();
include("includes/db.php");

$message = "";

if(isset($_POST['login']))
{
    // Normalize IC Number: trim spaces and remove dashes so
    // "990101-01-5566" and "990101015566" are treated the same.
    $ic_raw = str_replace(['-', ' '], '', trim($_POST['ic_number']));
    $ic = mysqli_real_escape_string($conn, $ic_raw);

    $registration_no = mysqli_real_escape_string($conn, trim($_POST['registration_no']));

    $sql = "SELECT * FROM users
            WHERE REPLACE(REPLACE(ic_number,'-',''),' ','')='$ic'
            AND registration_no='$registration_no'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1)
    {
        $row = mysqli_fetch_assoc($result);

        // Issue a brand-new, unique session ID for this login so it can
        // never collide with another user's session.
        session_regenerate_id(true);

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['full_name'] = $row['full_name'];

        header("Location: user/dashboard.php");
        exit();
    }
    else
    {
        $message = "<div class='alert alert-danger'>
        Invalid IC Number.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Login - JPP EMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--maroon:#800020;--deep:#5c0017;--gold:#d4af37;--gold2:#b9972e;--cream:#fbf7ef;--ink:#302629}
*{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;font-family:'DM Sans',sans-serif;background:linear-gradient(145deg,#180007,#520014) fixed;color:var(--ink);display:flex;align-items:center;justify-content:center;padding:25px;overflow-x:hidden;position:relative}
body:before{content:"";position:fixed;width:360px;height:360px;left:-180px;top:-180px;border-radius:50%;border:1px solid rgba(212,175,55,.28);pointer-events:none}
body:after{content:"";position:fixed;width:420px;height:420px;right:-220px;bottom:-220px;border-radius:50%;border:1px solid rgba(255,255,255,.10);pointer-events:none}
.login-wrap{width:min(520px,100%);position:relative;z-index:2}
.card{border:1px solid #e6dccd;border-radius:26px;background:#fff;box-shadow:0 24px 60px rgba(0,0,0,.45);overflow:hidden}
.header{padding:32px 30px;text-align:center;background:linear-gradient(145deg,#800020,#5c0017);color:#fff;position:relative}
.header:after{content:"";position:absolute;inset:auto 12% 0;height:1px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
.logo{width:82px;height:82px;object-fit:contain;border-radius:50%;background:#fff;padding:7px;margin-bottom:15px;box-shadow:0 0 0 4px rgba(212,175,55,.25),0 10px 25px rgba(64,25,20,.15);animation:logoClassic 7s ease-in-out infinite}
.header h2{font-family:'Playfair Display',serif;font-size:27px;margin-bottom:4px}.header p{opacity:.88;font-size:14px}
.body{padding:32px;color:var(--ink)}
.form-label{font-weight:700;color:#5a4148;margin-bottom:8px;display:inline-block}
.form-control{height:52px;border-radius:14px;border:1px solid #d8d2d0;background:#fff;color:#292124}.form-control::placeholder{color:#9a9093}.form-control:focus{background:#fff;border-color:var(--gold);color:#292124;box-shadow:0 0 0 4px rgba(212,175,55,.13)}
.login-btn{height:52px;border:0;border-radius:14px;background:linear-gradient(135deg,#d4af37,#f1d36a);color:#520016;font-weight:800;box-shadow:0 10px 22px rgba(212,175,55,.2);transition:.3s}.login-btn:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(212,175,55,.28)}
.back{color:var(--maroon);text-decoration:none;font-weight:700}.back:hover{color:#5c0017}.divider{border:0;border-top:1px solid #ece5dc;margin:25px 0}.small-note{font-size:12px;color:#8b7f82;text-align:center}
@keyframes logoClassic{0%,100%{transform:translateY(0) rotate(0deg) scale(1)}25%{transform:translateY(-3px) rotate(-1deg) scale(1.01)}50%{transform:translateY(0) rotate(1deg) scale(1.02)}75%{transform:translateY(-2px) rotate(-.7deg) scale(1.01)}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>
<div class="login-wrap">
<div class="card">
<div class="header">
<img src="assets/images/logo.png" class="logo" alt="JPP Logo">
<h2>JPP Event Management System</h2>
<p>Student Portal</p>
</div>
<div class="body">
<?= $message ?>
<form method="POST" autocomplete="off">
<div class="mb-3">
<label class="form-label">IC Number</label>
<input type="text" name="ic_number" class="form-control" placeholder="Enter IC Number" required autofocus>
</div>
<div class="mb-4">
<label class="form-label">Registration Number</label>
<input type="text" name="registration_no" class="form-control" placeholder="Enter Registration Number" required>
</div>
<button type="submit" name="login" class="login-btn w-100"><i class="fa fa-right-to-bracket"></i> Login</button>
</form>
<hr class="divider">
<div class="text-center">
<a href="user/register.php" class="back">Don't have an account? Register</a>
</div>
<div class="text-center mt-2">
<a href="index.php" class="back">Back to Home</a>
</div>
<div class="text-center mt-3">
<a href="admin/login.php" class="back"><i class="fa fa-user-shield"></i> Login as Admin</a>
</div>
<p class="small-note mt-4 mb-0">Use your IC number and registration number to sign in.</p>
</div>
</div>
</div>
</body>
</html>
