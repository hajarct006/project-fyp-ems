<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

function getCount($conn, $sql)
{
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return (int)($row['total'] ?? 0);
}

$total_admin = getCount($conn, "SELECT COUNT(*) AS total FROM admins");
$total_users = getCount($conn, "SELECT COUNT(*) AS total FROM users");
$total_events = getCount($conn, "SELECT COUNT(*) AS total FROM events");
$total_participants = getCount($conn, "SELECT COUNT(*) AS total FROM registrations");

$total_present = getCount(
    $conn,
    "SELECT COUNT(DISTINCT registration_id) AS total
     FROM attendance
     WHERE LOWER(TRIM(attendance_status))='present'"
);

$recent_events = mysqli_query(
    $conn,
    "SELECT event_id,event_title,event_date,start_time,end_time,venue,quota,status
     FROM events
     WHERE event_date IS NOT NULL
     AND event_date != '0000-00-00'
     ORDER BY event_date DESC, event_id DESC
     LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard - JPP EMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
    --maroon:#800020;
    --deep:#1b000a;
    --wine:#43000f;
    --gold:#d4af37;
    --gold2:#e0c476;
    --cream:#fbf7ef;
    --ink:#271d20;
}

*{
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{
    margin:0;
    font-family:'DM Sans',sans-serif;
    color:var(--cream);
    background:
        radial-gradient(circle at 15% 0%, rgba(212,175,55,.10), transparent 30%),
        radial-gradient(circle at 90% 10%, rgba(128,0,32,.35), transparent 35%),
        linear-gradient(160deg, #170008, #2b000b 45%, #170008);
    background-attachment:fixed;
    overflow-x:hidden;
}

body:before{
    content:"";
    position:fixed;
    inset:0;
    pointer-events:none;
    z-index:0;
    background-image:
        linear-gradient(rgba(100,0,25,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(100,0,25,.025) 1px,transparent 1px);
    background-size:55px 55px;
    mask-image:linear-gradient(to bottom,black,transparent 85%);
}

.main{
    margin-left:250px;
    padding:30px;
    min-height:100vh;
    position:relative;
    z-index:1;
}

.dashboard-hero{
    position:relative;
    overflow:hidden;
    color:#fff;
    border-radius:28px;
    padding:34px;
    margin-bottom:24px;
    background:
        radial-gradient(circle at 85% 20%,rgba(212,175,55,.18),transparent 25%),
        linear-gradient(135deg,#120006,#43000f 48%,#70001e);
    box-shadow:0 25px 60px rgba(44,0,12,.18);
    border:1px solid rgba(212,175,55,.18);
}

.dashboard-hero:before{
    content:"";
    position:absolute;
    inset:0;
    background:
        linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
    background-size:45px 45px;
}

.dashboard-hero:after{
    content:"";
    position:absolute;
    width:250px;
    height:250px;
    border:1px solid rgba(212,175,55,.2);
    border-radius:50%;
    right:-85px;
    top:-90px;
    box-shadow:0 0 0 35px rgba(212,175,55,.035),0 0 0 70px rgba(212,175,55,.02);
}

.hero-content{
    position:relative;
    z-index:2;
}

.kicker{
    display:inline-flex;
    align-items:center;
    gap:8px;
    color:var(--gold2);
    font-weight:800;
    letter-spacing:2px;
    text-transform:uppercase;
    font-size:11px;
    margin-bottom:7px;
}

.kicker:before{
    content:"";
    width:26px;
    height:2px;
    background:var(--gold);
    border-radius:2px;
}

.dashboard-hero h1{
    font-family:'Playfair Display',serif;
    font-size:clamp(30px,4vw,52px);
    margin:0 0 8px;
    line-height:1.1;
}

.dashboard-hero p{
    margin:0;
    color:rgba(255,255,255,.72);
    max-width:680px;
    line-height:1.7;
}

.welcome-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-top:20px;
    padding:9px 15px;
    border-radius:30px;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.15);
    color:#fff;
    font-size:13px;
}

.stat-card{
    height:100%;
    position:relative;
    overflow:hidden;
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:22px;
    padding:23px;
    box-shadow:0 15px 35px rgba(0,0,0,.25);
    transition:.3s;
}

.stat-card:hover{
    transform:translateY(-5px);
    box-shadow:0 22px 45px rgba(55,0,15,.12);
}

.stat-card:after{
    content:"";
    position:absolute;
    width:100px;
    height:100px;
    border:1px solid rgba(212,175,55,.18);
    border-radius:50%;
    right:-40px;
    top:-40px;
}

.stat-icon{
    width:52px;
    height:52px;
    border-radius:15px;
    display:grid;
    place-items:center;
    background:rgba(212,175,55,.14);
    color:var(--gold2);
    font-size:21px;
    margin-bottom:15px;
}

.stat-card h2{
    font-family:'Playfair Display',serif;
    color:var(--cream);
    font-size:34px;
    margin:0;
    font-weight:800;
}

.stat-card p{
    margin:5px 0 0;
    color:rgba(255,255,255,.6);
    font-size:13px;
    font-weight:600;
}

.section-head{
    display:flex;
    justify-content:space-between;
    align-items:end;
    gap:15px;
    margin:30px 0 16px;
}

.section-head h3{
    margin:0;
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-size:25px;
}

.section-head p{
    margin:4px 0 0;
    color:rgba(255,255,255,.55);
    font-size:13px;
}

.section-link{
    color:var(--gold2);
    text-decoration:none;
    font-weight:700;
    font-size:13px;
}

.section-link:hover{
    color:#fff;
}

.event-card{
    height:100%;
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,.25);
    transition:.35s;
}

.event-card:hover{
    transform:translateY(-6px);
    box-shadow:0 25px 55px rgba(55,0,15,.13);
}

.event-top{
    min-height:125px;
    padding:21px;
    color:#fff;
    position:relative;
    overflow:hidden;
    background:linear-gradient(145deg,#650019,#2b000b);
}

.event-top:after{
    content:"";
    position:absolute;
    width:120px;
    height:120px;
    border:1px solid rgba(212,175,55,.22);
    border-radius:50%;
    right:-40px;
    top:-45px;
}

.event-top h5{
    position:relative;
    z-index:2;
    font-family:'Playfair Display',serif;
    font-size:20px;
    line-height:1.35;
    margin:0 0 10px;
}

.event-date{
    position:relative;
    z-index:2;
    font-size:12px;
    color:rgba(255,255,255,.78);
}

.event-body{
    padding:19px;
}

.event-info{
    color:rgba(255,255,255,.62);
    font-size:13px;
    margin:8px 0;
}

.event-info i{
    color:var(--gold2);
    width:19px;
}

.event-status{
    display:inline-flex;
    margin-top:8px;
    padding:6px 11px;
    border-radius:20px;
    font-size:11px;
    font-weight:800;
}

.status-open{
    background:#d1e7dd;
    color:#0f5132;
}

.status-closed{
    background:#f8d7da;
    color:#842029;
}

.event-btn{
    display:block;
    text-align:center;
    text-decoration:none;
    background:var(--gold);
    color:#4b0012;
    border-radius:12px;
    padding:10px;
    margin-top:14px;
    font-weight:800;
    font-size:13px;
}

.event-btn:hover{
    background:#e8c95c;
    color:#4b0012;
}

.table-card{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:22px;
    padding:26px;
    box-shadow:0 15px 35px rgba(0,0,0,.25);
    overflow:hidden;
}

.table-title{
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-size:24px;
    font-weight:700;
}

.table thead th{
    background:linear-gradient(135deg,#650019,#2b000b);
    color:#fff;
    border:0;
    white-space:nowrap;
    font-size:14px;
    padding:14px 16px;
}

.table tbody td{
    font-size:14.5px;
    padding:14px 16px;
    vertical-align:middle;
    color:var(--cream);
    border-color:rgba(255,255,255,.08);
}

.table-hover>tbody>tr:hover>*{
    background:rgba(212,175,55,.08);
    color:var(--cream);
}

.badge-open,.badge-close{
    padding:7px 12px;
    border-radius:20px;
    font-weight:700;
    font-size:11px;
}

.badge-open{
    background:#d1e7dd;
    color:#0f5132;
}

.badge-close{
    background:#f8d7da;
    color:#842029;
}

.empty-box{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    border:1px dashed rgba(212,175,55,.3);
    border-radius:20px;
    padding:45px 20px;
    text-align:center;
    color:rgba(255,255,255,.6);
}

.empty-box i{
    color:var(--gold2);
}

@media(max-width:991px){
    .main{
        margin-left:0;
        padding:78px 18px 25px;
    }
}

@media(max-width:576px){
    .main{
        padding:70px 12px 20px;
    }

    .dashboard-hero{
        padding:26px 21px;
        border-radius:22px;
    }

    .dashboard-hero h1{
        font-size:31px;
    }

    .section-head{
        align-items:flex-start;
        flex-direction:column;
    }

    .stat-card{
        padding:20px;
    }

    .event-top{
        min-height:115px;
    }

    .table-card{
        padding:12px;
    }
}

/* Classic clean light theme */
body{background:#f8f6f1!important;color:#302629!important}
body:before{background-image:linear-gradient(rgba(128,0,32,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(128,0,32,.025) 1px,transparent 1px)!important;mask-image:linear-gradient(to bottom,black,transparent 85%)}
/* Rich maroon + gold stat cards (same palette as student dashboard) */
.stat-card:before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(160deg,rgba(255,255,255,.10),rgba(255,255,255,0) 45%);pointer-events:none}
.stat-card{text-align:center;color:#fff;border:1px solid rgba(128,0,32,.08)!important;box-shadow:0 12px 28px rgba(52,0,18,.12)!important;backdrop-filter:none;padding:28px!important}
.stat-card:after{border:1px solid rgba(255,255,255,.13)!important;width:110px!important;height:110px!important;top:-45px!important}
.stat-card:hover{transform:translateY(-5px);box-shadow:0 20px 38px rgba(52,0,18,.16)!important}
.stat-card h2{color:#fff!important;position:relative;z-index:1}
.stat-card p{color:rgba(255,255,255,.92)!important;font-weight:700!important;position:relative;z-index:1}
.stat-icon{background:rgba(255,255,255,.13)!important;color:#fff!important;margin:0 auto 15px!important;position:relative;z-index:1}
.stat-card{background:linear-gradient(145deg,#7a0022,#3a0010)!important}
.stat-card h2,.stat-card p{color:#fff!important}
.stat-card .stat-icon{background:rgba(255,255,255,.13)!important;color:#fff!important}
.stat-card:after{border:1px solid rgba(255,255,255,.13)!important}
.event-btn{background:var(--maroon)!important;color:#fff!important}.event-btn:hover{background:#5c0017!important;color:#fff!important}
.section-head h3{color:var(--maroon)!important}.section-head p{color:#76696d!important}.section-link{color:var(--maroon)!important}
.event-card{background:#fff!important;border:1px solid #e6dccd!important;box-shadow:0 12px 28px rgba(64,31,18,.08)!important;backdrop-filter:none}.event-body{background:#fff}.event-info{color:#76696d!important}.event-top{background:linear-gradient(145deg,#800020,#5c0017)!important}
.table-card{background:#fff!important;border:1px solid #e6dccd!important;box-shadow:0 12px 28px rgba(64,31,18,.08)!important;backdrop-filter:none}.table-title{color:var(--maroon)!important}.table tbody td{color:#302629!important;border-color:#e5e1dc!important}.table-hover>tbody>tr:hover>*{background:#fffaf0!important;color:#302629!important}.empty-box{background:#fff!important;color:#76696d!important}

</style>
</head>

<body>

<?php include("admin_menu.php"); ?>

<div class="main">

    <section class="dashboard-hero">
        <div class="hero-content">
            <div class="kicker">JPP Event Management System</div>
            <h1>Admin Dashboard</h1>
            <p>
                Manage events, participants, attendance and survey feedback
                from one place.
            </p>

            <div class="welcome-pill">
                <i class="fa fa-user-shield"></i>
                Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
            </div>
        </div>
    </section>

    <div class="row g-3">

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-user-shield"></i></div>
                <h2><?= $total_admin ?></h2>
                <p>Total Admin</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-users"></i></div>
                <h2><?= $total_users ?></h2>
                <p>Total Users</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-calendar-days"></i></div>
                <h2><?= $total_events ?></h2>
                <p>Total Events</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-user-check"></i></div>
                <h2><?= $total_participants ?></h2>
                <p>Total Registrations</p>
            </div>
        </div>

    </div>

    <div class="section-head">
        <div>
            <h3><i class="fa fa-clock-rotate-left me-2"></i>Recent Events</h3>
            <p>Latest event records in the system.</p>
        </div>

        <a href="reports.php" class="section-link">
            Open Reports <i class="fa fa-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="table-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Quota</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php if($recent_events && mysqli_num_rows($recent_events)>0): ?>

                    <?php while($row=mysqli_fetch_assoc($recent_events)): ?>

                        <tr>
                            <td><?= htmlspecialchars($row['event_id']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['event_title']) ?></td>
                            <td><?= date('d M Y',strtotime($row['event_date'])) ?></td>
                            <td><?= htmlspecialchars($row['venue']) ?></td>
                            <td><?= htmlspecialchars($row['quota']) ?></td>
                            <td>
                                <?php if(strtolower(trim($row['status']))==='open'): ?>
                                    <span class="badge-open">Open</span>
                                <?php else: ?>
                                    <span class="badge-close">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No event available.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
