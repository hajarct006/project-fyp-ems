<?php
session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

$total_events = 0;
$total_registrations = 0;
$total_present = 0;
$total_survey_responses = 0;

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM events
    "
);

if($result)
{
    $row = mysqli_fetch_assoc($result);
    $total_events = (int)($row['total'] ?? 0);
}

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM registrations
    WHERE LOWER(TRIM(status))='registered'
    "
);

if($result)
{
    $row = mysqli_fetch_assoc($result);
    $total_registrations = (int)($row['total'] ?? 0);
}

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(DISTINCT registration_id) AS total
    FROM attendance
    WHERE LOWER(TRIM(attendance_status))='present'
    "
);

if($result)
{
    $row = mysqli_fetch_assoc($result);
    $total_present = (int)($row['total'] ?? 0);
}

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(DISTINCT CONCAT(user_id,'-',event_id)) AS total
    FROM survey_confirmations
    "
);

if($result)
{
    $row = mysqli_fetch_assoc($result);
    $total_survey_responses = (int)($row['total'] ?? 0);
}


/*
|--------------------------------------------------------------------------
| EVENT REPORT DATA
|--------------------------------------------------------------------------
| Survey response is measured against participants who actually attended,
| because the current survey page only allows Present participants to
| submit the survey.
|--------------------------------------------------------------------------
*/

$events_sql = "
    SELECT
        e.event_id,
        e.event_title,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue,
        e.quota,

        COUNT(DISTINCT CASE
            WHEN LOWER(TRIM(r.status))='registered'
            THEN r.registration_id
        END) AS total_participants,

        COUNT(DISTINCT CASE
            WHEN LOWER(TRIM(r.status))='registered'
            AND LOWER(TRIM(a.attendance_status))='present'
            THEN r.registration_id
        END) AS total_present,

        COUNT(DISTINCT CASE
            WHEN LOWER(TRIM(r.status))='registered'
            AND sc.confirmation_id IS NOT NULL
            THEN r.registration_id
        END) AS total_survey_responses

    FROM events e

    LEFT JOIN registrations r
        ON e.event_id = r.event_id

    LEFT JOIN attendance a
        ON r.registration_id = a.registration_id

    LEFT JOIN survey_confirmations sc
        ON sc.user_id = r.user_id
        AND sc.event_id = r.event_id

    GROUP BY
        e.event_id,
        e.event_title,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue,
        e.quota

    ORDER BY
        e.event_date DESC,
        e.event_id DESC
";

$events_result = mysqli_query($conn,$events_sql);

if(!$events_result)
{
    die("Report query failed: " . htmlspecialchars(mysqli_error($conn)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width,initial-scale=1"
>

<title>Event Reports - JPP EMS</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>

<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
    rel="stylesheet"
>

<style>

:root{
    --maroon:#800020;
    --deep:#1b000a;
    --wine:#43000f;
    --gold:#d4af37;
    --gold2:#e0c476;
    --cream:#fbf7ef;
}

*{
    box-sizing:border-box;
    font-family:'DM Sans',sans-serif;
}

body{
    margin:0;
    background:
        radial-gradient(circle at 15% 0%, rgba(212,175,55,.10), transparent 30%),
        radial-gradient(circle at 90% 10%, rgba(128,0,32,.35), transparent 35%),
        linear-gradient(160deg, #170008, #2b000b 45%, #170008);
    background-attachment:fixed;
    color:var(--cream);
}

.main{
    margin-left:250px;
    padding:30px;
    min-height:100vh;
}

.topbar{
    position:relative;
    overflow:hidden;
    color:#fff;
    background:
        radial-gradient(circle at 88% 15%,rgba(212,175,55,.18),transparent 25%),
        linear-gradient(135deg,#120006,#43000f 48%,#70001e);
    border-radius:25px;
    padding:28px;
    box-shadow:0 20px 45px rgba(44,0,12,.17);
    margin-bottom:22px;
    border:1px solid rgba(212,175,55,.16);
}

.topbar:after{
    content:"";
    position:absolute;
    width:160px;
    height:160px;
    border:1px solid rgba(212,175,55,.18);
    border-radius:50%;
    right:-50px;
    top:-60px;
}

.topbar h3{
    position:relative;
    z-index:2;
    margin:0;
    font-family:'Playfair Display',serif;
    font-weight:700;
    font-size:30px;
}

.topbar p{
    position:relative;
    z-index:2;
    margin:7px 0 0;
    color:rgba(255,255,255,.72);
    font-size:14px;
}

.stat{
    height:100%;
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:20px;
    padding:20px;
    box-shadow:0 12px 28px rgba(0,0,0,.25);
    display:flex;
    align-items:center;
    gap:15px;
    transition:.3s;
}

.stat:hover{
    transform:translateY(-4px);
}

.stat-icon{
    width:52px;
    height:52px;
    flex:0 0 52px;
    border-radius:15px;
    background:rgba(212,175,55,.14);
    color:var(--gold2);
    display:grid;
    place-items:center;
    font-size:21px;
}

.stat h2{
    margin:0;
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-size:30px;
    font-weight:800;
}

.stat p{
    margin:3px 0 0;
    color:rgba(255,255,255,.55);
    font-size:12px;
    font-weight:600;
}

.section-title{
    margin:28px 0 16px;
}

.section-title h4{
    margin:0;
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-weight:700;
}

.section-title p{
    margin:4px 0 0;
    color:rgba(255,255,255,.55);
    font-size:13px;
}

.report-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:18px;
}

.event-card{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border-radius:22px;
    border:1px solid rgba(212,175,55,.18);
    overflow:hidden;
    box-shadow:0 12px 30px rgba(0,0,0,.25);
    transition:.35s;
    height:100%;
}

.event-card:hover{
    transform:translateY(-6px);
    box-shadow:0 22px 45px rgba(50,0,15,.13);
}

.event-top{
    padding:22px;
    color:#fff;
    min-height:125px;
    position:relative;
    overflow:hidden;
    background:linear-gradient(145deg,#650019,#2b000b);
}

.event-top:after{
    content:"";
    position:absolute;
    width:120px;
    height:120px;
    border:1px solid rgba(212,175,55,.2);
    border-radius:50%;
    right:-40px;
    top:-40px;
}

.event-top h5{
    position:relative;
    z-index:2;
    margin:0 0 9px;
    font-family:'Playfair Display',serif;
    font-size:20px;
    line-height:1.35;
}

.event-date{
    position:relative;
    z-index:2;
    font-size:12px;
    color:rgba(255,255,255,.76);
}

.event-body{
    padding:19px;
}

.info{
    color:rgba(255,255,255,.6);
    font-size:13px;
    margin-bottom:8px;
}

.info i{
    color:var(--gold2);
    width:20px;
}

.metrics{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:8px;
    margin:16px 0;
}

.metric{
    background:var(--cream);
    border:1px solid rgba(128,0,32,.08);
    border-radius:13px;
    padding:12px 8px;
    text-align:center;
}

.metric strong{
    display:block;
    color:var(--maroon);
    font-size:19px;
    font-weight:800;
}

.metric small{
    color:#8a7c80;
    font-size:10px;
    line-height:1.3;
}

.progress-label{
    display:flex;
    justify-content:space-between;
    font-size:11px;
    color:rgba(255,255,255,.6);
    margin-bottom:5px;
}

.progress{
    height:8px;
    background:rgba(255,255,255,.08);
    border-radius:20px;
}

.progress-bar{
    background:linear-gradient(90deg,#800020,#5c0017);
    border-radius:20px;
}

.pending{
    margin-top:9px;
    padding:8px 10px;
    border-radius:10px;
    background:#fff8e1;
    color:#6b5600;
    font-size:11px;
}

.complete{
    background:#eaf7ef;
    color:#166534;
}

.view-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    width:100%;
    border:0;
    border-radius:12px;
    padding:11px;
    margin-top:13px;
    background:var(--maroon);
    color:#fff;
    font-weight:800;
    text-decoration:none;
    transition:.25s;
}

.view-btn:hover{
    background:#5c0017;
    color:#fff;
}

.empty{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    border-radius:22px;
    padding:55px;
    text-align:center;
    border:1px dashed rgba(212,175,55,.3);
    color:rgba(255,255,255,.6);
}

.empty i{
    color:var(--gold2);
}

@media(max-width:991px){
    .main{
        margin-left:0;
        padding:80px 18px 25px;
    }
}

@media(max-width:576px){
    .main{
        padding:72px 12px 20px;
    }

    .topbar{
        padding:23px 20px;
    }

    .topbar h3{
        font-size:25px;
    }

    .metrics{
        gap:6px;
    }

    .metric{
        padding:10px 5px;
    }
}


/* Classic clean light theme */
body{background:#f8f6f1!important;color:#302629!important}
.topbar{background:#fffdf9!important;color:#4a3b3f!important;border:1px solid #e6dccd!important;border-left:4px solid var(--gold);box-shadow:0 14px 32px rgba(64,31,18,.08)!important}.topbar h3{color:var(--maroon)!important}.topbar p{color:#76696d!important}
/* Rich maroon + gold stat cards (same palette as student dashboard) */
.stat:before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(160deg,rgba(255,255,255,.10),rgba(255,255,255,0) 45%);pointer-events:none}
.stat{position:relative;overflow:hidden}
.stat{flex-direction:column;text-align:center;justify-content:center;gap:0!important;padding:26px 20px!important;color:#fff;border:1px solid rgba(128,0,32,.08)!important;box-shadow:0 12px 28px rgba(52,0,18,.12)!important;backdrop-filter:none}
.stat:hover{box-shadow:0 20px 38px rgba(52,0,18,.16)!important}
.stat h2{color:#fff!important}.stat p{color:rgba(255,255,255,.92)!important;font-weight:700!important}
.stat-icon{background:rgba(255,255,255,.13)!important;color:#fff!important;margin-bottom:14px}
.section-title h4{color:var(--maroon)!important}.section-title p{color:#76696d!important}.event-card{background:#fff!important;border:1px solid #e6dccd!important;box-shadow:0 12px 28px rgba(64,31,18,.08)!important;backdrop-filter:none}.event-body{background:#fff}.info{color:#76696d!important}.progress-label{color:#76696d!important}.progress{background:#eee8e4!important}.empty{background:#fff!important;color:#76696d!important}


/* event name header: maroon (as before) */
.event-top{background:linear-gradient(145deg,#800020,#3a0010)!important;color:#fff!important;border-bottom:0!important}
.event-top:after{border-color:rgba(255,255,255,.16)!important}
.event-top h5{color:#fff!important}
.event-date{color:rgba(255,255,255,.82)!important}
.info i{color:#800020!important}
</style>
</head>

<body>

<?php include("admin_menu.php"); ?>

<div class="main">

    <div class="topbar">

        <h3>
            <i class="fa fa-chart-line"></i>
            Event Reports
        </h3>

        <p>
            Select an event and click View Full Report to see its details.
        </p>

    </div>

    <div class="section-title">
        <h4>
            <i class="fa fa-chart-column me-2"></i>
            Events
        </h4>

        <p>
            Registration, attendance and survey details are shown inside each event report.
        </p>
    </div>

    <?php if(mysqli_num_rows($events_result)>0): ?>

        <div class="report-grid">

        <?php while($row=mysqli_fetch_assoc($events_result)): ?>

            

            <div class="event-card">

                <div class="event-top">

                    <h5>
                        <?= htmlspecialchars($row['event_title']) ?>
                    </h5>

                    <div class="event-date">
                        <i class="fa fa-calendar"></i>
                        <?= date('d M Y',strtotime($row['event_date'])) ?>
                    </div>

                </div>

                <div class="event-body">

                    <div class="info">
                        <i class="fa fa-clock"></i>
                        <?= date('h:i A',strtotime($row['start_time'])) ?>
                        -
                        <?= date('h:i A',strtotime($row['end_time'])) ?>
                    </div>

                    <div class="info">
                        <i class="fa fa-location-dot"></i>
                        <?= htmlspecialchars($row['venue']) ?>
                    </div>

                    <a
                        class="view-btn"
                        href="report_event.php?event_id=<?= urlencode($row['event_id']) ?>"
                    >
                        <i class="fa fa-eye"></i>
                        View Full Report
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty">

            <i class="fa fa-chart-simple fa-3x mb-3"></i>

            <h5>No events available</h5>

            <p class="mb-0">
                Create an event first before viewing reports.
            </p>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
