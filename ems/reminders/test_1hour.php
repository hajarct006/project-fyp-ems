<?php

date_default_timezone_set('Asia/Kuala_Lumpur');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mail.php';

echo "Testing 1-hour reminder email...<br><br>";

$sql = "
    SELECT
        u.full_name,
        u.email,
        e.event_title,
        e.event_date,
        e.start_time,
        e.end_time,
        e.venue
    FROM registrations r
    INNER JOIN users u ON r.user_id = u.user_id
    INNER JOIN events e ON r.event_id = e.event_id
    WHERE r.user_id = 4
    AND r.event_id = 7
    AND r.status = 'Registered'
    LIMIT 1
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("No registered participant found.");
}

$row = $result->fetch_assoc();

echo "Participant: " . $row['full_name'] . "<br>";
echo "Email: " . $row['email'] . "<br>";
echo "Event: " . $row['event_title'] . "<br>";
echo "Event Date: " . $row['event_date'] . "<br>";
echo "Event Time: " . $row['start_time'] . " - " . $row['end_time'] . "<br>";
echo "Venue: " . $row['venue'] . "<br><br>";

$sent = sendReminderEmail(
    $row['email'],
    $row['full_name'],
    $row['event_title'],
    $row['event_date'],
    $row['start_time'],
    $row['end_time'],
    $row['venue'],
    '1_hour'
);

if ($sent) {
    echo "<strong>SUCCESS!</strong><br>";
    echo "Test 1-hour reminder email was sent successfully.<br>";
    echo "No event_reminders record was created.";
} else {
    echo "<strong>FAILED!</strong><br>";
    echo "The test reminder email could not be sent.";
}

?>