<?php
session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

include("../includes/db.php");

if(!isset($_GET['id']))
{
    header("Location: my_events.php");
    exit();
}

$registration_id=mysqli_real_escape_string($conn,$_GET['id']);
$user_id=$_SESSION['user_id'];

// Make sure this registration belongs to the logged in user and is
// currently active before cancelling it.
$check=mysqli_query($conn,"
SELECT registrations.registration_id, attendance.attendance_status
FROM registrations
LEFT JOIN attendance
ON registrations.registration_id=attendance.registration_id
WHERE registrations.registration_id='$registration_id'
AND registrations.user_id='$user_id'
");

if(mysqli_num_rows($check)==0)
{
    echo "<script>
    alert('Registration not found.');
    window.location='my_events.php';
    </script>";
    exit();
}

$row=mysqli_fetch_assoc($check);

if($row['attendance_status']=="Present")
{
    echo "<script>
    alert('You have already checked in to this event, so your registration can no longer be cancelled.');
    window.location='my_events.php';
    </script>";
    exit();
}

mysqli_query($conn,"
UPDATE registrations
SET status='Cancelled'
WHERE registration_id='$registration_id'
AND user_id='$user_id'
");

echo "<script>
alert('Registration Cancelled.');
window.location='my_events.php';
</script>";
