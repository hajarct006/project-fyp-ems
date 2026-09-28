<?php

session_start();


/*
|--------------------------------------------------------------------------
| USER LOGIN SECURITY
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['user_id']))
{
    header("Location:../login.php");
    exit();
}


include("../includes/db.php");


/*
|--------------------------------------------------------------------------
| CHECK EVENT ID
|--------------------------------------------------------------------------
*/

if(!isset($_GET['id']) || empty($_GET['id']))
{
    header("Location:events.php");
    exit();
}


$event_id = mysqli_real_escape_string(
    $conn,
    $_GET['id']
);


/*
|--------------------------------------------------------------------------
| GET EVENT
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *

FROM events

WHERE event_id='$event_id'
";


$result = mysqli_query($conn, $sql);


if(!$result || mysqli_num_rows($result) == 0)
{
    die("Event not found.");
}


$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>
    <?php echo htmlspecialchars($row['event_title']); ?>
    - Event Details
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
   EVENT CONTAINER
========================================================= */

.event-container{

    max-width:1100px;

    margin:0 auto;

}


/* =========================================================
   EVENT CARD
========================================================= */

.event-card{

    background:white;

    border-radius:25px;

    overflow:hidden;

    box-shadow:0 10px 30px rgba(0,0,0,.10);

}


/* =========================================================
   POSTER AREA
========================================================= */

.poster-area{

    width:100%;

    min-height:500px;

    background:#f1f1f1;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:25px;

}


/* =========================================================
   EVENT POSTER
========================================================= */

.event-poster{

    display:block;

    max-width:100%;

    width:auto;

    height:500px;

    object-fit:contain;

    border-radius:12px;

    box-shadow:0 8px 25px rgba(0,0,0,.15);

}


/* =========================================================
   NO POSTER
========================================================= */

.no-poster{

    width:100%;

    height:450px;

    display:flex;

    align-items:center;

    justify-content:center;

    color:#aaa;

    font-size:65px;

}


/* =========================================================
   EVENT CONTENT
========================================================= */

.event-content{

    padding:35px;

}


.event-title{

    color:#800020;

    font-size:30px;

    font-weight:700;

    margin-bottom:10px;

}


.event-divider{

    width:70px;

    height:4px;

    background:#D4AF37;

    border-radius:10px;

    margin-bottom:25px;

}


/* =========================================================
   DESCRIPTION
========================================================= */

.description-title{

    color:#800020;

    font-size:18px;

    font-weight:600;

    margin-bottom:8px;

}


.description{

    color:#555;

    line-height:1.8;

    margin-bottom:30px;

}


/* =========================================================
   EVENT INFORMATION
========================================================= */

.info-grid{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:15px;

    margin-bottom:30px;

}


.info-box{

    background:#faf5f6;

    border-radius:15px;

    padding:18px;

    display:flex;

    align-items:center;

    gap:15px;

    border:1px solid #f0dddd;

}


.info-icon{

    width:45px;

    height:45px;

    min-width:45px;

    border-radius:12px;

    background:#800020;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:18px;

}


.info-text small{

    display:block;

    color:#888;

    font-size:12px;

    margin-bottom:3px;

}


.info-text strong{

    color:#333;

    font-size:15px;

}


/* =========================================================
   STATUS
========================================================= */

.status-section{

    margin-bottom:25px;

    display:flex;

    align-items:center;

    gap:10px;

    flex-wrap:wrap;

}


.status-open{

    background:#198754;

    color:white;

    padding:8px 18px;

    border-radius:20px;

    font-size:13px;

}


.status-closed{

    background:#dc3545;

    color:white;

    padding:8px 18px;

    border-radius:20px;

    font-size:13px;

}


/* =========================================================
   BUTTONS
========================================================= */

.action-buttons{

    display:flex;

    gap:12px;

    flex-wrap:wrap;

}


.btn-register{

    background:#800020;

    color:white;

    border:none;

    padding:12px 25px;

    border-radius:10px;

    font-weight:500;

    text-decoration:none;

    transition:.3s;

}


.btn-register:hover{

    background:#a00028;

    color:white;

    transform:translateY(-2px);

}


.btn-back{

    background:#f1f1f1;

    color:#555;

    border:none;

    padding:12px 25px;

    border-radius:10px;

    font-weight:500;

    text-decoration:none;

    transition:.3s;

}


.btn-back:hover{

    background:#ddd;

    color:#333;

}


/* =========================================================
   TABLET
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


    .poster-area{

        min-height:450px;

        padding:20px;

    }


    .event-poster{

        height:450px;

    }


    .no-poster{

        height:400px;

    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width:768px){

    .poster-area{

        min-height:400px;

        padding:15px;

    }


    .event-poster{

        height:400px;

    }


    .event-content{

        padding:25px;

    }


    .event-title{

        font-size:25px;

    }


    .info-grid{

        grid-template-columns:1fr;

    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width:576px){

    .main{

        padding:20px 14px;

        padding-top:64px;

    }


    .event-card{

        border-radius:18px;

    }


    .poster-area{

        min-height:auto;

        padding:15px;

    }


    .event-poster{

        width:100%;

        height:auto;

        max-height:none;

        object-fit:contain;

        border-radius:8px;

    }


    .no-poster{

        height:250px;

        font-size:45px;

    }


    .event-content{

        padding:20px;

    }


    .event-title{

        font-size:22px;

    }


    .description{

        font-size:14px;

    }


    .info-box{

        padding:14px;

    }


    .action-buttons{

        flex-direction:column;

    }


    .btn-register,
    .btn-back{

        width:100%;

        text-align:center;

    }

}

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

            <i class="fa fa-calendar-day"></i>

            Event Details

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
         EVENT
    ================================================= -->

    <div class="event-container">


        <div class="event-card">


            <!-- =================================================
                 POSTER
            ================================================= -->

            <div class="poster-area">


                <?php

                if(!empty($row['poster']))
                {

                ?>


                    <img
                        src="../uploads/<?php echo htmlspecialchars($row['poster']); ?>"
                        class="event-poster"
                        alt="Event Poster"
                    >


                <?php

                }
                else
                {

                ?>


                    <div class="no-poster">

                        <i class="fa fa-image"></i>

                    </div>


                <?php

                }

                ?>


            </div>



            <!-- =================================================
                 EVENT CONTENT
            ================================================= -->

            <div class="event-content">


                <!-- EVENT TITLE -->

                <h1 class="event-title">

                    <?php

                    echo htmlspecialchars(
                        $row['event_title']
                    );

                    ?>

                </h1>


                <div class="event-divider"></div>



                <!-- DESCRIPTION -->

                <div class="description-title">

                    <i class="fa fa-circle-info"></i>

                    About This Event

                </div>


                <div class="description">

                    <?php

                    if(!empty($row['description']))
                    {

                        echo nl2br(
                            htmlspecialchars(
                                $row['description']
                            )
                        );

                    }
                    else
                    {

                        echo "No description available for this event.";

                    }

                    ?>

                </div>



                <!-- =================================================
                     EVENT INFORMATION
                ================================================= -->

                <div class="info-grid">


                    <!-- DATE -->

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="fa fa-calendar"></i>

                        </div>


                        <div class="info-text">

                            <small>

                                Date

                            </small>


                            <strong>

                                <?php

                                echo date(
                                    "d M Y",
                                    strtotime($row['event_date'])
                                );

                                ?>

                            </strong>

                        </div>

                    </div>



                    <!-- TIME -->

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="fa fa-clock"></i>

                        </div>


                        <div class="info-text">

                            <small>

                                Time

                            </small>


                            <strong>

                                <?php

                                echo date('h:i A', strtotime($row['start_time']));

                                ?>

                                -

                                <?php

                                echo date('h:i A', strtotime($row['end_time']));

                                ?>

                            </strong>

                        </div>

                    </div>



                    <!-- VENUE -->

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="fa fa-location-dot"></i>

                        </div>


                        <div class="info-text">

                            <small>

                                Venue

                            </small>


                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row['venue']
                                );

                                ?>

                            </strong>

                        </div>

                    </div>



                    <!-- QUOTA -->

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="fa fa-users"></i>

                        </div>


                        <div class="info-text">

                            <small>

                                Quota

                            </small>


                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row['quota']
                                );

                                ?>

                                Participants

                            </strong>

                        </div>

                    </div>


                </div>



                <!-- =================================================
                     STATUS
                ================================================= -->

                <div class="status-section">


                    <strong>

                        Registration Status:

                    </strong>


                    <?php

                    if($row['status'] == "Open")
                    {

                    ?>


                        <span class="status-open">

                            <i class="fa fa-check-circle"></i>

                            Open

                        </span>


                    <?php

                    }
                    else
                    {

                    ?>


                        <span class="status-closed">

                            <i class="fa fa-xmark-circle"></i>

                            Closed

                        </span>


                    <?php

                    }

                    ?>


                </div>



                <!-- =================================================
                     ACTION BUTTONS
                ================================================= -->

                <div class="action-buttons">


                    <?php

                    if($row['status'] == "Open")
                    {

                    ?>


                        <a
                            href="register_event.php?id=<?php echo $row['event_id']; ?>"
                            class="btn-register"
                        >

                            <i class="fa fa-user-plus"></i>

                            Register Event

                        </a>


                    <?php

                    }
                    else
                    {

                    ?>


                        <button
                            class="btn btn-secondary"
                            disabled
                        >

                            <i class="fa fa-lock"></i>

                            Registration Closed

                        </button>


                    <?php

                    }

                    ?>


                    <!-- BACK TO MY EVENTS -->

                    <a
                        href="my_events.php"
                        class="btn-back"
                    >

                        <i class="fa fa-arrow-left"></i>

                        Back to My Events

                    </a>


                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>

