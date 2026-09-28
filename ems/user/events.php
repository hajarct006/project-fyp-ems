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
| GET AVAILABLE EVENTS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *

FROM events

WHERE event_date IS NOT NULL

AND event_date != '0000-00-00'

ORDER BY event_date ASC
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

<title>Available Events</title>


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
   EVENT TABLE
========================================================= */

.table-wrapper{

    background:white;

    border-radius:20px;

    overflow:hidden;

    box-shadow:0 10px 25px rgba(0,0,0,.08);

}


.table-wrapper table{

    margin-bottom:0;

}


.table-wrapper thead{

    background:#800020;

    color:white;

}


.table-wrapper thead th{

    border:none;

    vertical-align:middle;

    padding:16px;

    font-weight:600;

}


.table-wrapper tbody td{

    vertical-align:middle;

    padding:14px 16px;

}


.table-wrapper tbody tr:hover{

    background:#fbf3f4;

}


/* =========================================================
   EVENT POSTER
========================================================= */

.poster-thumb{

    display:block;

    width:90px;

    height:60px;

    object-fit:cover;

    border-radius:8px;

}


/* =========================================================
   EVENT TITLE
========================================================= */

.event-title{

    color:#800020;

    font-weight:bold;

    margin-bottom:2px;

}


/* =========================================================
   RESPONSIVE CONTENT
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


    .table-wrapper{

        border-radius:15px;

    }

}


/* date + time stay on one line */
.table td:nth-child(3),.table td:nth-child(4),
.table th:nth-child(3),.table th:nth-child(4){white-space:nowrap}
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

            Available Events

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
         EVENT TABLE
    ================================================= -->

    <div class="table-wrapper">


        <div class="table-responsive">


            <table class="table table-hover align-middle mb-0">


                <thead>


                    <tr>


                        <th>

                            Poster

                        </th>


                        <th>

                            Event

                        </th>


                        <th>

                            <i class="fa fa-calendar"></i>

                            Date

                        </th>


                        <th>

                            <i class="fa fa-clock"></i>

                            Time

                        </th>


                        <th>

                            <i class="fa fa-location-dot"></i>

                            Venue

                        </th>


                        <th>

                            <i class="fa fa-users"></i>

                            Quota

                        </th>


                        <th>

                            Status

                        </th>


                        <th>

                            Action

                        </th>


                    </tr>


                </thead>


                <tbody>


                <?php


                if(mysqli_num_rows($result) > 0)
                {


                    while($row = mysqli_fetch_assoc($result))
                    {


                        $event_id = $row['event_id'];


                        /*
                        |--------------------------------------------------------------------------
                        | CHECK WHETHER USER ALREADY REGISTERED
                        |--------------------------------------------------------------------------
                        */

                        $check = mysqli_query(
                            $conn,

                            "
                            SELECT *

                            FROM registrations

                            WHERE user_id='$user_id'

                            AND event_id='$event_id'
                            "
                        );


                ?>


                    <tr>


                        <!-- POSTER -->

                        <td>


                        <?php

                        if(empty($row['poster']))
                        {

                        ?>


                            <img
                                src="../assets/images/no-image.png"
                                class="poster-thumb"
                                alt="No Image"
                            >


                        <?php

                        }
                        else
                        {

                        ?>


                            <img
                                src="../uploads/<?php echo htmlspecialchars($row['poster']); ?>"
                                class="poster-thumb"
                                alt="Event Poster"
                            >


                        <?php

                        }

                        ?>


                        </td>



                        <!-- EVENT TITLE -->

                        <td>


                            <div class="event-title">

                                <?php

                                echo htmlspecialchars(
                                    $row['event_title']
                                );

                                ?>

                            </div>


                        </td>



                        <!-- DATE -->

                        <td>


                            <?php

                            echo date(
                                "d M Y",
                                strtotime($row['event_date'])
                            );

                            ?>


                        </td>



                        <!-- TIME -->

                        <td>


                            <?php

                            echo date('h:i A', strtotime($row['start_time']));

                            ?>

                            -

                            <?php

                            echo date('h:i A', strtotime($row['end_time']));

                            ?>


                        </td>



                        <!-- VENUE -->

                        <td>


                            <?php

                            echo htmlspecialchars(
                                $row['venue']
                            );

                            ?>


                        </td>



                        <!-- QUOTA -->

                        <td>


                            <?php

                            echo htmlspecialchars(
                                $row['quota']
                            );

                            ?>


                        </td>



                        <!-- STATUS -->

                        <td>


                        <?php

                        if($row['status'] == "Open")
                        {

                        ?>


                            <span class="badge bg-success">

                                Open

                            </span>


                        <?php

                        }
                        else
                        {

                        ?>


                            <span class="badge bg-danger">

                                Closed

                            </span>


                        <?php

                        }

                        ?>


                        </td>



                        <!-- ACTION -->

                        <td>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | ALREADY REGISTERED
                        |--------------------------------------------------------------------------
                        */

                        if(mysqli_num_rows($check) > 0)
                        {

                        ?>


                            <button
                                class="btn btn-success btn-sm w-100"
                                disabled
                            >

                                <i class="fa fa-check"></i>

                                Registered

                            </button>


                        <?php

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | EVENT IS OPEN
                        |--------------------------------------------------------------------------
                        */

                        else if($row['status'] == "Open")
                        {

                        ?>


                            <a
                                href="register_event.php?id=<?php echo $event_id; ?>"
                                class="btn btn-primary btn-sm w-100"
                            >

                                <i class="fa fa-user-plus"></i>

                                Register Now

                            </a>


                        <?php

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | EVENT IS CLOSED
                        |--------------------------------------------------------------------------
                        */

                        else
                        {

                        ?>


                            <button
                                class="btn btn-secondary btn-sm w-100"
                                disabled
                            >

                                Registration Closed

                            </button>


                        <?php

                        }

                        ?>


                        </td>


                    </tr>


                <?php

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | NO EVENTS
                |--------------------------------------------------------------------------
                */

                else
                {

                ?>


                    <tr>


                        <td
                            colspan="8"
                            class="p-0"
                        >


                            <div
                                class="alert alert-warning text-center mb-0 rounded-0"
                            >

                                <i class="fa fa-circle-info"></i>

                                No Event Available

                            </div>


                        </td>


                    </tr>


                <?php

                }


                ?>


                </tbody>


            </table>


        </div>


    </div>


</div>


</body>

</html>

