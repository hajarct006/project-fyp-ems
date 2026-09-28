<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../includes/db.php");


/*
|--------------------------------------------------------------------------
| SEARCH EVENT
|--------------------------------------------------------------------------
*/

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );
}


/*
|--------------------------------------------------------------------------
| GET EVENTS
|--------------------------------------------------------------------------
|
| ORDER:
|
| 1. Upcoming / today's events
| 2. Past events
|
| Upcoming:
| Nearest date first
|
| Past:
| Most recent past event first
|
|--------------------------------------------------------------------------
*/

if($search != "")
{

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

        WHERE events.event_title
        LIKE '%$search%'

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

}
else
{

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

}


$result = mysqli_query(
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

<title>Participants</title>


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

/*
|--------------------------------------------------------------------------
| GENERAL
|--------------------------------------------------------------------------
*/

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'DM Sans',sans-serif;

}


body{

    background:#f4f4f4;

}


/*
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
*/

.main{

    margin-left:250px;

    padding:30px;

}


/*
|--------------------------------------------------------------------------
| TOPBAR
|--------------------------------------------------------------------------
*/

.topbar{

    background:white;

    padding:18px 25px;

    border-radius:15px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

}


.topbar h3{

    color:#800020;

    margin:0;

    font-weight:600;

}


.topbar span{

    color:#555;

}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.card-box{

    background:white;

    padding:25px;

    border-radius:20px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

.search-box{

    width:320px;

}


/*
|--------------------------------------------------------------------------
| EVENT CARD
|--------------------------------------------------------------------------
*/

.event-card{

    background:white;

    border:1px solid #e5e5e5;

    border-radius:15px;

    padding:22px;

    margin-bottom:18px;

    transition:.3s;

}


.event-card:hover{

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);

    transform:translateY(-2px);

}


.event-title{

    color:#800020;

    font-size:20px;

    font-weight:600;

    margin-bottom:15px;

}


.event-info{

    color:#666;

    font-size:14px;

    margin-top:10px;

}


.event-info i{

    color:#800020;

    width:25px;

}


.participant-count{

    color:#666;

    font-size:14px;

    margin-top:10px;

}


.participant-count i{

    color:#800020;

    width:25px;

}


/*
|--------------------------------------------------------------------------
| EVENT STATUS
|--------------------------------------------------------------------------
*/

.event-status{

    display:inline-block;

    padding:6px 14px;

    border-radius:20px;

    font-size:12px;

    font-weight:600;

    margin-top:15px;

}


.status-upcoming{

    background:#e8f7ee;

    color:#198754;

}


.status-today{

    background:#fff3cd;

    color:#856404;

}


.status-past{

    background:#eeeeee;

    color:#666;

}


/*
|--------------------------------------------------------------------------
| SECTION TITLE
|--------------------------------------------------------------------------
*/

.section-title{

    color:#800020;

    font-size:20px;

    font-weight:600;

    margin-top:10px;

    margin-bottom:18px;

}


.section-title i{

    margin-right:8px;

}


/*
|--------------------------------------------------------------------------
| PAST SECTION
|--------------------------------------------------------------------------
*/

.past-section{

    margin-top:35px;

    padding-top:25px;

    border-top:2px solid #eeeeee;

}


/*
|--------------------------------------------------------------------------
| BUTTON
|--------------------------------------------------------------------------
*/

.btn-see{

    background:#800020;

    color:white;

    border:none;

    padding:10px 18px;

    border-radius:8px;

    text-decoration:none;

    display:inline-block;

}


.btn-see:hover{

    background:#a00028;

    color:white;

}


/*
|--------------------------------------------------------------------------
| EMPTY
|--------------------------------------------------------------------------
*/

.empty-box{

    text-align:center;

    padding:50px 20px;

}


.empty-box i{

    color:#800020;

}


/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

@media(max-width:991px){

    .main{

        margin-left:0;

        padding:24px 18px;

        padding-top:68px;

    }


    .topbar{

        flex-wrap:wrap;

        gap:12px;

    }

}


@media(max-width:576px){

    .main{

        padding:20px 14px;

        padding-top:64px;

    }


    .card-box{

        padding:18px;

    }


    .search-box{

        width:100%;

    }


    .event-card{

        padding:18px;

    }


    .topbar{

        padding:18px;

    }


    .topbar span{

        width:100%;

        font-size:13px;

    }

}

</style>

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| ADMIN MENU
|--------------------------------------------------------------------------
|
| Reusable sidebar is loaded from admin_menu.php
|
|--------------------------------------------------------------------------
*/

include("admin_menu.php");

?>


<!-- MAIN -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">


        <h3>

            <i class="fa fa-users"></i>

            Participants

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


    <!-- EVENT LIST -->

    <div class="card-box">


        <!-- HEADER -->

        <div
        class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3"
        >


            <h4>

                <i
                class="fa fa-calendar-check"
                style="color:#800020;"
                ></i>

                Select Event

            </h4>


            <!-- SEARCH -->

            <form
            method="GET"
            action="participants.php"
            >


                <div class="input-group search-box">


                    <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search Event..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    >


                    <button
                    type="submit"
                    class="btn btn-warning"
                    >

                        <i class="fa fa-search"></i>

                    </button>


                </div>


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


        /*
        |--------------------------------------------------------------------------
        | EVENT DATE
        |--------------------------------------------------------------------------
        */

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

        if(
            $event_date >= $today
        )
        {


            /*
            |--------------------------------------------------------------------------
            | UPCOMING SECTION TITLE
            |--------------------------------------------------------------------------
            */

            if(!$upcoming_started)
            {

                $upcoming_started = true;

?>

                <div class="section-title">

                    <i class="fa fa-calendar-days"></i>

                    Upcoming Events

                </div>

<?php

            }


            /*
            |--------------------------------------------------------------------------
            | TODAY
            |--------------------------------------------------------------------------
            */

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
                    "status-today";

                $status_text =
                    "Today";

            }

            /*
            |--------------------------------------------------------------------------
            | UPCOMING
            |--------------------------------------------------------------------------
            */

            else
            {

                $status_class =
                    "status-upcoming";

                $status_text =
                    "Upcoming";

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAST EVENTS
        |--------------------------------------------------------------------------
        */

        else
        {


            /*
            |--------------------------------------------------------------------------
            | PAST SECTION TITLE
            |--------------------------------------------------------------------------
            */

            if(!$past_started)
            {

                $past_started = true;

?>

                <div class="past-section">


                    <div class="section-title">

                        <i class="fa fa-calendar-check"></i>

                        Past Events

                    </div>


                </div>

<?php

            }


            $status_class =
                "status-past";

            $status_text =
                "Completed";

        }

?>


        <!-- EVENT CARD -->

        <div class="event-card">


            <div class="row align-items-center">


                <!-- EVENT INFORMATION -->

                <div class="col-md-8">


                    <!-- EVENT TITLE -->

                    <div class="event-title">

                        <?php

                        echo htmlspecialchars(
                            $row['event_title']
                        );

                        ?>

                    </div>


                    <!-- EVENT DATE -->

                    <div class="event-info">

                        <i class="fa fa-calendar"></i>

                        <?php

                        echo date(
                            "d M Y",
                            $event_date
                        );

                        ?>

                    </div>


                    <!-- VENUE -->

                    <div class="event-info">

                        <i class="fa fa-location-dot"></i>

                        <?php

                        echo htmlspecialchars(
                            $row['venue']
                        );

                        ?>

                    </div>


                    <!-- PARTICIPANTS -->

                    <div class="participant-count">

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
                    class="event-status <?php echo $status_class; ?>"
                    >


                        <?php

                        if(
                            $status_text ==
                            "Today"
                        )
                        {

                        ?>

                            <i
                            class="fa fa-star"
                            ></i>

                        <?php

                        }
                        elseif(
                            $status_text ==
                            "Upcoming"
                        )
                        {

                        ?>

                            <i
                            class="fa fa-clock"
                            ></i>

                        <?php

                        }
                        else
                        {

                        ?>

                            <i
                            class="fa fa-check"
                            ></i>

                        <?php

                        }


                        echo " " . $status_text;

                        ?>


                    </span>


                </div>


                <!-- SEE PARTICIPANTS BUTTON -->

                <div
                class="col-md-4 text-md-end text-center mt-3 mt-md-0"
                >


                    <a
                    href="participants_event.php?event_id=<?php echo intval($row['event_id']); ?>"
                    class="btn-see"
                    >

                        See Participants

                        <i
                        class="fa fa-arrow-right"
                        ></i>

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


        <!-- NO EVENTS -->

        <div class="empty-box">


            <i
            class="fa fa-calendar-xmark fa-3x mb-3"
            ></i>


            <h5>

                No Event Found

            </h5>


            <p class="text-muted">

                <?php

                if($search != "")
                {

                    echo "There are no events matching your search.";

                }
                else
                {

                    echo "There are currently no events.";

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