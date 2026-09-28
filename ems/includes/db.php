<?php

/*
|--------------------------------------------------------------------------
| PREVENT CACHING OF SESSION-BASED PAGES
|--------------------------------------------------------------------------
| Without this, a caching proxy (common on mobile data networks) can
| serve one user's page/session cookie to another user, which looks
| like being logged out or "kicked" whenever someone else logs in.
*/

if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
}

$host = "localhost";
$user = "root";
$password = "";
$database = "ems_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database Connection Failed : " . mysqli_connect_error());
}

?>