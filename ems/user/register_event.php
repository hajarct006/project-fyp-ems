<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");
include("../includes/mail.php"); // TAMBAH

$user_id  = $_SESSION['user_id'];
$event_id = $_GET['id'];

// Check if user already has a registration for this event
$check = mysqli_query(
    $conn,
    "SELECT * FROM registrations 
     WHERE user_id='$user_id' 
     AND event_id='$event_id'"
);

if (mysqli_num_rows($check) > 0) {

    $existing = mysqli_fetch_assoc($check);

    // Already registered
    if ($existing['status'] == "Registered") {

        echo "<script>
                alert('You have already registered.');
                window.location='my_events.php';
              </script>";

        exit();
    }

    // Previously cancelled registration - reactivate it
    $registration_id = $existing['registration_id'];

    $qr = "QR" . time() . rand(100, 999);

    mysqli_query(
        $conn,
        "UPDATE registrations 
         SET status='Registered',
             qr_code='$qr',
             registration_date=NOW(),
             reminder_sent=0
         WHERE registration_id='$registration_id'"
    );

    // Check if attendance record already exists
    $att_check = mysqli_query(
        $conn,
        "SELECT attendance_id 
         FROM attendance 
         WHERE registration_id='$registration_id'"
    );

    if (mysqli_num_rows($att_check) > 0) {

        // Reset attendance to Absent
        mysqli_query(
            $conn,
            "UPDATE attendance 
             SET attendance_status='Absent'
             WHERE registration_id='$registration_id'"
        );

    } else {

        // Create new attendance record
        mysqli_query(
            $conn,
            "INSERT INTO attendance (
                registration_id,
                attendance_status
             ) VALUES (
                '$registration_id',
                'Absent'
             )"
        );
    }


    /* =====================================================
       SEND REGISTRATION CONFIRMATION EMAIL
       TAMBAH SAHAJA
       ===================================================== */

    $user_sql = mysqli_query(
        $conn,
        "SELECT full_name, email
         FROM users
         WHERE user_id='$user_id'
         LIMIT 1"
    );

    $event_sql = mysqli_query(
        $conn,
        "SELECT event_title,
                event_date,
                start_time,
                end_time,
                venue
         FROM events
         WHERE event_id='$event_id'
         LIMIT 1"
    );

    if (
        $user_sql &&
        mysqli_num_rows($user_sql) > 0 &&
        $event_sql &&
        mysqli_num_rows($event_sql) > 0
    ) {

        $user = mysqli_fetch_assoc($user_sql);
        $event = mysqli_fetch_assoc($event_sql);

        if (!empty($user['email'])) {

            sendRegistrationEmail(
                $user['email'],
                $user['full_name'],
                $event['event_title'],
                $event['event_date'],
                $event['start_time'],
                $event['end_time'],
                $event['venue'],
                $qr
            );
        }
    }


    echo "<script>
            alert('Registration Successful.');
            window.location='my_events.php';
          </script>";

    exit();
}

// New registration
$qr = "QR" . time() . rand(100, 999);

mysqli_query(
    $conn,
    "INSERT INTO registrations (
        user_id,
        event_id,
        qr_code,
        registration_date,
        status
     ) VALUES (
        '$user_id',
        '$event_id',
        '$qr',
        NOW(),
        'Registered'
     )"
);

$registration_id = mysqli_insert_id($conn);

// Create attendance record
mysqli_query(
    $conn,
    "INSERT INTO attendance (
        registration_id,
        attendance_status
     ) VALUES (
        '$registration_id',
        'Absent'
     )"
);


/* =====================================================
   SEND REGISTRATION CONFIRMATION EMAIL
   TAMBAH SAHAJA
   ===================================================== */

$user_sql = mysqli_query(
    $conn,
    "SELECT full_name, email
     FROM users
     WHERE user_id='$user_id'
     LIMIT 1"
);

$event_sql = mysqli_query(
    $conn,
    "SELECT event_title,
            event_date,
            start_time,
            end_time,
            venue
     FROM events
     WHERE event_id='$event_id'
     LIMIT 1"
);

if (
    $user_sql &&
    mysqli_num_rows($user_sql) > 0 &&
    $event_sql &&
    mysqli_num_rows($event_sql) > 0
) {

    $user = mysqli_fetch_assoc($user_sql);
    $event = mysqli_fetch_assoc($event_sql);

    if (!empty($user['email'])) {

        sendRegistrationEmail(
            $user['email'],
            $user['full_name'],
            $event['event_title'],
            $event['event_date'],
            $event['start_time'],
            $event['end_time'],
            $event['venue'],
            $qr
        );
    }
}


echo "<script>
        alert('Registration Successful.');
        window.location='my_events.php';
      </script>";

?>