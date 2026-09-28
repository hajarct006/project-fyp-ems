<?php

session_start();


/*
|--------------------------------------------------------------------------
| ADMIN LOGIN SECURITY
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['admin_id']) ||
    empty($_SESSION['admin_id'])
) {

    header("Location: login.php");

    exit();

}


include("../includes/db.php");


/*
|--------------------------------------------------------------------------
| EVENT ID
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET['event_id']) ||
    empty($_GET['event_id'])
) {

    header("Location: participants.php");

    exit();

}


$event_id = intval($_GET['event_id']);


if ($event_id <= 0) {

    header("Location: participants.php");

    exit();

}


/*
|--------------------------------------------------------------------------
| GET EVENT INFORMATION
|--------------------------------------------------------------------------
*/

$event_stmt = mysqli_prepare(
    $conn,

    "
    SELECT

        event_id,
        event_title,
        event_date,
        start_time,
        end_time,
        venue,
        quota,
        status

    FROM events

    WHERE event_id = ?

    LIMIT 1
    "
);


mysqli_stmt_bind_param(
    $event_stmt,
    "i",
    $event_id
);


mysqli_stmt_execute(
    $event_stmt
);


$event_result = mysqli_stmt_get_result(
    $event_stmt
);


if (
    !$event_result ||
    mysqli_num_rows($event_result) == 0
) {

    header("Location: participants.php");

    exit();

}


$event = mysqli_fetch_assoc(
    $event_result
);


mysqli_stmt_close(
    $event_stmt
);


/*
|--------------------------------------------------------------------------
| SEARCH PARTICIPANT
|--------------------------------------------------------------------------
*/

$search = "";


if (isset($_GET['search'])) {

    $search = trim(
        $_GET['search']
    );

}


/*
|--------------------------------------------------------------------------
| GET PARTICIPANTS
|--------------------------------------------------------------------------
|
| We get participants from registrations.
|
| This means even if a participant has not
| attended yet, they will still appear here.
|
|--------------------------------------------------------------------------
*/

if ($search !== "") {

    $search_value =
        "%" .
        $search .
        "%";


    $sql = "

    SELECT

        registrations.registration_id,

        registrations.registration_date,

        registrations.status AS registration_status,

        users.full_name,

        users.registration_no,

        users.email,

        users.phone

    FROM registrations

    INNER JOIN users

        ON registrations.user_id =
           users.user_id

    WHERE registrations.event_id = ?

    AND (

        users.full_name LIKE ?

        OR users.registration_no LIKE ?

        OR users.email LIKE ?

        OR users.phone LIKE ?

    )

    ORDER BY users.full_name ASC

    ";


    $stmt = mysqli_prepare(
        $conn,
        $sql
    );


    mysqli_stmt_bind_param(
        $stmt,
        "issss",
        $event_id,
        $search_value,
        $search_value,
        $search_value,
        $search_value
    );

} else {

    $sql = "

    SELECT

        registrations.registration_id,

        registrations.registration_date,

        registrations.status AS registration_status,

        users.full_name,

        users.registration_no,

        users.email,

        users.phone

    FROM registrations

    INNER JOIN users

        ON registrations.user_id =
           users.user_id

    WHERE registrations.event_id = ?

    ORDER BY users.full_name ASC

    ";


    $stmt = mysqli_prepare(
        $conn,
        $sql
    );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $event_id
    );

}


mysqli_stmt_execute(
    $stmt
);


$result = mysqli_stmt_get_result(
    $stmt
);


/*
|--------------------------------------------------------------------------
| TOTAL PARTICIPANTS
|--------------------------------------------------------------------------
*/

$count_stmt = mysqli_prepare(
    $conn,

    "
    SELECT COUNT(*) AS total

    FROM registrations

    WHERE event_id = ?

    "
);


mysqli_stmt_bind_param(
    $count_stmt,
    "i",
    $event_id
);


mysqli_stmt_execute(
    $count_stmt
);


$count_result = mysqli_stmt_get_result(
    $count_stmt
);


$count_data = mysqli_fetch_assoc(
    $count_result
);


$total_participants =
    intval(
        $count_data['total']
    );


mysqli_stmt_close(
    $count_stmt
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

<title>
Participants - <?php echo htmlspecialchars($event['event_title']); ?>
</title>


<!-- BOOTSTRAP -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>


<!-- FONT AWESOME -->

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet"
>


<!-- POPPINS -->

<link
href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
rel="stylesheet"
>


<style>

/* =========================
   GENERAL
========================= */

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

    font-family:'DM Sans',sans-serif;

}


body {

    background: #f4f4f4;

}


/*
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
*/

.main {

    margin-left: 250px;

    padding: 30px;

}


/*
|--------------------------------------------------------------------------
| TOPBAR
|--------------------------------------------------------------------------
*/

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

}


.topbar h3 {

    margin: 0;

    color: #800020;

    font-size: 24px;

    font-weight: 600;

}


.topbar span {

    font-size: 14px;

}


/*
|--------------------------------------------------------------------------
| EVENT HEADER
|--------------------------------------------------------------------------
*/

.event-header {

    background: #800020;

    color: white;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 25px;

}


.event-header h4 {

    font-size: 22px;

    font-weight: 600;

    margin-bottom: 15px;

}


.event-header p {

    margin: 6px 0;

    font-size: 14px;

}


.event-header i {

    width: 22px;

}


/*
|--------------------------------------------------------------------------
| CONTENT CARD
|--------------------------------------------------------------------------
*/

.card-box {

    background: white;

    padding: 25px;

    border-radius: 20px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

}


/*
|--------------------------------------------------------------------------
| STAT
|--------------------------------------------------------------------------
*/

.stat-box {

    background: #fff5f7;

    border: 1px solid #f1d5dc;

    border-radius: 15px;

    padding: 18px;

    margin-bottom: 25px;

}


.stat-box i {

    color: #800020;

    font-size: 25px;

    margin-right: 10px;

}


.stat-number {

    color: #800020;

    font-size: 24px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

.search-box {

    max-width: 400px;

}


.search-box .form-control {

    height: 45px;

    border-radius: 10px 0 0 10px;

}


.search-box .btn {

    background: #D4AF37;

    border: none;

    color: #800020;

    font-weight: 600;

}


/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.table-responsive {

    border-radius: 14px;

    overflow-x: auto;

    border: 1px solid #eeeeee;

}


.table {

    margin-bottom: 0;

    vertical-align: middle;

    min-width: 850px;

}


.table thead th {

    background: #800020 !important;

    color: white !important;

    border: none !important;

    text-align: center;

    padding: 16px 14px;

    font-size: 15px;

    white-space: nowrap;

}


.table tbody td {

    padding: 16px 14px;

    font-size: 15px;

    border-color: #eeeeee;

}


.table tbody td strong{

    font-size: 15.5px;

}


.table tbody tr:hover td {

    background: #fff5f7;

}


.table tbody td:first-child {

    text-align: center;

    width: 60px;

}


.table tbody td:nth-child(1),
.table tbody td:nth-child(3),
.table tbody td:nth-child(4),
.table tbody td:nth-child(5),
.table tbody td:nth-child(6) {

    text-align: center;

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

.badge {

    padding: 8px 12px;

    border-radius: 8px;

    font-weight: 500;

}


/*
|--------------------------------------------------------------------------
| BACK BUTTON
|--------------------------------------------------------------------------
*/

.btn-back {

    background: #6c757d;

    color: white;

    border: none;

    padding: 10px 18px;

    border-radius: 8px;

    text-decoration: none;

}


.btn-back:hover {

    background: #5c636a;

    color: white;

}


/*
|--------------------------------------------------------------------------
| EMPTY
|--------------------------------------------------------------------------
*/

.empty-box {

    text-align: center;

    padding: 50px 20px;

}


.empty-box i {

    font-size: 45px;

    color: #800020;

    margin-bottom: 15px;

}


.empty-box h5 {

    font-weight: 600;

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media(max-width:991px) {

    .main {

        margin-left: 0;

        padding:
            24px 18px;

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


@media(max-width:576px) {

    .main {

        padding:
            20px 14px;

        padding-top: 64px;

    }


    .card-box {

        padding: 18px;

    }


    .event-header {

        padding: 18px;

    }


    .event-header h4 {

        font-size: 18px;

    }


    .search-box {

        width: 100%;

        max-width: 100%;

    }


    .topbar span {

        width: 100%;

    }

}

</style>

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| REUSABLE ADMIN MENU
|--------------------------------------------------------------------------
*/

include("admin_menu.php");

?>


<!-- =========================
     MAIN
========================= -->

<div class="main">


    <!-- =========================
         TOPBAR
    ========================= -->

    <div class="topbar">

        <h3>

            <i class="fa fa-users"></i>

            Event Participants

        </h3>


        <span>

            Welcome,

            <b>

                <?php

                echo htmlspecialchars(
                    $_SESSION['admin_name'] ?? 'Admin'
                );

                ?>

            </b>

        </span>

    </div>


    <!-- =========================
         EVENT INFORMATION
    ========================= -->

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

            <strong>Date:</strong>

            <?php

            echo date(
                "d M Y",
                strtotime(
                    $event['event_date']
                )
            );

            ?>

        </p>


        <p>

            <i class="fa fa-clock"></i>

            <strong>Time:</strong>

            <?php

            echo date(
                "h:i A",
                strtotime(
                    $event['start_time']
                )
            );

            ?>

            -

            <?php

            echo date(
                "h:i A",
                strtotime(
                    $event['end_time']
                )
            );

            ?>

        </p>


        <p>

            <i class="fa fa-location-dot"></i>

            <strong>Venue:</strong>

            <?php

            echo htmlspecialchars(
                $event['venue']
            );

            ?>

        </p>


        <p>

            <i class="fa fa-users"></i>

            <strong>Quota:</strong>

            <?php

            echo htmlspecialchars(
                $event['quota']
            );

            ?>

        </p>

    </div>


    <!-- =========================
         PARTICIPANT CARD
    ========================= -->

    <div class="card-box">


        <!-- TOTAL -->

        <div class="stat-box">

            <i class="fa fa-users"></i>

            <span>

                Total Registered Participants:

            </span>

            <span class="stat-number">

                <?php

                echo $total_participants;

                ?>

            </span>

        </div>


        <!-- =========================
             HEADER + SEARCH
        ========================= -->

        <div
            class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"
        >


            <h4
                style="color:#800020;"
                class="mb-0"
            >

                <i class="fa fa-user-group"></i>

                Registered Participants

            </h4>


            <form
                method="GET"
                class="search-box"
            >

                <input
                    type="hidden"
                    name="event_id"
                    value="<?php echo $event_id; ?>"
                >


                <div class="input-group">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search participant..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >


                    <button
                        type="submit"
                        class="btn"
                    >

                        <i class="fa fa-search"></i>

                    </button>

                </div>

            </form>


        </div>


        <!-- =========================
             PARTICIPANT TABLE
        ========================= -->

        <div class="table-responsive">


            <table
                class="table table-bordered table-hover"
            >


                <thead>

                    <tr>

                        <th>No.</th>

                        <th>Participant</th>

                        <th>Registration No.</th>

                        <th>Email</th>

                        <th>Phone</th>

                        <th>Registration Date</th>

                        <th>Status</th>

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


                        <!-- NO -->

                        <td>

                            <?php

                            echo $count++;

                            ?>

                        </td>


                        <!-- NAME -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row['full_name']
                                );

                                ?>

                            </strong>

                        </td>


                        <!-- REGISTRATION NUMBER -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['registration_no']
                                ?? '-'
                            );

                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['email']
                                ?? '-'
                            );

                            ?>

                        </td>


                        <!-- PHONE -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['phone']
                                ?? '-'
                            );

                            ?>

                        </td>


                        <!-- REGISTRATION DATE -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row['registration_date']
                                )
                            ) {

                                echo date(
                                    "d M Y h:i A",
                                    strtotime(
                                        $row['registration_date']
                                    )
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>


                        <!-- STATUS -->

                        <td>


                            <?php

                            if (
                                isset(
                                    $row['registration_status']
                                )
                                &&
                                strtolower(
                                    $row['registration_status']
                                )
                                ===
                                'approved'
                            ) {

                            ?>

                                <span
                                    class="badge bg-success"
                                >

                                    <i
                                        class="fa fa-check"
                                    ></i>

                                    Approved

                                </span>

                            <?php

                            }
                            elseif (
                                isset(
                                    $row['registration_status']
                                )
                                &&
                                strtolower(
                                    $row['registration_status']
                                )
                                ===
                                'pending'
                            ) {

                            ?>

                                <span
                                    class="badge bg-warning text-dark"
                                >

                                    <i
                                        class="fa fa-clock"
                                    ></i>

                                    Pending

                                </span>

                            <?php

                            }
                            elseif (
                                isset(
                                    $row['registration_status']
                                )
                                &&
                                strtolower(
                                    $row['registration_status']
                                )
                                ===
                                'rejected'
                            ) {

                            ?>

                                <span
                                    class="badge bg-danger"
                                >

                                    <i
                                        class="fa fa-xmark"
                                    ></i>

                                    Rejected

                                </span>

                            <?php

                            }
                            else {

                            ?>

                                <span
                                    class="badge bg-secondary"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $row['registration_status']
                                        ?? 'Registered'
                                    );

                                    ?>

                                </span>

                            <?php

                            }

                            ?>


                        </td>


                    </tr>


                <?php

                    }


                }
                else {

                ?>


                    <tr>

                        <td
                            colspan="7"
                            class="text-center"
                        >

                            <div class="empty-box">


                                <i
                                    class="fa fa-users"
                                ></i>


                                <h5>

                                    No Participants Found

                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    <?php

                                    if ($search !== "") {

                                        echo "No participants match your search.";

                                    }
                                    else {

                                        echo "No participants have registered for this event yet.";

                                    }

                                    ?>

                                </p>


                            </div>

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>

            </table>


        </div>


        <!-- =========================
             BACK BUTTON
        ========================= -->

        <a
            href="participants.php"
            class="btn-back mt-3 d-inline-block"
        >

            <i class="fa fa-arrow-left"></i>

            Back to Participants

        </a>


    </div>


</div>


</body>

</html>


<?php

/*
|--------------------------------------------------------------------------
| CLOSE STATEMENT
|--------------------------------------------------------------------------
*/

mysqli_stmt_close(
    $stmt
);

?>