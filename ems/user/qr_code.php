<?php

include("../includes/db.php");

/*
=========================================================
QR CODE
=========================================================

QR CODE CONTENT = USER IC NUMBER

Example:

050101081234

The IC number comes directly from:

users.ic_number
*/


if (isset($_GET['user_id'])) {

    $user_id = intval($_GET['user_id']);

    $sql = "
        SELECT ic_number
        FROM users
        WHERE user_id='$user_id'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {

        die("User not found.");

    }

    $user = mysqli_fetch_assoc($result);

    if (empty($user['ic_number'])) {

        die("User IC number is empty.");

    }

    echo htmlspecialchars(
        trim($user['ic_number'])
    );

}

?>