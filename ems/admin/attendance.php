<?php

session_start();


/*
|--------------------------------------------------------------------------
| ADMIN LOGIN SECURITY
|--------------------------------------------------------------------------
*/

if(
    !isset($_SESSION['admin_id']) ||
    empty($_SESSION['admin_id'])
)
{
    header("Location: login.php");

    exit();
}


include("../includes/db.php");


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = "";

if(isset($_GET['search']))
{
    $search = trim($_GET['search']);
}


/*
|--------------------------------------------------------------------------
| SQL
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT

        events.event_id,

        events.event_title,

        events.event_date,

        events.venue,

        events.status,

        COUNT(
            registrations.registration_id
        ) AS total_participants

    FROM events

    LEFT JOIN registrations

    ON events.event_id =
       registrations.event_id

";


/*
|--------------------------------------------------------------------------
| SEARCH CONDITION
|--------------------------------------------------------------------------
*/

if($search != "")
{

    $safe_search =
        mysqli_real_escape_string(
            $conn,
            $search
        );


    $sql .= "

        WHERE

            events.event_title
            LIKE '%$safe_search%'

            OR

            events.venue
            LIKE '%$safe_search%'

            OR

            events.event_date
            LIKE '%$safe_search%'

    ";

}


/*
|--------------------------------------------------------------------------
| GROUP + ORDER
|--------------------------------------------------------------------------
*/

$sql .= "

    GROUP BY

        events.event_id,

        events.event_title,

        events.event_date,

        events.venue,

        events.status


    ORDER BY

        CASE

            WHEN events.event_date >= CURDATE()
            THEN 0

            ELSE 1

        END ASC,


        CASE

            WHEN events.event_date >= CURDATE()
            THEN events.event_date

        END ASC,


        CASE

            WHEN events.event_date < CURDATE()
            THEN events.event_date

        END DESC

";


$result =
    mysqli_query(
        $conn,
        $sql
    );

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>Attendance</title>


<!-- =====================================================
     BOOTSTRAP
====================================================== -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>


<!-- =====================================================
     FONT AWESOME
====================================================== -->

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    rel="stylesheet"
>


<!-- =====================================================
     POPPINS
====================================================== -->

<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
    rel="stylesheet"
>


<style>

/* =========================================================
   GENERAL
========================================================= */

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

    font-family:'DM Sans',sans-serif;

}


body {

    background: #f5f5f5;

}


/* =========================================================
   MAIN
========================================================= */

.main {

    margin-left: 250px;

    padding: 30px;

}


/* =========================================================
   TOPBAR
========================================================= */

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

    color: #800020;

    margin: 0;

    font-weight: 600;

}


/* =========================================================
   CARD
========================================================= */

.card-box {

    background: white;

    padding: 25px;

    border-radius: 20px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

}


/* =========================================================
   EVENT HEADER
========================================================= */

.event-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;

}


.event-header h4 {

    color: #111827;

    font-size: 28px;

    font-weight: 600;

    margin: 0;

}


.event-header h4 i {

    color: #800020;

    margin-right: 8px;

}


/* =========================================================
   SEARCH
========================================================= */

.search-box {

    display: flex;

    width: 400px;

    height: 48px;

}


.search-box input {

    flex: 1;

    height: 48px;

    border: 1px solid #d9dce1;

    border-right: none;

    border-radius: 9px 0 0 9px;

    padding: 0 16px;

    font-size: 16px;

    outline: none;

}


.search-box input:focus {

    border-color: #800020;

    box-shadow: none;

}


.search-btn {

    width: 53px;

    height: 48px;

    border: none;

    background: #ffbf00;

    color: #000;

    border-radius: 0 9px 9px 0;

    font-size: 18px;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

}


.search-btn:hover {

    background: #e6aa00;

}


/* =========================================================
   EVENT CARD
========================================================= */

.event-card {

    background: white;

    border: 1px solid #eeeeee;

    border-radius: 15px;

    padding: 22px;

    margin-bottom: 20px;

    transition: .3s;

}


.event-card:hover {

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);

    transform: translateY(-2px);

}


.event-title {

    color: #800020;

    font-size: 20px;

    font-weight: 600;

    margin-bottom: 12px;

}


.event-info {

    color: #555;

    font-size: 14px;

    margin-bottom: 7px;

}


.event-info i {

    width: 23px;

    color: #800020;

}


/* =========================================================
   BUTTON
========================================================= */

.btn-view {

    background: #800020;

    color: white;

    border: none;

    padding: 11px 18px;

    border-radius: 8px;

    text-decoration: none;

    display: inline-block;

}


.btn-view:hover {

    background: #a00028;

    color: white;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:991px) {

    .main {

        margin-left: 0;

        padding: 24px 18px;

        padding-top: 68px;

    }


    .topbar {

        flex-wrap: wrap;

        gap: 12px;

    }


    .event-header {

        flex-wrap: wrap;

        gap: 18px;

    }


    .search-box {

        width: 350px;

    }

}


@media(max-width:576px) {

    .main {

        padding: 20px 14px;

        padding-top: 64px;

    }


    .card-box {

        padding: 18px;

    }


    .event-header {

        flex-direction: column;

        align-items: stretch;

    }


    .event-header h4 {

        font-size: 23px;

    }


    .search-box {

        width: 100%;

    }


    .event-card {

        padding: 18px;

    }


    .btn-view {

        width: 100%;

        text-align: center;

        margin-top: 15px;

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


<!-- =====================================================
     MAIN
====================================================== -->

<div class="main">


    <!-- =================================================
         TOPBAR
    ================================================== -->

    <div class="topbar">

        <h3>

            <i class="fa fa-qrcode"></i>

            Attendance

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


    <!-- =================================================
         EVENT BOX
    ================================================== -->

    <div class="card-box">


        <!-- EVENT HEADER -->

        <div class="event-header">


            <h4>

                <i class="fa fa-calendar-check"></i>

                Select Event

            </h4>


            <!-- SEARCH -->

            <form
                method="GET"
                action="attendance.php"
                class="search-box"
            >

                <input
                    type="text"
                    name="search"
                    placeholder="Search Event..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >


                <button
                    type="submit"
                    class="search-btn"
                >

                    <i class="fa fa-search"></i>

                </button>

            </form>


        </div>


<?php

/*
|--------------------------------------------------------------------------
| DISPLAY EVENTS
|--------------------------------------------------------------------------
*/

if(
    $result &&
    mysqli_num_rows($result) > 0
)
{

    $upcoming_started = false;

    $past_started = false;


    while(
        $row =
        mysqli_fetch_assoc($result)
    )
    {

        $event_date =
            strtotime(
                $row['event_date']
            );


        $today =
            strtotime(
                date("Y-m-d")
            );


        /*
        |--------------------------------------------------------------------------
        | UPCOMING / TODAY
        |--------------------------------------------------------------------------
        */

        if($event_date >= $today)
        {

            if(!$upcoming_started)
            {

                $upcoming_started = true;

?>

                <div
                    class="section-title"
                    style="
                        color:#800020;
                        font-size:20px;
                        font-weight:600;
                        margin-bottom:18px;
                    "
                >

                    <i class="fa fa-calendar-days"></i>

                    Upcoming Events

                </div>

<?php

            }


            if(
                date(
                    "Y-m-d",
                    $event_date
                )
                ==
                date("Y-m-d")
            )
            {

                $status_class =
                    "background:#fff3cd;color:#856404;";

                $status_text =
                    "Today";

            }
            else
            {

                $status_class =
                    "background:#e8f7ee;color:#198754;";

                $status_text =
                    "Upcoming";

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAST
        |--------------------------------------------------------------------------
        */

        else
        {

            if(!$past_started)
            {

                $past_started = true;

?>

                <div
                    style="
                        margin-top:35px;
                        padding-top:25px;
                        border-top:2px solid #eeeeee;
                        color:#800020;
                        font-size:20px;
                        font-weight:600;
                        margin-bottom:18px;
                    "
                >

                    <i class="fa fa-calendar-check"></i>

                    Past Events

                </div>

<?php

            }


            $status_class =
                "background:#eeeeee;color:#666;";

            $status_text =
                "Completed";

        }

?>


        <!-- =================================================
             EVENT CARD
        ================================================== -->

        <div class="event-card">


            <div class="row align-items-center">


                <!-- EVENT INFORMATION -->

                <div class="col-md-8">


                    <div class="event-title">

                        <?php

                        echo htmlspecialchars(
                            $row['event_title']
                        );

                        ?>

                    </div>


                    <div class="event-info">

                        <i class="fa fa-calendar"></i>

                        <?php

                        if(!empty($row['event_date']))
                        {

                            echo date(
                                "d M Y",
                                $event_date
                            );

                        }
                        else
                        {

                            echo "-";

                        }

                        ?>

                    </div>


                    <div class="event-info">

                        <i class="fa fa-location-dot"></i>

                        <?php

                        echo htmlspecialchars(
                            $row['venue']
                        );

                        ?>

                    </div>


                    <div class="event-info">

                        <i class="fa fa-users"></i>

                        <?php

                        echo $row[
                            'total_participants'
                        ];

                        ?>

                        Participants

                    </div>


                    <!-- STATUS -->

                    <span
                        style="
                            display:inline-block;
                            padding:6px 14px;
                            border-radius:20px;
                            font-size:12px;
                            font-weight:600;
                            margin-top:10px;
                            <?php echo $status_class; ?>
                        "
                    >

                        <?php

                        if($status_text == "Today")
                        {

                        ?>

                            <i class="fa fa-star"></i>

                        <?php

                        }
                        elseif($status_text == "Upcoming")
                        {

                        ?>

                            <i class="fa fa-clock"></i>

                        <?php

                        }
                        else
                        {

                        ?>

                            <i class="fa fa-check"></i>

                        <?php

                        }


                        echo " " . $status_text;

                        ?>

                    </span>


                </div>


                <!-- =================================================
                     VIEW ATTENDANCE
                ================================================== -->

                <div
                    class="col-md-4 text-md-end text-center"
                >

                    <a
                        href="attendance_event.php?event_id=<?php echo (int)$row['event_id']; ?>"
                        class="btn-view"
                    >

                        <i class="fa fa-qrcode"></i>

                        View Attendance

                    </a>

                </div>


            </div>


        </div>


<?php

    }

}
else
{

?>


        <!-- =================================================
             NO EVENTS
        ================================================== -->

        <div class="text-center p-5">


            <i
                class="fa fa-calendar-xmark fa-3x mb-3"
                style="color:#800020;"
            ></i>


            <h5>

                No Events Found

            </h5>


            <p class="text-muted">

                <?php

                if($search != "")
                {

                    echo "No events match your search.";

                }
                else
                {

                    echo "There are currently no events available.";

                }

                ?>

            </p>


        </div>


<?php

}

?>


    </div>


</div>


</body>

</html>