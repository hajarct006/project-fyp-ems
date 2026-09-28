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
| TOTAL REGISTERED EVENTS
|--------------------------------------------------------------------------
*/

$total_registered = mysqli_fetch_assoc(

    mysqli_query(
        $conn,

        "
        SELECT COUNT(*) AS total

        FROM registrations

        WHERE user_id='$user_id'
        "
    )

)['total'];


/*
|--------------------------------------------------------------------------
| TOTAL ATTENDANCE
|--------------------------------------------------------------------------
*/

$total_attendance = mysqli_fetch_assoc(

    mysqli_query(
        $conn,

        "
        SELECT COUNT(*) AS total

        FROM attendance

        INNER JOIN registrations

        ON attendance.registration_id = registrations.registration_id

        WHERE registrations.user_id='$user_id'

        AND attendance.attendance_status='Present'
        "
    )

)['total'];


/*
|--------------------------------------------------------------------------
| UPCOMING EVENTS
|--------------------------------------------------------------------------
*/

$total_events = mysqli_fetch_assoc(

    mysqli_query(
        $conn,

        "
        SELECT COUNT(*) AS total

        FROM events

        WHERE event_date >= CURDATE()
        "
    )

)['total'];


/*
|--------------------------------------------------------------------------
| LATEST EVENTS
|--------------------------------------------------------------------------
*/

$latest = mysqli_query(

    $conn,

    "
    SELECT *

    FROM events

    WHERE event_date IS NOT NULL

    AND event_date != '0000-00-00'

    ORDER BY event_date ASC

    LIMIT 5
    "

);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>User Dashboard</title>


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


<!-- DM SANS + PLAYFAIR (matches the homepage / admin dashboard theme) -->

<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
    rel="stylesheet"
>


<style>
:root{
    --maroon:#800020;
    --wine:#5c0017;
    --deep:#3a0010;
    --gold:#d4af37;
    --gold2:#e0c476;
    --cream:#fbf7ef;
    --ink:#271d20;
}
*{margin:0;padding:0;box-sizing:border-box;font-family:'DM Sans',sans-serif}
body{
    background:#f8f6f1;
    color:var(--ink);
    min-height:100vh;
}
.main{margin-left:250px;padding:30px}
.kicker{
    display:inline-flex;align-items:center;gap:8px;color:var(--maroon);
    font-weight:800;letter-spacing:1.5px;text-transform:uppercase;font-size:11px;margin-bottom:4px
}
.kicker::before{content:"";width:22px;height:2px;background:var(--gold);border-radius:2px}
.topbar{
    background:#fffdf9;padding:20px 25px;border-radius:22px;border:1px solid #e7dfd0;
    border-left:4px solid var(--gold);box-shadow:0 10px 28px rgba(64,31,18,.08);
    display:flex;justify-content:space-between;align-items:center;margin-bottom:25px
}
.topbar h3{color:var(--maroon);font-family:'Playfair Display',serif;font-weight:700;font-size:26px;margin:0}
.topbar span{color:#55484b;background:#fff;padding:9px 18px;border-radius:30px;box-shadow:0 4px 12px rgba(64,31,18,.06);border:1px solid #eadfce}
.card-box{
    position:relative;overflow:hidden;border-radius:24px;padding:28px;color:#fff;
    transition:.3s;box-shadow:0 12px 28px rgba(52,0,18,.12);border:1px solid rgba(128,0,32,.08)
}
.card-box::after{content:"";position:absolute;width:110px;height:110px;border:1px solid rgba(255,255,255,.13);border-radius:50%;right:-40px;top:-45px}
.card-box:hover{transform:translateY(-5px);box-shadow:0 20px 38px rgba(52,0,18,.16)}
.card-box::before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(160deg,rgba(255,255,255,.10),rgba(255,255,255,0) 45%);pointer-events:none}
.card-box::after{border-color:rgba(255,255,255,.13)}
.card1,.card2,.card3{background:linear-gradient(145deg,#7a0022,#3a0010);color:#fff}
.card-box .card-icon{width:52px;height:52px;border-radius:15px;background:rgba(255,255,255,.13);color:#fff;display:grid;place-items:center;font-size:21px;margin:0 auto 15px;position:relative;z-index:1}
.card-box h2{font-family:'Playfair Display',serif;color:inherit;font-weight:800;font-size:34px;margin:0;position:relative;z-index:1}
.card-box p{color:rgba(255,255,255,.92);font-size:13px;font-weight:700;margin:5px 0 0;position:relative;z-index:1}
.table-card{background:#fff;border-radius:22px;padding:28px;border:1px solid #e7dfd0;border-top:3px solid var(--maroon);box-shadow:0 12px 30px rgba(64,31,18,.08);margin-top:30px}
.table-title{font-size:24px;font-weight:700;font-family:'Playfair Display',serif;color:var(--maroon);margin-bottom:20px;display:flex;align-items:center;gap:10px}
.table-title i{color:var(--maroon)}
.table-card .table{color:var(--ink);margin-bottom:0}
.table thead.table-dark th{background:linear-gradient(135deg,#800020,#5c0017)!important;color:#fff;border:0;font-size:14px;padding:14px 16px}
.table-card .table td{font-size:14.5px;padding:14px 16px;border-color:#e5e1dc;color:#332b2e;background:#fff}
.table-card .table-hover>tbody>tr:hover>*{background:#fffaf0;color:#332b2e}
.table-card .badge{font-weight:700;padding:7px 11px;border-radius:20px}
@media(max-width:991px){.main{margin-left:0;padding:78px 18px 25px}.topbar{flex-wrap:wrap;gap:12px}.topbar h3{font-size:20px}}
@media(max-width:576px){.main{padding:70px 12px 20px}.card-box{padding:22px}.table-card{padding:18px}}

</style>

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| LOAD USER MENU
|--------------------------------------------------------------------------
*/

include("user_menu.php");

?>


<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main">


    <!-- TOP BAR -->

    <div class="topbar dash-welcome">

        <div>
        <div class="kicker">Overview</div>
        <h3>

            User Dashboard

        </h3>
        </div>


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
         DASHBOARD STATISTICS
    ================================================= -->

    <div class="row">


        <!-- REGISTERED EVENTS -->

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card-box card1 shadow text-center">

                <div class="card-icon"><i class="fa fa-calendar-check"></i></div>

                <h2>

                    <?php

                    echo $total_registered;

                    ?>

                </h2>

                <p>

                    Registered Events

                </p>

            </div>

        </div>



        <!-- ATTENDANCE -->

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card-box card2 shadow text-center">

                <div class="card-icon"><i class="fa fa-user-check"></i></div>

                <h2>

                    <?php

                    echo $total_attendance;

                    ?>

                </h2>

                <p>

                    Total Attendance

                </p>

            </div>

        </div>



        <!-- UPCOMING EVENTS -->

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card-box card3 shadow text-center">

                <div class="card-icon"><i class="fa fa-calendar-days"></i></div>

                <h2>

                    <?php

                    echo $total_events;

                    ?>

                </h2>

                <p>

                    Upcoming Events

                </p>

            </div>

        </div>


    </div>



    <!-- =================================================
         LATEST EVENTS
    ================================================= -->

    <div class="table-card">


        <div class="kicker">Recent Activity</div>
        <div class="table-title">

            <i class="fa fa-calendar"></i>

            Latest Events

        </div>


        <div class="table-responsive">


            <table class="table table-hover align-middle">


                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Event</th>

                        <th>Date</th>

                        <th>Venue</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if(mysqli_num_rows($latest) > 0)
                {

                    while($row = mysqli_fetch_assoc($latest))
                    {

                ?>


                    <tr>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['event_id']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['event_title']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime($row['event_date'])
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['venue']
                            );

                            ?>

                        </td>


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


                    </tr>


                <?php

                    }

                }

                else
                {

                ?>


                    <tr>

                        <td
                            colspan="5"
                            class="text-center"
                        >

                            No Event Available

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

