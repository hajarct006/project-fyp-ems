<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

if (!isset($_GET['event_id'])) {
    header("Location: attendance.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['event_id']);


/* =========================================================
   GET EVENT INFORMATION
========================================================= */

$event_sql = "
    SELECT event_id, event_title, event_date, venue
    FROM events
    WHERE event_id='$event_id'
";

$event_result = mysqli_query($conn, $event_sql);

if (!$event_result || mysqli_num_rows($event_result) == 0) {
    header("Location: attendance.php");
    exit();
}

$event = mysqli_fetch_assoc($event_result);

$message = "";


/* =========================================================
   CHECK EVENT DATE
========================================================= */

$today = date("Y-m-d");

$event_date_only = "";

if (!empty($event['event_date'])) {

    $event_date_only = date(
        "Y-m-d",
        strtotime($event['event_date'])
    );

}

$is_event_day = (
    !empty($event_date_only) &&
    $today == $event_date_only
);


/* =========================================================
   SCAN / CHECK IN
========================================================= */

if (isset($_POST['scan'])) {

    if (empty($_POST['qr_token'])) {

        $message = "
        <div class='alert alert-danger'>
            <i class='fa fa-circle-xmark'></i>
            Please scan a QR Code.
        </div>";

    } else {

        $token = mysqli_real_escape_string(
            $conn,
            trim($_POST['qr_token'])
        );


        /* =====================================================
           CHECK EVENT DATE BEFORE RECORDING ATTENDANCE
        ===================================================== */

        if (!$is_event_day) {

            $formatted_event_date = !empty($event_date_only)
                ? date("d M Y", strtotime($event_date_only))
                : "the scheduled date";


            $message = "
            <div class='alert alert-warning'>
                <i class='fa fa-calendar-xmark'></i>
                <strong>Attendance is not available today.</strong>
                <br>
                This event is scheduled for
                <b>" . htmlspecialchars($formatted_event_date) . "</b>.
                <br>
                You can only check in participants on the event date.
            </div>";

        } else {


            /* =================================================
               CHECK PARTICIPANT IC
            ================================================= */

            $check_sql = "
                SELECT
                    attendance.attendance_id,
                    attendance.attendance_status,
                    attendance.registration_id,
                    users.full_name,
                    registrations.event_id
                FROM attendance
                INNER JOIN registrations
                    ON attendance.registration_id =
                       registrations.registration_id
                INNER JOIN users
                    ON registrations.user_id =
                       users.user_id
                WHERE users.ic_number='$token'
                AND registrations.event_id='$event_id'
                LIMIT 1
            ";

            $check = mysqli_query(
                $conn,
                $check_sql
            );


            /* =================================================
               PARTICIPANT FOUND
            ================================================= */

            if ($check && mysqli_num_rows($check) > 0) {

                $data = mysqli_fetch_assoc($check);


                /* =============================================
                   ALREADY PRESENT
                ============================================= */

                if (
                    $data['attendance_status']
                    == "Present"
                ) {

                    $message = "
                    <div class='alert alert-warning'>
                        <i class='fa fa-triangle-exclamation'></i>
                        Attendance already recorded for
                        <b>" .
                        htmlspecialchars(
                            $data['full_name']
                        ) .
                        "</b>
                    </div>";

                } else {


                    /* =========================================
                       UPDATE ATTENDANCE
                    ========================================= */

                    $attendance_id =
                        $data['attendance_id'];


                    $update_sql = "
                        UPDATE attendance
                        SET
                            attendance_status='Present',
                            check_in=NOW()
                        WHERE attendance_id='$attendance_id'
                    ";


                    $update = mysqli_query(
                        $conn,
                        $update_sql
                    );


                    if ($update) {

                        $message = "
                        <div class='alert alert-success'>
                            <i class='fa fa-circle-check'></i>
                            Attendance recorded successfully for
                            <b>" .
                            htmlspecialchars(
                                $data['full_name']
                            ) .
                            "</b>
                        </div>";

                    } else {

                        $message = "
                        <div class='alert alert-danger'>
                            <i class='fa fa-circle-xmark'></i>
                            Failed to record attendance.
                        </div>";

                    }

                }

            } else {


                /* =============================================
                   INVALID IC / PARTICIPANT NOT REGISTERED
                ============================================= */

                $message = "
                <div class='alert alert-danger'>
                    <i class='fa fa-circle-xmark'></i>
                    <b>Invalid Participant QR Code.</b>
                    <br>
                    This participant is not registered for this event.
                </div>";

            }

        }

    }

}


/* =========================================================
   PARTICIPANT LIST
========================================================= */

$sql = "
    SELECT
        attendance.attendance_id,
        attendance.attendance_status,
        attendance.check_in,
        users.full_name,
        registrations.registration_id,
        registrations.registration_date
    FROM attendance
    INNER JOIN registrations
        ON attendance.registration_id =
           registrations.registration_id
    INNER JOIN users
        ON registrations.user_id =
           users.user_id
    WHERE registrations.event_id='$event_id'
    ORDER BY users.full_name ASC
";

$result = mysqli_query(
    $conn,
    $sql
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Event Attendance</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    rel="stylesheet"
>

<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
    rel="stylesheet"
>

<script
    src="https://unpkg.com/html5-qrcode"
    type="text/javascript">
</script>


<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family:'DM Sans',sans-serif;
}

body {
    background: #f5f5f5;
}


/* Sidebar */

.sidebar {
    position: fixed;
    width: 250px;
    height: 100vh;
    background: #800020;
    z-index: 1100;
    left: 0;
    top: 0;
}

.logo {
    text-align: center;
    padding: 30px;
    color: white;
}

.logo img {
    width: 80px;
    margin-bottom: 10px;
}

.logo h4 {
    font-weight: 600;
    margin: 0;
}

.sidebar a {
    display: block;
    padding: 16px 25px;
    text-decoration: none;
    color: white;
    transition: .3s;
    font-size: 16px;
}

.sidebar a i {
    width: 25px;
    margin-right: 5px;
}

.sidebar a:hover {
    background: #a00028;
    padding-left: 35px;
}


/* Main */

.main {
    margin-left: 250px;
    padding: 30px;
}


/* Topbar */

.topbar {
    background: white;
    padding: 18px 25px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.topbar h3 {
    margin: 0;
    font-size: 26px;
    font-weight: 600;
    color: #800020;
}

.topbar span {
    font-size: 15px;
}


/* Card */

.card-box {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    margin-bottom: 25px;
}


/* Event */

.event-header {
    background: #800020;
    color: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.event-header h4 {
    margin-bottom: 8px;
    font-weight: 600;
}

.event-header p {
    margin-bottom: 0;
    font-size: 15px;
}


/* Alert */

.alert {
    border-radius: 10px;
    margin-bottom: 20px;
}


/* Scanner */

.scan-box {
    background: #fafafa;
    border: 1px solid #eeeeee;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
}

.scan-box h5 {
    font-weight: 600;
}

#reader {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

#reader button {
    background: #800020 !important;
    color: white !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 8px 15px !important;
    margin: 5px;
}

#reader select {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ddd;
}


/* Check In */

.btn-checkin {
    background: #800020;
    color: white;
    border: none;
}

.btn-checkin:hover {
    background: #a00028;
    color: white;
}


/* Disabled */

.btn-checkin:disabled {
    background: #999;
    cursor: not-allowed;
}


/* Table */

.table-responsive {
    border-radius: 14px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid #eeeeee;
    box-shadow: 0 5px 15px rgba(0,0,0,.05);
    background: white;
}

.table {
    vertical-align: middle;
    margin-bottom: 0;
    width: 100%;
}

.table thead th {
    background-color: #800020 !important;
    color: white !important;
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
    font-size: 14px;
    border: none !important;
    padding: 15px 12px;
    white-space: nowrap;
}

.table tbody td {
    vertical-align: middle;
    padding: 14px 12px;
    font-size: 14px;
    border-color: #eeeeee;
    color: #444;
    background: white;
}

.table-hover tbody tr:hover td {
    background-color: #fff5f7;
}

.table tbody td:first-child {
    text-align: center;
    width: 70px;
    font-weight: 500;
    color: #666;
}

.table tbody td:nth-child(2) {
    text-align: left;
    font-weight: 500;
    min-width: 180px;
}

.table tbody td:nth-child(3),
.table tbody td:nth-child(4),
.table tbody td:nth-child(5) {
    text-align: center;
    white-space: nowrap;
}


/* Badge */

.badge {
    padding: 8px 13px;
    font-weight: 500;
    font-size: 12px;
    border-radius: 8px;
}


/* Mobile */

.menu-toggle {
    display: none;
    position: fixed;
    top: 14px;
    left: 14px;
    z-index: 1200;
    background: #800020;
    color: white;
    border: 2px solid rgba(255,255,255,.9);
    width: 36px;
    height: 36px;
    border-radius: 9px;
    font-size: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,.5);
    align-items: center;
    justify-content: center;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.55);
    z-index: 1099;
}

.sidebar-close {
    display: none;
    position: absolute;
    top: 14px;
    right: 14px;
    background: transparent;
    border: none;
    color: white;
    font-size: 22px;
}


@media(max-width: 991px) {

    .menu-toggle {
        display: flex;
    }

    .sidebar {
        transform: translateX(-100%);
        transition: .3s ease;
        box-shadow: 0 0 30px rgba(0,0,0,.3);
        overflow-y: auto;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar-close {
        display: block;
    }

    .sidebar-overlay.active {
        display: block;
    }

    .main {
        margin-left: 0;
        padding: 24px 18px;
        padding-top: 68px;
    }

    .topbar {
        flex-wrap: wrap;
        gap: 12px;
    }

    .topbar h3 {
        font-size: 20px;
    }

}


@media(max-width: 576px) {

    .main {
        padding: 20px 14px;
        padding-top: 64px;
    }

    .card-box {
        padding: 18px;
    }

    .scan-box {
        padding: 15px;
    }

    .event-header {
        padding: 16px;
    }

    .event-header h4 {
        font-size: 18px;
    }

    .event-header p {
        font-size: 13px;
    }

    .topbar span {
        width: 100%;
        font-size: 13px;
    }

    .input-group {
        flex-direction: column;
    }

    .input-group .form-control {
        width: 100%;
        border-radius: 6px !important;
        margin-bottom: 10px;
    }

    .input-group .btn {
        width: 100%;
        border-radius: 6px !important;
    }

    #reader {
        width: 100%;
    }

    .table-responsive {
        border-radius: 12px;
    }

    .table {
        min-width: 700px;
    }

}

</style>

</head>


<body>


<button
    class="menu-toggle"
    onclick="openSidebar()"
>

    <i class="fa fa-bars"></i>

</button>


<div
    class="sidebar-overlay"
    onclick="closeSidebar()"
></div>


<div
    class="sidebar"
    id="sidebar"
>

    <button
        class="sidebar-close"
        onclick="closeSidebar()"
    >

        <i class="fa fa-xmark"></i>

    </button>


    <div class="logo">

        <img
            src="../assets/images/logo.png"
            alt="JPP Logo"
        >

        <h4>JPP EMS</h4>

    </div>


    <a href="dashboard.php">

        <i class="fa fa-home"></i>

        Dashboard

    </a>


    <a href="events.php">

        <i class="fa fa-calendar"></i>

        Events

    </a>


    <a href="participants.php">

        <i class="fa fa-users"></i>

        Participants

    </a>


    <a href="attendance.php">

        <i class="fa fa-qrcode"></i>

        Attendance

    </a>


    <a href="reports.php">

        <i class="fa fa-chart-column"></i>

        Reports

    </a>


    <a href="exemption.php">

        <i class="fa fa-file-shield"></i>

        Exemption

    </a>


    <a href="manage_admins.php">

        <i class="fa fa-user-shield"></i>

        Manage Admins

    </a>


    <a href="logout.php">

        <i class="fa fa-right-from-bracket"></i>

        Logout

    </a>

</div>


<div class="main">


    <div class="topbar">

        <h3>

            <i class="fa fa-qrcode"></i>

            Event Attendance

        </h3>


        <span>

            Welcome,

            <b>

                <?php

                echo htmlspecialchars(
                    $_SESSION['admin_name']
                );

                ?>

            </b>

        </span>

    </div>


    <div class="card-box">


        <div class="event-header">

            <h4>

                <i class="fa fa-calendar-check"></i>

                <?php

                echo htmlspecialchars(
                    $event['event_title']
                );

                ?>

            </h4>


            <p>

                <i class="fa fa-calendar"></i>

                <?php

                if (!empty($event['event_date'])) {

                    echo date(
                        "d M Y",
                        strtotime($event['event_date'])
                    );

                } else {

                    echo "-";

                }

                ?>

                &nbsp;&nbsp; | &nbsp;&nbsp;

                <i class="fa fa-location-dot"></i>

                <?php

                echo htmlspecialchars(
                    $event['venue']
                );

                ?>

            </p>

        </div>


        <?php echo $message; ?>


        <!-- =================================================
             EVENT DATE WARNING
        ================================================= -->

        <?php

        if (!$is_event_day) {

            $display_date = !empty($event_date_only)
                ? date(
                    "d M Y",
                    strtotime($event_date_only)
                )
                : "the scheduled date";

        ?>

        <div class="alert alert-warning">

            <i class="fa fa-calendar-xmark"></i>

            <strong>
                Attendance is not available today.
            </strong>

            <br>

            Today is

            <b>
                <?php echo date("d M Y"); ?>
            </b>

            but this event is scheduled for

            <b>
                <?php echo htmlspecialchars($display_date); ?>
            </b>.

            <br>

            <span>
                You can view this page, but participants
                cannot be checked in until the event date.
            </span>

        </div>

        <?php } else { ?>

        <div class="alert alert-success">

            <i class="fa fa-calendar-check"></i>

            <strong>
                Attendance is open today.
            </strong>

            You can scan participant QR codes
            and record attendance.

        </div>

        <?php } ?>


        <div class="scan-box">


            <h5
                style="color:#800020;"
                class="mb-3"
            >

                <i class="fa fa-qrcode"></i>

                Scan Participant QR Code

            </h5>


            <div id="reader"></div>


            <div
                id="cameraStatus"
                class="alert alert-info mt-3"
            >

                <i class="fa fa-spinner fa-spin"></i>

                Starting camera...

            </div>


            <form
                method="POST"
                id="scanForm"
            >

                <div class="input-group mt-3">


                    <input
                        type="text"
                        name="qr_token"
                        id="qr_token"
                        class="form-control"
                        placeholder="Participant IC"
                        autocomplete="off"
                        required
                    >


                    <button
                        type="submit"
                        name="scan"
                        class="btn btn-checkin"
                        <?php

                        if (!$is_event_day) {

                            echo "disabled";

                        }

                        ?>
                    >

                        <i class="fa fa-check"></i>

                        Check In

                    </button>


                </div>

            </form>


            <small class="text-muted d-block mt-2">

                <i class="fa fa-camera"></i>

                Allow camera permission and point the
                camera at the participant's QR Code.

            </small>


            <?php if (!$is_event_day) { ?>

            <small
                class="text-danger d-block mt-2"
            >

                <i class="fa fa-lock"></i>

                Check-in is disabled because today
                is not the event date.

            </small>

            <?php } ?>


        </div>


        <h5
            class="mb-3"
            style="color:#800020;"
        >

            <i class="fa fa-users"></i>

            Participants

        </h5>


        <div class="table-responsive">


            <table
                class="table table-bordered table-hover"
            >


                <thead>

                    <tr>

                        <th>No.</th>

                        <th>Participant</th>

                        <th>Registration ID</th>

                        <th>Status</th>

                        <th>Check In</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                $count = 1;


                if (
                    $result &&
                    mysqli_num_rows($result) > 0
                ) {


                    while (
                        $row =
                        mysqli_fetch_assoc($result)
                    ) {


                ?>


                    <tr>


                        <td>

                            <?php

                            echo $count++;

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['full_name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['registration_id']
                            );

                            ?>

                        </td>


                        <td>


                            <?php

                            if (
                                $row['attendance_status']
                                == "Present"
                            ) {


                            ?>


                                <span
                                    class="badge bg-success"
                                >

                                    <i
                                        class="fa fa-check"
                                    ></i>

                                    Present

                                </span>


                            <?php

                            } else {


                            ?>


                                <span
                                    class="badge bg-danger"
                                >

                                    <i
                                        class="fa fa-xmark"
                                    ></i>

                                    Absent

                                </span>


                            <?php

                            }


                            ?>


                        </td>


                        <td>


                            <?php


                            if (
                                empty(
                                    $row['check_in']
                                )
                                ||
                                $row['check_in']
                                ==
                                "0000-00-00 00:00:00"
                            ) {


                                echo "-";


                            } else {


                                echo date(
                                    "d M Y h:i A",
                                    strtotime(
                                        $row['check_in']
                                    )
                                );


                            }


                            ?>


                        </td>


                    </tr>


                <?php


                    }


                } else {


                ?>


                    <tr>


                        <td
                            colspan="5"
                            class="text-center py-4"
                        >

                            <i
                                class="fa fa-users fa-2x mb-2"
                                style="color:#800020;"
                            >
                            </i>


                            <br>


                            No participants registered
                            for this event.


                        </td>


                    </tr>


                <?php


                }


                ?>


                </tbody>


            </table>


        </div>


        <a
            href="attendance.php"
            class="btn btn-secondary mt-3"
        >

            <i class="fa fa-arrow-left"></i>

            Back to Events

        </a>


    </div>

</div>


<script>


function openSidebar() {

    document
        .getElementById("sidebar")
        .classList
        .add("active");

}


function closeSidebar() {

    document
        .getElementById("sidebar")
        .classList
        .remove("active");

}


let scanner = null;

let scannerStarted = false;

let scanProcessing = false;


const qrInput =
    document.getElementById("qr_token");


const cameraStatus =
    document.getElementById("cameraStatus");


function showCameraStatus(
    message,
    type
) {

    cameraStatus.className =
        "alert alert-" +
        type +
        " mt-3";

    cameraStatus.innerHTML =
        message;

}


async function startScanner() {


    if (
        !navigator.mediaDevices ||
        !navigator.mediaDevices.getUserMedia
    ) {

        showCameraStatus(

            '<i class="fa fa-video-slash"></i> ' +

            '<strong>Camera is not available.</strong>',

            "danger"

        );

        return;

    }


    if (!window.isSecureContext) {

        showCameraStatus(

            '<i class="fa fa-lock"></i> ' +

            '<strong>Camera permission is blocked.</strong><br>' +

            'Please use localhost or HTTPS.',

            "warning"

        );

        return;

    }


    showCameraStatus(

        '<i class="fa fa-spinner fa-spin"></i> ' +

        'Requesting camera permission...',

        "info"

    );


    try {


        const stream =

            await navigator
                .mediaDevices
                .getUserMedia({

                    video: {

                        facingMode: {

                            ideal: "environment"

                        }

                    }

                });


        stream
            .getTracks()
            .forEach(function(track) {

                track.stop();

            });


        scanner =

            new Html5Qrcode(
                "reader"
            );


        await scanner.start(

            {

                facingMode:
                    "environment"

            },


            {

                fps: 10,

                qrbox: {

                    width: 250,

                    height: 250

                }

            },


            async function(decodedText) {


                if (scanProcessing) {

                    return;

                }


                scanProcessing = true;


                let ic =
                    decodedText.trim();


                ic =
                    ic.replace(
                        /\s/g,
                        ""
                    );


                qrInput.value =
                    ic;


                <?php if (!$is_event_day) { ?>


                showCameraStatus(

                    '<i class="fa fa-calendar-xmark"></i> ' +

                    '<strong>Attendance is not available today.</strong><br>' +

                    'This event is scheduled for ' +

                    '<strong><?php echo date("d M Y", strtotime($event_date_only)); ?></strong>.' +

                    '<br>' +

                    'The participant cannot be checked in today.',

                    "warning"

                );


                <?php } else { ?>


                showCameraStatus(

                    '<i class="fa fa-circle-check"></i> ' +

                    '<strong>QR Code scanned successfully!</strong><br>' +

                    'Participant IC: ' +

                    '<strong>' +

                    ic +

                    '</strong><br>' +

                    '<span class="text-muted">' +

                    'Please click Check In to record attendance.' +

                    '</span>',

                    "success"

                );


                <?php } ?>


                if (
                    scanner &&
                    scannerStarted
                ) {


                    try {


                        await scanner.stop();


                        scannerStarted =
                            false;


                    } catch (error) {


                        console.log(
                            error
                        );


                    }

                }


            },


            function(errorMessage) {

            }

        );


        scannerStarted = true;


        <?php if (!$is_event_day) { ?>


        showCameraStatus(

            '<i class="fa fa-calendar-xmark"></i> ' +

            '<strong>Camera ready, but attendance is closed.</strong><br>' +

            'You can scan the QR code, but check-in is disabled until the event date.',

            "warning"

        );


        <?php } else { ?>


        showCameraStatus(

            '<i class="fa fa-camera"></i> ' +

            '<strong>Camera ready!</strong><br>' +

            'Point the camera at the participant QR Code.',

            "success"

        );


        <?php } ?>


    } catch (error) {


        console.error(
            "Camera error:",
            error
        );


        let errorMessage =
            "Unable to access camera.";


        if (
            error.name ===
            "NotAllowedError"
        ) {


            errorMessage =
                "Camera permission was denied. " +
                "Please allow camera access.";


        } else if (
            error.name ===
            "NotFoundError"
        ) {


            errorMessage =
                "No camera was found.";


        } else if (
            error.name ===
            "NotReadableError"
        ) {


            errorMessage =
                "The camera is already being used.";


        }


        showCameraStatus(

            '<i class="fa fa-triangle-exclamation"></i> ' +

            '<strong>' +

            errorMessage +

            '</strong>',

            "danger"

        );

    }

}


/* =========================================================
   FORM VALIDATION
========================================================= */

document
    .getElementById("scanForm")
    .addEventListener(
        "submit",
        function(event) {


            <?php if (!$is_event_day) { ?>


            event.preventDefault();


            showCameraStatus(

                '<i class="fa fa-calendar-xmark"></i> ' +

                '<strong>Attendance is not available today.</strong><br>' +

                'You can only check in participants on the event date.',

                "warning"

            );


            return;


            <?php } ?>


            if (
                qrInput.value.trim() === ""
            ) {


                event.preventDefault();


                showCameraStatus(

                    '<i class="fa fa-triangle-exclamation"></i> ' +

                    '<strong>Please scan a QR Code first.</strong>',

                    "warning"

                );

            }

        }
    );


/* =========================================================
   START CAMERA
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function() {

        startScanner();

    }
);


/* =========================================================
   STOP CAMERA WHEN LEAVING
========================================================= */

window.addEventListener(
    "beforeunload",
    function() {


        if (
            scanner &&
            scannerStarted
        ) {


            scanner
                .stop()
                .catch(
                    function() {}
                );

        }

    }
);


</script>


</body>

</html>