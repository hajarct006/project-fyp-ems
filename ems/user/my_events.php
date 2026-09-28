<?php

session_start();


/*
|--------------------------------------------------------------------------
| USER LOGIN SECURITY
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}


include("../includes/db.php");


$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| GET USER'S REGISTERED EVENTS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT

    registrations.registration_id,
    registrations.status,

    events.event_id,
    events.event_title,
    events.event_date,
    events.start_time,
    events.end_time,
    events.venue,
    events.poster,

    attendance.attendance_status

FROM registrations

INNER JOIN events

ON registrations.event_id = events.event_id

LEFT JOIN attendance

ON registrations.registration_id = attendance.registration_id

WHERE registrations.user_id = '$user_id'

ORDER BY events.event_date DESC
";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>My Events</title>


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

/* =========================================================
   GENERAL
========================================================= */

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'DM Sans',sans-serif;

}


body{

    background:#f5f5f5;

}


/* =========================================================
   MAIN CONTENT
========================================================= */

.main{

    margin-left:250px;

    padding:30px;

}


/* =========================================================
   TOP BAR
========================================================= */

.topbar{

    background:white;

    padding:18px 25px;

    border-radius:15px;

    box-shadow:0 10px 25px rgba(0,0,0,.08);

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

}


.topbar h3{

    color:#800020;

    font-weight:bold;

    margin:0;

}


.topbar span{

    color:#555;

}


/* =========================================================
   EVENT CARD
========================================================= */

.event-card{

    height:100%;

    display:flex;

    flex-direction:column;

    background:white;

    border:none;

    border-radius:20px;

    overflow:hidden;

    box-shadow:0 10px 25px rgba(0,0,0,.08);

    transition:.3s;

}


.event-card:hover{

    transform:translateY(-6px);

}


/* =========================================================
   EVENT POSTER
========================================================= */

.poster-frame{
    position:relative;
    width:100%;
    aspect-ratio:4/5;
    max-height:min(480px,72vh);
    overflow:hidden;
    background:linear-gradient(145deg,#650019,#2b000b);
    display:flex;
    align-items:center;
    justify-content:center;
}

/* soft blurred copy of the poster fills any empty space */
.poster-frame::before{
    content:"";
    position:absolute;
    inset:-20px;
    background:var(--poster) center/cover no-repeat;
    filter:blur(18px) brightness(.55);
    opacity:.9;
}

/* the poster itself is always shown in full - never cropped */
.poster-frame img{
    position:relative;
    z-index:1;
    display:block;
    width:100%;
    height:100%;
    object-fit:contain;
}

.poster-empty{
    flex-direction:column;
    gap:10px;
    color:rgba(255,255,255,.75);
    font-size:14px;
}

.poster-empty i{
    font-size:42px;
    color:#e0c476;
}

.poster-empty::before{display:none}


/* =========================================================
   CARD BODY
========================================================= */

.event-card .card-body{

    padding:18px;

    flex:1;

    display:flex;

    flex-direction:column;

}

.event-card .card-body .d-grid{

    margin-top:auto;

}


.event-card .card-body h4{

    font-size:18px;

    color:#800020;

    font-weight:bold;

}


.event-card .card-body p{

    font-size:14px;

    margin-bottom:8px;

}


.event-card .btn{

    font-size:14px;

    padding:8px 12px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width:991px){

    .main{

        margin-left:0;

        padding:24px 18px;

        padding-top:68px;

    }


    .topbar{

        flex-wrap:wrap;

        gap:12px;

    }


    .topbar h3{

        font-size:20px;

    }

}


@media (max-width:576px){

    .main{

        padding:20px 14px;

        padding-top:64px;

    }


    .event-card .card-body{

        padding:16px;

    }


    .event-card .card-body h4{

        font-size:17px;

    }

}


/* ---- tidy card layout ---- */
.poster-frame{aspect-ratio:1/1;max-height:none}
.poster-frame::before{filter:blur(22px) brightness(.95) saturate(1.1);opacity:1}
.event-card:hover{transform:translateY(-4px)}
.event-card .card-body h4{min-height:2.6em;line-height:1.3;display:flex;align-items:center;margin-bottom:0}
.event-card .card-body .d-grid{margin-top:14px;align-content:start;min-height:132px}
.event-card .card-body p i.fa-calendar,
.event-card .card-body p i.fa-clock,
.event-card .card-body p i.fa-location-dot{color:#800020!important}
.event-card .btn-warning{background:#800020!important;border-color:#800020!important;color:#fff!important}
.event-card .btn-warning:hover{background:#5c0017!important;border-color:#5c0017!important}

/* poster shown in full on a clean neutral background (no blur) */
.poster-frame{aspect-ratio:4/5;background:#f6f1ea}
.poster-frame::before{display:none}
.poster-frame img{object-fit:contain}
.poster-empty{background:linear-gradient(145deg,#650019,#2b000b)}
</style>

</head>


<body>


<!-- =====================================================
     SHARED USER MENU
===================================================== -->

<?php

include("user_menu.php");

?>


<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main">


    <!-- TOP BAR -->

    <div class="topbar">


        <h3>

            My Registered Events

        </h3>


        <span>

            Welcome,

            <b>

                <?php

                echo htmlspecialchars(
                    $_SESSION['full_name']
                );

                ?>

            </b>

        </span>


    </div>



    <!-- =================================================
         REGISTERED EVENTS
    ================================================= -->

    <div class="row">


    <?php

    if(mysqli_num_rows($result) > 0)
    {


        while($row = mysqli_fetch_assoc($result))
        {


    ?>


        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4">


            <div class="event-card">


                <!-- EVENT POSTER -->

                <?php
                $posterFile = trim((string)($row['poster'] ?? ''));

                if($posterFile === '')
                {
                ?>

                    <div class="poster-frame poster-empty">
                        <i class="fa fa-image"></i>
                        <span>No poster</span>
                    </div>

                <?php
                }
                else
                {
                    $posterUrl = '../uploads/' . rawurlencode($posterFile);
                ?>

                    <div class="poster-frame" style="--poster:url(<?php echo htmlspecialchars($posterUrl, ENT_QUOTES); ?>)">
                        <img
                            src="<?php echo htmlspecialchars($posterUrl, ENT_QUOTES); ?>"
                            alt="Event Poster"
                            loading="lazy"
                        >
                    </div>

                <?php
                }
                ?>


                <!-- CARD BODY -->

                <div class="card-body">


                    <!-- EVENT TITLE -->

                    <h4>

                        <?php

                        echo htmlspecialchars(
                            $row['event_title']
                        );

                        ?>

                    </h4>


                    <hr>


                    <!-- DATE -->

                    <p>

                        <i class="fa fa-calendar text-danger"></i>

                        <b>Date :</b>

                        <?php

                        echo date(
                            "d M Y",
                            strtotime($row['event_date'])
                        );

                        ?>

                    </p>


                    <!-- TIME -->

                    <p>

                        <i class="fa fa-clock text-warning"></i>

                        <b>Time :</b>

                        <?php

                        echo date('h:i A', strtotime($row['start_time']));

                        ?>

                        -

                        <?php

                        echo date('h:i A', strtotime($row['end_time']));

                        ?>

                    </p>


                    <!-- VENUE -->

                    <p>

                        <i class="fa fa-location-dot text-primary"></i>

                        <b>Venue :</b>

                        <?php

                        echo htmlspecialchars(
                            $row['venue']
                        );

                        ?>

                    </p>


                    <!-- REGISTRATION STATUS -->

                    <p>

                        <b>Registration :</b>


                        <?php

                        if($row['status'] == "Registered")
                        {

                        ?>


                            <span class="badge bg-success">

                                Registered

                            </span>


                        <?php

                        }
                        else
                        {

                        ?>


                            <span class="badge bg-danger">

                                Cancelled

                            </span>


                        <?php

                        }

                        ?>


                    </p>


                    <!-- ATTENDANCE STATUS -->

                    <p>

                        <b>Attendance :</b>


                        <?php

                        if($row['attendance_status'] == "Present")
                        {

                        ?>


                            <span class="badge bg-primary">

                                Present

                            </span>


                        <?php

                        }
                        else
                        {

                        ?>


                            <span class="badge bg-secondary">

                                Not Checked In

                            </span>


                        <?php

                        }

                        ?>


                    </p>



                    <!-- =================================================
                         ACTION BUTTONS
                    ================================================= -->

                    <div class="d-grid gap-2">


                        <!-- EVENT DETAILS -->

                        <a
                            href="event_detail.php?id=<?php echo $row['event_id']; ?>"
                            class="btn btn-outline-primary"
                        >

                            <i class="fa fa-circle-info"></i>

                            Event Details

                        </a>



                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | IF REGISTRATION IS ACTIVE
                        |--------------------------------------------------------------------------
                        */

                        if($row['status'] == "Registered")
                        {

                        ?>


                            <!-- DOCUMENTS -->

                            <a
                                href="exemption.php?id=<?php echo $row['event_id']; ?>"
                                class="btn btn-success"
                            >

                                <i class="fa fa-file-shield"></i>

                                Documents

                            </a>



                            <?php

                            /*
                            |--------------------------------------------------------------------------
                            | ALLOW CANCELLATION ONLY BEFORE ATTENDANCE
                            |--------------------------------------------------------------------------
                            */

                            if($row['attendance_status'] != "Present")
                            {

                            ?>


                                <a
                                    href="cancel_registration.php?id=<?php echo $row['registration_id']; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to cancel this registration?')"
                                >

                                    <i class="fa fa-trash"></i>

                                    Cancel Registration

                                </a>


                            <?php

                            }

                            ?>


                        <?php

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | IF REGISTRATION WAS CANCELLED
                        |--------------------------------------------------------------------------
                        */

                        else
                        {

                        ?>


                            <a
                                href="register_event.php?id=<?php echo $row['event_id']; ?>"
                                class="btn btn-warning"
                            >

                                <i class="fa fa-rotate-left"></i>

                                Register Again

                            </a>


                        <?php

                        }

                        ?>


                    </div>


                </div>


            </div>


        </div>


    <?php

        }

    }


    /*
    |--------------------------------------------------------------------------
    | NO REGISTERED EVENTS
    |--------------------------------------------------------------------------
    */

    else
    {

    ?>


        <div class="col-12">


            <div class="alert alert-warning text-center">


                <i class="fa fa-circle-info"></i>

                You have not registered for any event yet.


            </div>


        </div>


    <?php

    }


    ?>


    </div>


</div>


</body>

</html>

