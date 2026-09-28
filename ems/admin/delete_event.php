<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Dapatkan maklumat poster dahulu
$sql = "SELECT poster FROM events WHERE event_id='$id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    // Padam gambar poster jika ada
    if (!empty($row['poster'])) {

        $file = "../uploads/" . $row['poster'];

        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Padam rekod attendance yang berkaitan
    // (attendance table has no event_id column - it links via registration_id)
    try {
        mysqli_query($conn, "DELETE FROM attendance WHERE registration_id IN (SELECT registration_id FROM registrations WHERE event_id='$id')");
    } catch (mysqli_sql_exception $e) {
        // ignore if attendance table/column doesn't exist for this setup
    }

    // Padam rekod certificate yang berkaitan
    // (certificates table also links via registration_id, not event_id)
    try {
        mysqli_query($conn, "DELETE FROM certificates WHERE registration_id IN (SELECT registration_id FROM registrations WHERE event_id='$id')");
    } catch (mysqli_sql_exception $e) {
        // ignore if certificates table/column doesn't exist for this setup
    }

    // Padam rekod registration yang berkaitan
    mysqli_query($conn, "DELETE FROM registrations WHERE event_id='$id'");

    // Padam event
    $delete = mysqli_query($conn, "DELETE FROM events WHERE event_id='$id'");

    if ($delete) {

        echo "<script>
        alert('Event deleted successfully.');
        window.location='events.php';
        </script>";

    } else {

        echo "<script>
        alert('Failed to delete event.');
        window.location='events.php';
        </script>";
    }

} else {

    echo "<script>
    alert('Event not found.');
    window.location='events.php';
    </script>";
}
?>