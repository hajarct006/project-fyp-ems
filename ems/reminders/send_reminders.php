<?php

date_default_timezone_set('Asia/Kuala_Lumpur');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mail.php';

$sent24 = 0;
$sent1 = 0;
$failed = 0;

function processReminders($conn, $reminderType, $startInterval, $endInterval)
{
    global $sent24, $sent1, $failed;

    $sql = "
        SELECT
            r.registration_id,
            r.event_id,
            r.user_id,
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
        LEFT JOIN event_reminders er
            ON er.event_id = r.event_id
            AND er.user_id = r.user_id
            AND er.reminder_type = ?
        WHERE r.status = 'Registered'
        AND er.reminder_id IS NULL
        AND TIMESTAMP(e.event_date, e.start_time)
            BETWEEN DATE_ADD(NOW(), INTERVAL $startInterval)
            AND DATE_ADD(NOW(), INTERVAL $endInterval)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reminderType);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $sent = sendReminderEmail(
            $row['email'],
            $row['full_name'],
            $row['event_title'],
            $row['event_date'],
            $row['start_time'],
            $row['end_time'],
            $row['venue']
        );

        if ($sent) {

            $insert = $conn->prepare("
                INSERT INTO event_reminders
                (event_id, user_id, reminder_type, sent_at)
                VALUES (?, ?, ?, NOW())
            ");

            $insert->bind_param(
                "iis",
                $row['event_id'],
                $row['user_id'],
                $reminderType
            );

            $insert->execute();
            $insert->close();

            if ($reminderType === '24_hours') {
                $sent24++;
            } else {
                $sent1++;
            }

        } else {
            $failed++;
        }
    }

    $stmt->close();
}

processReminders(
    $conn,
    '24_hours',
    '23 HOUR + 55 MINUTE',
    '24 HOUR + 5 MINUTE'
);

processReminders(
    $conn,
    '1_hour',
    '55 MINUTE',
    '1 HOUR + 5 MINUTE'
);

echo "Reminder process completed.<br><br>";
echo "24-hour reminders sent: " . $sent24 . "<br>";
echo "1-hour reminders sent: " . $sent1 . "<br>";
echo "Failed: " . $failed . "<br>";
echo "Checked at: " . date('Y-m-d H:i:s');
?>