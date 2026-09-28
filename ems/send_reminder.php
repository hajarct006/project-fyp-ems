<?php

include("includes/db.php");
include("includes/mail.php");


/* =========================================================
   EVENT REMINDER SYSTEM
   Sends reminder 1 day before the event
   ========================================================= */

// Get tomorrow's date
$tomorrow = date('Y-m-d', strtotime('+1 day'));


// Find registered participants for tomorrow's events
$sql = "
    SELECT
        r.registration_id,
        r.user_id,
        r.event_id,
        r.reminder_sent,

        u.full_name,
        u.email,

        e.event_title,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue

    FROM registrations r

    INNER JOIN users u
        ON r.user_id = u.user_id

    INNER JOIN events e
        ON r.event_id = e.event_id

    WHERE r.status = 'Registered'

    AND r.reminder_sent = 0

    AND e.event_date = '$tomorrow'

    AND (
        LOWER(e.status) = 'active'
        OR LOWER(e.status) = 'open'
    )
";


$result = mysqli_query($conn, $sql);


// Check database query
if (!$result)
{
    die(
        "Database Error: " .
        mysqli_error($conn)
    );
}


// Count participants
$total = mysqli_num_rows($result);

$sent = 0;
$failed = 0;


echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

echo "<meta charset='UTF-8'>";

echo "<title>Event Reminder System</title>";

echo "<style>

body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    padding: 30px;
}

.container {
    max-width: 800px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

h2 {
    color: #800020;
}

.success {
    color: green;
    background: #e8f5e9;
    padding: 12px;
    margin: 10px 0;
    border-radius: 5px;
}

.error {
    color: red;
    background: #ffebee;
    padding: 12px;
    margin: 10px 0;
    border-radius: 5px;
}

.info {
    background: #f8e9ee;
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
}

</style>";

echo "</head>";

echo "<body>";

echo "<div class='container'>";


/* =========================================================
   PAGE TITLE
   ========================================================= */

echo "<h2>Event Reminder System</h2>";


echo "<div class='info'>";

echo "<strong>Reminder Date:</strong> ";

echo date(
    'd F Y',
    strtotime($tomorrow)
);

echo "<br>";

echo "<strong>Participants Found:</strong> ";

echo $total;

echo "</div>";


/* =========================================================
   NO PARTICIPANTS
   ========================================================= */

if ($total == 0)
{
    echo "<div class='info'>";

    echo "No reminder needs to be sent.";

    echo "<br><br>";

    echo "This can happen if:";

    echo "<ul>";

    echo "<li>There is no event tomorrow.</li>";

    echo "<li>No user is registered for tomorrow's event.</li>";

    echo "<li>The registration status is not Registered.</li>";

    echo "<li>The reminder has already been sent.</li>";

    echo "</ul>";

    echo "</div>";

    echo "</div>";
    echo "</body>";
    echo "</html>";

    exit();
}


/* =========================================================
   SEND REMINDER EMAIL
   ========================================================= */

while ($row = mysqli_fetch_assoc($result))
{

    $registration_id = $row['registration_id'];

    $user_name = $row['full_name'];
    $user_email = $row['email'];

    $event_title = $row['event_title'];
    $event_date = $row['event_date'];
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];
    $venue = $row['venue'];


    /* =====================================================
       CHECK EMAIL
       ===================================================== */

    if (empty($user_email))
    {

        $failed++;

        echo "<div class='error'>";

        echo "<strong>FAILED</strong><br>";

        echo "User: " .
            htmlspecialchars($user_name);

        echo "<br>";

        echo "Reason: User does not have an email address.";

        echo "</div>";

        continue;
    }


    /* =====================================================
       SEND EMAIL
       ===================================================== */

    $email_sent = sendReminderEmail(
        $user_email,
        $user_name,
        $event_title,
        $event_date,
        $start_time,
        $end_time,
        $venue
    );


    /* =====================================================
       EMAIL SUCCESS
       ===================================================== */

    if ($email_sent)
    {

        $sent++;


        // Update reminder_sent to 1
        $update = mysqli_query(
            $conn,
            "UPDATE registrations
             SET reminder_sent = 1
             WHERE registration_id = '$registration_id'"
        );


        echo "<div class='success'>";

        echo "<strong>REMINDER SENT</strong><br>";

        echo "Name: " .
            htmlspecialchars($user_name);

        echo "<br>";

        echo "Email: " .
            htmlspecialchars($user_email);

        echo "<br>";

        echo "Event: " .
            htmlspecialchars($event_title);

        echo "<br>";

        echo "reminder_sent: <strong>1</strong>";

        echo "</div>";


        // Check database update
        if (!$update)
        {

            echo "<div class='error'>";

            echo "Warning: Email was sent, ";
            echo "but reminder_sent could not be updated.";

            echo "<br>";

            echo mysqli_error($conn);

            echo "</div>";
        }

    }


    /* =====================================================
       EMAIL FAILED
       ===================================================== */

    else
    {

        $failed++;


        // reminder_sent stays 0
        // so the system can try again later

        echo "<div class='error'>";

        echo "<strong>FAILED TO SEND</strong><br>";

        echo "Name: " .
            htmlspecialchars($user_name);

        echo "<br>";

        echo "Email: " .
            htmlspecialchars($user_email);

        echo "<br>";

        echo "Event: " .
            htmlspecialchars($event_title);

        echo "<br>";

        echo "reminder_sent remains: <strong>0</strong>";

        echo "</div>";
    }
}


/* =========================================================
   FINAL RESULT
   ========================================================= */

echo "<hr>";

echo "<h3>Reminder Process Completed</h3>";

echo "<div class='info'>";

echo "<strong>Successfully Sent:</strong> ";

echo $sent;

echo "<br>";

echo "<strong>Failed:</strong> ";

echo $failed;

echo "<br>";

echo "<strong>Total Processed:</strong> ";

echo $total;

echo "</div>";


/* =========================================================
   TESTING INFORMATION
   ========================================================= */

echo "<div class='info'>";

echo "<strong>Testing Information</strong>";

echo "<br><br>";

if ($sent > 0)
{
    echo "The reminder email was sent successfully.";

    echo "<br>";

    echo "The registration record has been updated to:";

    echo "<br><br>";

    echo "<strong>reminder_sent = 1</strong>";
}
else
{
    echo "No reminder email was successfully sent.";
}

echo "</div>";


echo "</div>";

echo "</body>";

echo "</html>";

?>