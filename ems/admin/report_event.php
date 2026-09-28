<?php
session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

$event_id = (int)($_GET['event_id'] ?? 0);

if($event_id <= 0)
{
    header("Location: reports.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| EVENT
|--------------------------------------------------------------------------
*/

$event_stmt = mysqli_prepare(
    $conn,
    "
    SELECT *
    FROM events
    WHERE event_id=?
    LIMIT 1
    "
);

mysqli_stmt_bind_param(
    $event_stmt,
    "i",
    $event_id
);

mysqli_stmt_execute($event_stmt);

$event_result = mysqli_stmt_get_result($event_stmt);

$event = mysqli_fetch_assoc($event_result);

mysqli_stmt_close($event_stmt);

if(!$event)
{
    header("Location: reports.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| PARTICIPANT + ATTENDANCE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        r.registration_id,
        r.user_id,
        r.status,

        u.full_name,
        u.ic_number,
        u.registration_no,
        u.education_level,
        u.department,
        u.phone,
        u.email,

        a.attendance_status,
        a.check_in,

        CASE
            WHEN sc.confirmation_id IS NOT NULL
            THEN 1
            ELSE 0
        END AS survey_submitted

    FROM registrations r

    INNER JOIN users u
        ON r.user_id=u.user_id

    LEFT JOIN attendance a
        ON a.registration_id=r.registration_id

    LEFT JOIN survey_confirmations sc
        ON sc.user_id=r.user_id
        AND sc.event_id=r.event_id

    WHERE r.event_id=?

    ORDER BY u.full_name ASC
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $event_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$rows = [];

while($r=mysqli_fetch_assoc($result))
{
    $rows[]=$r;
}

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

$total_participants = 0;
$total_present = 0;
$total_survey_responses = 0;

foreach($rows as $r)
{
    if(strtolower(trim($r['status'] ?? '')) === 'registered')
    {
        $total_participants++;
    }

    if(
        strtolower(trim($r['status'] ?? '')) === 'registered' &&
        strtolower(trim($r['attendance_status'] ?? '')) === 'present'
    )
    {
        $total_present++;
    }

    if(
        strtolower(trim($r['status'] ?? '')) === 'registered' &&
        (int)$r['survey_submitted'] === 1
    )
    {
        $total_survey_responses++;
    }
}

$attendance_rate = $total_participants > 0
    ? round(($total_present / $total_participants) * 100,1)
    : 0;

$survey_rate = $total_present > 0
    ? round(($total_survey_responses / $total_present) * 100,1)
    : 0;

$survey_pending = max(
    0,
    $total_present - $total_survey_responses
);


/*
|--------------------------------------------------------------------------
| CONCLUSION
|--------------------------------------------------------------------------
*/

if($total_participants == 0)
{
    $conclusion =
        "No registered participants were recorded for this event.";
}
else
{
    $conclusion =
        $total_participants .
        " participant(s) registered, " .
        $total_present .
        " participant(s) attended (" .
        $attendance_rate .
        "% attendance). ";

    if($total_present == 0)
    {
        $conclusion .=
            "No survey response is available because no participant was marked Present.";
    }
    elseif($survey_pending > 0)
    {
        $conclusion .=
            $total_survey_responses .
            " of " .
            $total_present .
            " Present participant(s) submitted the survey (" .
            $survey_rate .
            "%), while " .
            $survey_pending .
            " participant(s) have not submitted it.";
    }
    else
    {
        $conclusion .=
            "All Present participant(s) submitted the survey (" .
            $survey_rate .
            "% response rate).";
    }
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

<title>
    <?= htmlspecialchars($event['event_title']) ?> - Event Report
</title>

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
    color:var(--cream, #fbf7ef);
}

.main{
    margin-left:250px;
    padding:30px;
    min-height:100vh;
}

.topbar{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:20px;
    padding:18px 22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    box-shadow:0 12px 28px rgba(0,0,0,.25);
    margin-bottom:20px;
}

.topbar h3{
    margin:0;
    color:var(--cream, #fbf7ef);
    font-family:'Playfair Display',serif;
    font-weight:700;
}

.back-btn{
    background:rgba(212,175,55,.14);
    color:var(--gold2);
    border:0;
    border-radius:12px;
    padding:10px 15px;
    text-decoration:none;
    font-weight:700;
    font-size:13px;
}

.back-btn:hover{
    background:rgba(212,175,55,.22);
    color:#fff;
}

.event-header{
    position:relative;
    overflow:hidden;
    background:
        radial-gradient(circle at 88% 20%,rgba(212,175,55,.18),transparent 25%),
        linear-gradient(145deg,#650019,#2b000b);
    color:#fff;
    border-radius:24px;
    padding:28px;
    box-shadow:0 18px 40px rgba(50,0,15,.17);
    margin-bottom:20px;
    border:1px solid rgba(212,175,55,.15);
}

.event-header:after{
    content:"";
    position:absolute;
    width:180px;
    height:180px;
    border:1px solid rgba(212,175,55,.18);
    border-radius:50%;
    right:-65px;
    top:-65px;
}

.event-header h4{
    position:relative;
    z-index:2;
    font-family:'Playfair Display',serif;
    font-size:28px;
    font-weight:700;
    margin-bottom:13px;
}

.event-info{
    position:relative;
    z-index:2;
    display:flex;
    flex-wrap:wrap;
    gap:16px;
    color:rgba(255,255,255,.78);
    font-size:13px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:13px;
    margin-bottom:20px;
}

.stat{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:18px;
    padding:18px;
    box-shadow:0 10px 25px rgba(0,0,0,.22);
}

.stat-icon{
    width:40px;
    height:40px;
    border-radius:12px;
    background:rgba(212,175,55,.14);
    color:var(--gold2);
    display:grid;
    place-items:center;
    margin-bottom:11px;
}

.stat strong{
    display:block;
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-size:27px;
    font-weight:800;
}

.stat span{
    font-size:11px;
    color:rgba(255,255,255,.55);
}

.conclusion{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-left:5px solid var(--gold);
    border-radius:18px;
    padding:18px 20px;
    box-shadow:0 10px 25px rgba(0,0,0,.22);
    margin-bottom:20px;
}

.conclusion-title{
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-size:20px;
    font-weight:700;
    margin-bottom:7px;
}

.conclusion p{
    margin:0;
    color:rgba(255,255,255,.65);
    font-size:13px;
    line-height:1.7;
}

.monitor-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
    margin-bottom:20px;
}

.monitor-card{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border:1px solid rgba(212,175,55,.18);
    border-radius:18px;
    padding:18px;
    box-shadow:0 10px 25px rgba(0,0,0,.22);
}

.monitor-card h5{
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-weight:700;
    font-size:18px;
    margin-bottom:12px;
}

.monitor-row{
    display:flex;
    justify-content:space-between;
    gap:12px;
    font-size:12px;
    color:rgba(255,255,255,.6);
    margin-bottom:6px;
}

.monitor-row strong{
    color:var(--gold2);
}

.progress{
    height:9px;
    background:rgba(255,255,255,.08);
    border-radius:20px;
}

.progress-bar{
    background:linear-gradient(90deg,var(--maroon),var(--gold));
    border-radius:20px;
}

.card-box{
    background:linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
    backdrop-filter:blur(10px);
    border-radius:22px;
    padding:26px;
    border:1px solid rgba(212,175,55,.18);
    box-shadow:0 12px 30px rgba(0,0,0,.25);
}

.card-title{
    color:var(--cream);
    font-family:'Playfair Display',serif;
    font-weight:700;
    font-size:22px;
}

.table thead th{
    background:linear-gradient(135deg,#650019,#2b000b);
    color:#fff;
    white-space:nowrap;
    font-size:14px;
    padding:15px 18px;
}

.table td{
    font-size:14.5px;
    padding:15px 18px;
    vertical-align:middle;
    color:var(--cream);
    border-color:rgba(255,255,255,.08);
}

.table-hover>tbody>tr:hover>*{
    background:rgba(212,175,55,.08);
    color:var(--cream);
}

.status{
    display:inline-block;
    border-radius:20px;
    padding:6px 12px;
    font-size:11.5px;
    font-weight:800;
    white-space:nowrap;
}

.present{
    background:#d1e7dd;
    color:#0f5132;
}

.absent{
    background:#e9ecef;
    color:#495057;
}

.registered{
    background:#cff4fc;
    color:#055160;
}

.cancelled{
    background:#f8d7da;
    color:#842029;
}

.survey-yes{
    background:#d1e7dd;
    color:#0f5132;
}

.survey-no{
    background:#fff3cd;
    color:#664d03;
}

.empty{
    text-align:center;
    padding:45px;
    color:rgba(255,255,255,.55);
}

.text-muted{
    color:rgba(255,255,255,.55) !important;
}

@media(max-width:1100px){
    .stats{
        grid-template-columns:repeat(3,1fr);
    }
}

@media(max-width:991px){
    .main{
        margin-left:0;
        padding:80px 18px 25px;
    }

    .stats{
        grid-template-columns:repeat(2,1fr);
    }

    .monitor-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:576px){
    .main{
        padding:72px 12px 20px;
    }

    .topbar{
        align-items:flex-start;
        flex-direction:column;
    }

    .stats{
        grid-template-columns:1fr 1fr;
    }

    .event-header{
        padding:23px 20px;
    }

    .event-header h4{
        font-size:24px;
    }

    .card-box{
        padding:12px;
    }
}


/* Classic clean light theme */
body{background:#f8f6f1!important;color:#302629!important}.main{color:#302629!important}
/* ===== Stat boxes are maroon; the panels below stay white ===== */
.conclusion,.monitor-card,.card-box{
    background:#fff!important;
    backdrop-filter:none!important;
    border:1px solid #e6dccd!important;
    box-shadow:0 12px 28px rgba(64,31,18,.08)!important;
    color:#302629!important;
}
.conclusion{border-left:5px solid var(--maroon,#800020)!important}
.conclusion-title,.monitor-card h5,.card-title{color:var(--maroon,#800020)!important}
.conclusion p,.monitor-row{color:#5f5256!important}
.monitor-row strong{color:var(--maroon,#800020)!important}
.card-box .text-muted,.monitor-card .text-muted{color:#76696d!important}
.progress{background:#eee8e4!important}
.progress-bar{background:linear-gradient(90deg,#800020,#5c0017)!important}

/* Table: white rows, maroon header */
.card-box .table{
    --bs-table-bg:#fff;
    --bs-table-color:#302629;
    --bs-table-hover-bg:#fffaf0;
    --bs-table-hover-color:#302629;
    --bs-table-border-color:#e5e1dc;
    color:#302629;
    margin-bottom:0;
}
.card-box .table>:not(caption)>*>*{background-color:#fff;color:#302629;border-color:#e5e1dc}
.card-box .table-hover>tbody>tr:hover>*{background-color:#fffaf0!important;color:#302629!important}
.card-box .table thead th{
    background:linear-gradient(135deg,#800020,#5c0017)!important;
    color:#fff!important;
    border-bottom:0!important;
}
.card-box .empty{color:#76696d!important}

/* Rich maroon + gold stat cards (same palette as dashboards) */
.stat:before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(160deg,rgba(255,255,255,.10),rgba(255,255,255,0) 45%);pointer-events:none}
.stat{position:relative;overflow:hidden}
.stat{text-align:center;color:#fff;border:1px solid rgba(128,0,32,.08)!important;box-shadow:0 12px 28px rgba(52,0,18,.12)!important;backdrop-filter:none;padding:24px 16px!important}
.stat-icon{background:rgba(255,255,255,.13)!important;color:#fff!important;margin:0 auto 12px!important}
.stat{background:linear-gradient(145deg,#7a0022,#3a0010)!important}
.stat strong,.stat span{color:#fff!important}.stat span{font-weight:700}
.stat .stat-icon{background:rgba(255,255,255,.13)!important;color:#fff!important}


/* event name header: white card */
.event-header{background:#fff!important;color:#302629!important;border:1px solid #e6dccd!important;box-shadow:0 12px 28px rgba(64,31,18,.08)!important}
.event-header:after{border-color:rgba(128,0,32,.12)!important}
.event-header h4{color:#800020!important}
.event-info{color:#5f5256!important}
.event-info i{color:#800020!important}
</style>
</head>

<body>

<?php include("admin_menu.php"); ?>

<div class="main">

    <div class="topbar">

        <h3>
            <i class="fa fa-chart-column"></i>
            Event Report
        </h3>

        <a
            class="back-btn"
            href="reports.php"
        >
            <i class="fa fa-arrow-left"></i>
            Back to Reports
        </a>

    </div>


    <div class="event-header">

        <h4>
            <?= htmlspecialchars($event['event_title']) ?>
        </h4>

        <div class="event-info">

            <span>
                <i class="fa fa-calendar"></i>
                <?= date('d M Y',strtotime($event['event_date'])) ?>
            </span>

            <span>
                <i class="fa fa-clock"></i>
                <?= date('h:i A',strtotime($event['start_time'])) ?>
                -
                <?= date('h:i A',strtotime($event['end_time'])) ?>
            </span>

            <span>
                <i class="fa fa-location-dot"></i>
                <?= htmlspecialchars($event['venue']) ?>
            </span>

            <span>
                <i class="fa fa-users"></i>
                Quota: <?= htmlspecialchars($event['quota']) ?>
            </span>

        </div>

    </div>


    <div class="stats">

        <div class="stat">
            <div class="stat-icon">
                <i class="fa fa-user-plus"></i>
            </div>
            <strong><?= $total_participants ?></strong>
            <span>Registered</span>
        </div>

        <div class="stat">
            <div class="stat-icon">
                <i class="fa fa-user-check"></i>
            </div>
            <strong><?= $total_present ?></strong>
            <span>Present</span>
        </div>

        <div class="stat">
            <div class="stat-icon">
                <i class="fa fa-chart-line"></i>
            </div>
            <strong><?= $attendance_rate ?>%</strong>
            <span>Attendance Rate</span>
        </div>

        <div class="stat">
            <div class="stat-icon">
                <i class="fa fa-clipboard-check"></i>
            </div>
            <strong><?= $total_survey_responses ?></strong>
            <span>Survey Responses</span>
        </div>

        <div class="stat">
            <div class="stat-icon">
                <i class="fa fa-percent"></i>
            </div>
            <strong><?= $survey_rate ?>%</strong>
            <span>Survey Response Rate</span>
        </div>

    </div>


    <div class="conclusion">

        <div class="conclusion-title">
            <i class="fa fa-file-circle-check me-2"></i>
            Event Conclusion
        </div>

        <p>
            <?= htmlspecialchars($conclusion) ?>
        </p>

    </div>


    <div class="monitor-grid">

        <div class="monitor-card">

            <h5>
                <i class="fa fa-user-check me-2"></i>
                Attendance Monitoring
            </h5>

            <div class="monitor-row">
                <span>Present</span>
                <strong><?= $total_present ?> / <?= $total_participants ?></strong>
            </div>

            <div class="progress">
                <div
                    class="progress-bar"
                    style="width:<?= min(100,$attendance_rate) ?>%"
                ></div>
            </div>

        </div>


        <div class="monitor-card">

            <h5>
                <i class="fa fa-clipboard-list me-2"></i>
                Survey Monitoring
            </h5>

            <div class="monitor-row">
                <span>Responded</span>
                <strong><?= $total_survey_responses ?> / <?= $total_present ?></strong>
            </div>

            <div class="progress">
                <div
                    class="progress-bar"
                    style="width:<?= min(100,$survey_rate) ?>%"
                ></div>
            </div>

            <div class="small text-muted mt-2">

                <?php if($total_present == 0): ?>

                    No Present participant yet.

                <?php elseif($survey_pending > 0): ?>

                    <?= $survey_pending ?>
                    Present participant(s) have not responded.

                <?php else: ?>

                    All Present participants have responded.

                <?php endif; ?>

            </div>

        </div>

    </div>


    <div class="card-box">

        <div class="mb-3">

            <h4 class="card-title mb-1">
                <i class="fa fa-users me-2"></i>
                Participant List
            </h4>

            <span class="text-muted small">
                Registration, attendance and survey response status.
            </span>

        </div>


        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>IC Number</th>
                        <th>Registration No</th>
                        <th>Programme</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Registration</th>
                        <th>Attendance</th>
                        <th>Survey</th>
                        <th>Check-In</th>
                    </tr>

                </thead>

                <tbody>

                <?php if($total_participants > 0): ?>

                    <?php
                    $no = 1;

                    foreach($rows as $row):

                        if(
                            strtolower(trim($row['status'] ?? '')) !== 'registered'
                        )
                        {
                            continue;
                        }

                        $att =
                            strtolower(
                                trim(
                                    $row['attendance_status'] ?? ''
                                )
                            );

                        $survey =
                            (int)$row['survey_submitted'] === 1;
                    ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td>
                            <?= htmlspecialchars($row['full_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['ic_number']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['registration_no']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['education_level'] ?? '') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['department'] ?? '') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['phone'] ?? '') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['email'] ?? '') ?>
                        </td>

                        <td>
                            <span class="status registered">
                                Registered
                            </span>
                        </td>

                        <td>

                            <?php if($att === 'present'): ?>

                                <span class="status present">
                                    Present
                                </span>

                            <?php else: ?>

                                <span class="status absent">
                                    Not Checked In
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if($survey): ?>

                                <span class="status survey-yes">
                                    Responded
                                </span>

                            <?php else: ?>

                                <span class="status survey-no">
                                    Pending
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= $row['check_in']
                                ? date(
                                    'd M Y, h:i A',
                                    strtotime($row['check_in'])
                                )
                                : '-'
                            ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="12"
                            class="empty"
                        >

                            <i
                                class="fa fa-user-slash fa-2x mb-2"
                                style="color:#800020"
                            ></i>

                            <br>

                            No participants registered for this event.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>
