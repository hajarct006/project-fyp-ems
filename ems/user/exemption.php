<?php

session_start();

// =====================================================
// CHECK USER LOGIN
// =====================================================

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}


// =====================================================
// DATABASE
// =====================================================

include("../includes/db.php");


// =====================================================
// CHECK EVENT ID
// =====================================================

if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: my_events.php");
    exit();

}

$event_id = intval($_GET['id']);

$user_id = intval($_SESSION['user_id']);


// =====================================================
// GET USER REGISTRATION
// =====================================================

$reg_check = mysqli_query($conn, "

    SELECT
        registration_id,
        user_id,
        event_id,
        status

    FROM registrations

    WHERE event_id = '$event_id'

    AND user_id = '$user_id'

    LIMIT 1

");


if (!$reg_check) {

    die(
        "Registration query failed: " .
        mysqli_error($conn)
    );

}


if (mysqli_num_rows($reg_check) == 0) {

    header("Location: my_events.php");
    exit();

}


$reg = mysqli_fetch_assoc($reg_check);

$registration_id = intval($reg['registration_id']);


// =====================================================
// CHECK REGISTRATION STATUS
// =====================================================

if (
    strtolower(trim($reg['status'])) != "registered"
) {

    header("Location: my_events.php");
    exit();

}


// =====================================================
// GET EVENT INFORMATION
// =====================================================

$event_result = mysqli_query($conn, "

    SELECT *

    FROM events

    WHERE event_id = '$event_id'

    LIMIT 1

");


if (!$event_result) {

    die(
        "Event query failed: " .
        mysqli_error($conn)
    );

}


if (mysqli_num_rows($event_result) == 0) {

    header("Location: my_events.php");
    exit();

}


$event = mysqli_fetch_assoc($event_result);


// =====================================================
// CHECK ATTENDANCE / CHECK-IN
// =====================================================

$attendance_result = mysqli_query($conn, "

    SELECT
        attendance_id,
        attendance_status,
        check_in

    FROM attendance

    WHERE registration_id = '$registration_id'

    LIMIT 1

");


if (!$attendance_result) {

    die(
        "Attendance query failed: " .
        mysqli_error($conn)
    );

}


$checked_in = false;

$attendance = null;


if (mysqli_num_rows($attendance_result) > 0) {

    $attendance = mysqli_fetch_assoc($attendance_result);

    if (
        strtolower(trim($attendance['attendance_status'])) == "present"
    ) {

        $checked_in = true;

    }

}


// =====================================================
// CHECK SURVEY QUESTIONS
// =====================================================

$total_questions = 0;

$questions_result = mysqli_query($conn, "

    SELECT question_id

    FROM survey_questions

    ORDER BY question_id ASC

");


if ($questions_result) {

    $total_questions = mysqli_num_rows($questions_result);

}


// =====================================================
// CHECK SURVEY CONFIRMATION
// =====================================================

$survey_completed = false;

if ($checked_in && $total_questions > 0) {

    $confirmation_result = mysqli_query($conn, "

        SELECT confirmation_id

        FROM survey_confirmations

        WHERE user_id = '$user_id'

        AND event_id = '$event_id'

        LIMIT 1

    ");


    if ($confirmation_result) {

        if (mysqli_num_rows($confirmation_result) > 0) {

            $survey_completed = true;

        }

    }

}


// =====================================================
// GET LECTURE EXEMPTION MEMO
// =====================================================

$memo_result = mysqli_query($conn, "

    SELECT *

    FROM exemption_memos

    WHERE event_id = '$event_id'

    ORDER BY memo_id DESC

    LIMIT 1

");


if (!$memo_result) {

    die(
        "Memo query failed: " .
        mysqli_error($conn)
    );

}


$memo = null;


if (mysqli_num_rows($memo_result) > 0) {

    $memo =
        mysqli_fetch_assoc($memo_result);

}


// =====================================================
// CHECK EXISTING CERTIFICATE
// =====================================================

$certificate_result = mysqli_query($conn, "

    SELECT *

    FROM certificates

    WHERE registration_id = '$registration_id'

    LIMIT 1

");


if (!$certificate_result) {

    die(
        "Certificate query failed: " .
        mysqli_error($conn)
    );

}


$certificate = null;


if (mysqli_num_rows($certificate_result) > 0) {

    $certificate =
        mysqli_fetch_assoc($certificate_result);

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>Documents</title>


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


<style>

* {

    margin: 0;
    padding: 0;
    box-sizing: border-box;

    font-family:'DM Sans',sans-serif;

}


body {

    background: #f5f5f5;

    overflow-x: hidden;

}


.main {

    margin-left: 250px;

    padding: 30px;

    min-height: 100vh;

}


.topbar {

    background: white;

    padding: 18px 25px;

    border-radius: 15px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

    gap: 12px;

    flex-wrap: wrap;

}


.topbar h3 {

    color: #800020;

    font-weight: bold;

    margin: 0;

}


.topbar span {

    color: #555;

}


.event-header {

    background: #800020;

    color: white;

    padding: 25px 30px;

    border-radius: 20px;

    margin-bottom: 25px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.10);

}


.event-header h4 {

    font-weight: 700;

    margin-bottom: 8px;

}


.event-header p {

    margin: 0;

    opacity: .9;

}


.card-box {

    background: white;

    padding: 30px;

    border-radius: 20px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

}


.status-box {

    border-radius: 16px;

    padding: 22px;

    margin-bottom: 25px;

    border: 1px solid #ddd;

}


.status-box h5 {

    color: #800020;

    font-weight: 600;

    margin-bottom: 8px;

}


.status-box p {

    color: #666;

    margin-bottom: 15px;

}


.checked-box {

    background: #eaf7ef;

    border-color: #b7dfc4;

}


.checked-box h5 {

    color: #198754;

}


.not-checked-box {

    background: #fff8e6;

    border-color: #f0d98c;

}


.not-checked-box h5 {

    color: #856404;

}


.survey-box {

    background:
        linear-gradient(
            135deg,
            #fff,
            #f9f5f6
        );

    border: 1.5px solid #e5d4d9;

    border-radius: 18px;

    padding: 25px;

    margin-bottom: 25px;

}


.survey-icon {

    width: 60px;

    height: 60px;

    border-radius: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #f9e8ed;

    color: #800020;

    font-size: 28px;

    margin-bottom: 18px;

}


.survey-box h5 {

    color: #800020;

    font-weight: 600;

    margin-bottom: 8px;

}


.survey-box p {

    color: #777;

    font-size: 14px;

    line-height: 1.6;

}


.document-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;

    margin-top: 20px;

}


.document-box {

    border: 2px solid #e5e5e5;

    border-radius: 18px;

    padding: 25px;

    background: white;

    transition: .3s;

}


.document-box:hover {

    transform: translateY(-3px);

    box-shadow:
        0 8px 20px rgba(0,0,0,.08);

}


.document-box.locked {

    background: #f5f5f5;

    border-color: #ddd;

}


.document-icon {

    width: 60px;

    height: 60px;

    border-radius: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 28px;

    margin-bottom: 18px;

}


.certificate-icon {

    background: #fff7dc;

    color: #D4AF37;

}


.memo-icon {

    background: #eaf7ef;

    color: #198754;

}


.lock-icon {

    background: #e9ecef;

    color: #777;

}


.document-box h5 {

    color: #800020;

    font-weight: 600;

    margin-bottom: 8px;

}


.document-box p {

    color: #777;

    font-size: 14px;

    line-height: 1.6;

}


.btn-maroon {

    background: #800020;

    color: white;

    border: none;

    border-radius: 10px;

    padding: 10px 18px;

}


.btn-maroon:hover {

    background: #a00028;

    color: white;

}


.btn-gold {

    background:#800020;

    color:#fff;

    font-weight: bold;

    border: none;

    border-radius: 10px;

    padding: 10px 18px;

}


.btn-gold:hover {

    background:#5c0017;

    color:#fff;

}


.btn-back {

    background: #6c757d;

    color: white;

    border: none;

    border-radius: 10px;

    padding: 10px 20px;

    text-decoration: none;

    display: inline-block;

}


.btn-back:hover {

    background: #5c636a;

    color: white;

}


@media(max-width:991px) {

    .main {

        margin-left: 0;

        padding: 24px 18px;

        padding-top: 68px;

    }

}


@media(max-width:768px) {

    .document-grid {

        grid-template-columns: 1fr;

    }


    .event-header {

        padding: 22px;

    }


    .card-box {

        padding: 25px;

    }

}


@media(max-width:576px) {

    .main {

        padding: 20px 14px;

        padding-top: 64px;

    }


    .topbar {

        padding: 16px;

    }


    .topbar h3 {

        font-size: 19px;

    }


    .event-header {

        border-radius: 16px;

        padding: 20px;

    }


    .event-header h4 {

        font-size: 20px;

    }


    .card-box {

        padding: 20px;

        border-radius: 16px;

    }


    .document-box {

        padding: 20px;

    }

}

</style>

</head>


<body>


<?php

include("user_menu.php");

?>


<div class="main">


<div class="topbar">

    <h3>

        <i class="fa fa-file-lines"></i>

        &nbsp;

        Documents

    </h3>


    <span>

        Welcome,

        <b>

            <?php

            echo htmlspecialchars(
                $_SESSION['full_name'] ?? 'User'
            );

            ?>

        </b>

    </span>

</div>


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

        echo date(
            "d M Y",
            strtotime($event['event_date'])
        );

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


<div class="card-box">


<?php if (!$checked_in) { ?>


<div class="status-box not-checked-box">

    <h5>

        <i class="fa fa-circle-exclamation"></i>

        Attendance Required

    </h5>


    <p>

        You need to check in to this event before you
        can answer the survey and access your documents.

    </p>


    <a
        href="my_events.php"
        class="btn btn-maroon"
    >

        <i class="fa fa-qrcode"></i>

        &nbsp;

        Go to My Events

    </a>

</div>


<div class="document-grid">


<div class="document-box locked">

    <div class="document-icon lock-icon">

        <i class="fa fa-lock"></i>

    </div>


    <h5>

        Certificate

    </h5>


    <p>

        Check in to the event and complete the survey
        before accessing your certificate.

    </p>


    <button
        class="btn btn-secondary"
        disabled
    >

        <i class="fa fa-lock"></i>

        &nbsp;

        Locked

    </button>

</div>


<div class="document-box locked">

    <div class="document-icon lock-icon">

        <i class="fa fa-lock"></i>

    </div>


    <h5>

        Lecture Exemption

    </h5>


    <p>

        Check in to the event and complete the survey
        before accessing your lecture exemption document.

    </p>


    <button
        class="btn btn-secondary"
        disabled
    >

        <i class="fa fa-lock"></i>

        &nbsp;

        Locked

    </button>

</div>


</div>


<?php } else { ?>


<div class="status-box checked-box">

    <h5>

        <i class="fa fa-circle-check"></i>

        Attendance Confirmed

    </h5>


    <p>

        You have successfully checked in to this event.

        <?php if (!empty($attendance['check_in'])) { ?>

            <br>

            <small>

                Check-in:

                <?php

                echo date(
                    "d M Y, h:i A",
                    strtotime($attendance['check_in'])
                );

                ?>

            </small>

        <?php } ?>

    </p>

</div>


<?php if (!$survey_completed) { ?>


<div class="survey-box">


    <div class="survey-icon">

        <i class="fa fa-clipboard-check"></i>

    </div>


    <h5>

        Event Survey

    </h5>


    <?php if ($total_questions > 0) { ?>


    <p>

        Thank you for attending this event.

        Please complete the event survey before
        you can access your certificate and lecture
        exemption letter.

    </p>


    <a
        href="survey.php?id=<?php echo urlencode($event_id); ?>"
        class="btn btn-maroon"
    >

        <i class="fa fa-pen-to-square"></i>

        &nbsp;

        Answer Survey

    </a>


    <?php } else { ?>


    <div class="alert alert-warning mb-0">

        <i class="fa fa-triangle-exclamation"></i>

        &nbsp;

        The survey has not been prepared for this event yet.

        Your certificate and lecture exemption will remain
        locked until the survey is available.

    </div>


    <?php } ?>


</div>


<div class="document-grid">


<div class="document-box locked">

    <div class="document-icon lock-icon">

        <i class="fa fa-lock"></i>

    </div>


    <h5>

        Certificate

    </h5>


    <p>

        Complete the event survey first to unlock
        your certificate.

    </p>


    <button
        class="btn btn-secondary"
        disabled
    >

        <i class="fa fa-lock"></i>

        &nbsp;

        Locked

    </button>

</div>


<div class="document-box locked">

    <div class="document-icon lock-icon">

        <i class="fa fa-lock"></i>

    </div>


    <h5>

        Lecture Exemption

    </h5>


    <p>

        Complete the event survey first to access
        your lecture exemption document.

    </p>


    <button
        class="btn btn-secondary"
        disabled
    >

        <i class="fa fa-lock"></i>

        &nbsp;

        Locked

    </button>

</div>


</div>


<?php } else { ?>


<div class="status-box checked-box">

    <h5>

        <i class="fa fa-circle-check"></i>

        Survey Completed

    </h5>


    <p>

        You have successfully completed the event survey.

        Your certificate and lecture exemption are now
        available.

    </p>

</div>


<div class="document-grid">


<div class="document-box">

    <div class="document-icon certificate-icon">

        <i class="fa fa-award"></i>

    </div>


    <h5>

        Certificate

    </h5>


    <p>

        Download your official certificate
        for this event.

    </p>


    <a
        href="certificates/generate_certificate.php?registration_id=<?php echo urlencode($registration_id); ?>"
        class="btn btn-gold"
        target="_blank"
    >

        <i class="fa fa-download"></i>

        &nbsp;

        Download Certificate

    </a>

</div>


<div class="document-box">

    <div class="document-icon memo-icon">

        <i class="fa fa-file-shield"></i>

    </div>


    <h5>

        Lecture Exemption

    </h5>


    <?php if ($memo) { ?>


    <p>

        Your lecture exemption document
        is available for viewing or download.

    </p>


    <a
        href="../uploads/memos/<?php echo htmlspecialchars($memo['memo_file']); ?>"
        target="_blank"
        class="btn btn-gold"
    >

        <i class="fa fa-download"></i>

        &nbsp;

        View / Download Memo

    </a>


    <?php } else { ?>


    <div class="alert alert-warning mb-0">

        <i class="fa fa-triangle-exclamation"></i>

        &nbsp;

        The lecture exemption document has not
        been uploaded by the admin yet.

    </div>


    <?php } ?>


</div>


</div>


<?php } ?>


<?php } ?>


<div class="mt-4">

    <a
        href="my_events.php"
        class="btn-back"
    >

        <i class="fa fa-arrow-left"></i>

        &nbsp;

        Back to My Events

    </a>

</div>


</div>


</div>


</body>

</html>