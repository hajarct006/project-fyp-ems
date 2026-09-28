<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../includes/db.php");


/* =========================
   SELECT CATEGORY
========================= */

$category = "all";

if(isset($_GET['category']))
{
    $category = $_GET['category'];
}


/* =========================
   GET TODAY
========================= */

$today = date("Y-m-d");


/* =========================
   GET EVENTS
========================= */

if($category == "upcoming")
{

    /*
    | Upcoming:
    | Nearest event first
    */

    $sql = "
        SELECT *
        FROM events
        WHERE event_date >= '$today'
        ORDER BY event_date ASC, start_time ASC
    ";

}
elseif($category == "past")
{

    /*
    | Past:
    | Most recently finished event first
    */

    $sql = "
        SELECT *
        FROM events
        WHERE event_date < '$today'
        ORDER BY event_date DESC, start_time DESC
    ";

}
else
{

    /*
    | ALL:
    | Upcoming events first
    | Past events below
    |
    | Example:
    | 25 Aug 2026
    | 28 Aug 2026
    | 02 Sep 2026
    | ----------------
    | 20 Aug 2026
    | 15 Aug 2026
    | 10 Aug 2026
    */

    $sql = "
        SELECT *
        FROM events
        ORDER BY
            CASE
                WHEN event_date >= '$today' THEN 0
                ELSE 1
            END ASC,

            CASE
                WHEN event_date >= '$today'
                THEN event_date
            END ASC,

            CASE
                WHEN event_date < '$today'
                THEN event_date
            END DESC,

            start_time ASC
    ";

}


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Manage Events</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">


<link
href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
rel="stylesheet">


<style>

/* =========================
   GENERAL
========================= */

*{

margin:0;

padding:0;

box-sizing:border-box;

font-family:'DM Sans',sans-serif;

}


body{

background:#f6f4f2;

}


/* =========================
   MAIN
========================= */

.main{

margin-left:250px;

padding:30px;

}


/* =========================
   TOP BAR
========================= */

.topbar{

background:white;

padding:18px 30px;

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


/* =========================
   ADD BUTTON
========================= */

.add-btn{

background:#D4AF37;

color:#800020;

font-weight:bold;

border:none;

padding:10px 20px;

border-radius:30px;

text-decoration:none;

}


.add-btn:hover{

background:#FFD700;

color:#800020;

}


/* =========================
   CATEGORY BOX
========================= */

.category-box{

background:white;

padding:20px 25px;

border-radius:15px;

box-shadow:0 10px 25px rgba(0,0,0,.08);

margin-bottom:30px;

display:flex;

align-items:center;

justify-content:space-between;

gap:20px;

}


.category-label{

font-size:18px;

font-weight:600;

color:#800020;

}


.category-label i{

margin-right:8px;

}


.category-select{

width:300px;

height:45px;

border:1px solid #ddd;

border-radius:10px;

padding:0 15px;

font-size:15px;

color:#555;

background:white;

outline:none;

cursor:pointer;

}


.category-select:focus{

border-color:#800020;

box-shadow:0 0 0 3px rgba(128,0,32,.10);

}


/* =========================
   CATEGORY TITLE
========================= */

.category-title{

color:#800020;

font-size:24px;

font-weight:600;

margin-bottom:20px;

}


.category-title i{

margin-right:8px;

}


/* =========================
   EVENT CARD
========================= */

.event-card{

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


.poster{

width:100%;

height:405px;

object-fit:cover;

}


.card-body{

padding:20px;

}


.card-body h4{

color:#800020;

font-weight:bold;

}


.card-body p{

margin-bottom:10px;

}


.badge-open{

background:#198754;

padding:8px 15px;

border-radius:20px;

}


.badge-close{

background:#dc3545;

padding:8px 15px;

border-radius:20px;

}


.btn-edit{

background:#0d6efd;

color:white;

}


.btn-edit:hover{

background:#0b5ed7;

color:white;

}


.btn-delete{

background:#dc3545;

color:white;

}


.btn-delete:hover{

background:#bb2d3b;

color:white;

}


/* =========================
   EMPTY
========================= */

.empty-box{

background:white;

padding:40px;

border-radius:15px;

text-align:center;

box-shadow:0 5px 15px rgba(0,0,0,.05);

}


.empty-box i{

font-size:40px;

color:#800020;

margin-bottom:15px;

}


.empty-box h5{

font-weight:600;

}


/* =========================
   PAST EVENT LABEL
========================= */

.past-label{

background:#6c757d;

color:white;

padding:6px 12px;

border-radius:20px;

font-size:12px;

display:inline-block;

margin-bottom:10px;

}


/* =========================
   MOBILE MENU
========================= */

.menu-toggle{

display:none;

position:fixed;

top:14px;

left:14px;

z-index:1200;

background:#800020;

color:white;

border:2px solid rgba(255,255,255,.9);

width:36px;

height:36px;

border-radius:9px;

font-size:15px;

box-shadow:0 4px 12px rgba(0,0,0,.5);

align-items:center;

justify-content:center;

}


.sidebar-overlay{

display:none;

position:fixed;

top:0;

left:0;

width:100%;

height:100%;

background:rgba(0,0,0,.55);

z-index:1099;

}


.sidebar-close{

display:none;

position:absolute;

top:14px;

right:14px;

background:transparent;

border:none;

color:white;

font-size:22px;

}


/* =========================
   TABLET
========================= */

@media(max-width:991px){

.menu-toggle{

display:flex;

}


.sidebar{

transform:translateX(-100%);

transition:.3s ease;

box-shadow:0 0 30px rgba(0,0,0,.3);

overflow-y:auto;

}


.sidebar.active{

transform:translateX(0);

}


.sidebar-close{

display:block;

}


.sidebar-overlay.active{

display:block;

}


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


.category-box{

flex-direction:column;

align-items:stretch;

}


.category-select{

width:100%;

}

}


/* =========================
   MOBILE
========================= */

@media(max-width:576px){

.main{

padding:20px 14px;

padding-top:64px;

}


.topbar{

padding:18px 20px;

}


.category-box{

padding:18px;

}


.category-title{

font-size:21px;

}


.poster{

height:200px;

}

}


/* equal-size event cards */
.row>[class*="col-"]>.event-card{height:100%;display:flex;flex-direction:column}
.event-card .poster{flex:0 0 auto}
.event-card .card-body{flex:1;display:flex;flex-direction:column}
.event-card .card-body .d-grid{margin-top:auto}
</style>

</head>


<body>


<!-- =========================
     USE YOUR EXISTING MENU
========================= -->

<?php include("admin_menu.php"); ?>


<!-- =========================
     MAIN
========================= -->

<div class="main">


<!-- =========================
     TOP BAR
========================= -->

<div class="topbar">

<h3>

Manage Events

</h3>


<a
href="add_event.php"
class="add-btn">

<i class="fa fa-plus"></i>

Add Event

</a>

</div>


<!-- =========================
     CATEGORY SELECT
========================= -->

<div class="category-box">

<div class="category-label">

<i class="fa fa-filter"></i>

Select Category

</div>


<form
method="GET"
action="events.php"
id="categoryForm">


<select
name="category"
class="category-select"
onchange="document.getElementById('categoryForm').submit();">


<option
value="all"
<?php

if($category == "all")
{
    echo "selected";
}

?>>

All Programs

</option>


<option
value="upcoming"
<?php

if($category == "upcoming")
{
    echo "selected";
}

?>>

Upcoming Programs

</option>


<option
value="past"
<?php

if($category == "past")
{
    echo "selected";
}

?>>

Past Programs

</option>


</select>

</form>

</div>


<!-- =========================
     CATEGORY TITLE
========================= -->

<?php

if($category == "upcoming")
{

?>

<h4 class="category-title">

<i class="fa fa-calendar-days"></i>

Upcoming Programs

</h4>

<?php

}
elseif($category == "past")
{

?>

<h4 class="category-title">

<i class="fa fa-calendar-check"></i>

Past Programs

</h4>

<?php

}
else
{

?>

<h4 class="category-title">

<i class="fa fa-calendar"></i>

All Programs

</h4>

<?php

}

?>


<!-- =========================
     EVENT LIST
========================= -->

<div class="row">


<?php

if(mysqli_num_rows($result) > 0)
{

    while($row = mysqli_fetch_assoc($result))
    {

        $isPast = ($row['event_date'] < $today);

?>


<div class="col-lg-4 col-md-6 mb-4">


<div class="event-card">


<!-- =========================
     POSTER
========================= -->

<?php

if($row['poster'] == "")
{

?>

<img
src="../assets/images/no-image.png"
class="poster"
alt="No Image">

<?php

}
else
{

?>

<img
src="../uploads/<?php echo htmlspecialchars($row['poster']); ?>"
class="poster"
alt="Event Poster">

<?php

}

?>


<!-- =========================
     CARD BODY
========================= -->

<div class="card-body">


<?php

if($isPast)
{

?>

<span class="past-label">

<i class="fa fa-clock-rotate-left"></i>

Past Event

</span>

<?php

}

?>


<h4>

<?php

echo htmlspecialchars(
    $row['event_title']
);

?>

</h4>


<hr>


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


<p>

<i class="fa fa-clock text-warning"></i>

<b>Time :</b>

<?php

echo date(
    "h:i A",
    strtotime($row['start_time'])
);

?>

-

<?php

echo date(
    "h:i A",
    strtotime($row['end_time'])
);

?>

</p>


<p>

<i class="fa fa-location-dot text-primary"></i>

<b>Venue :</b>

<?php

echo htmlspecialchars(
    $row['venue']
);

?>

</p>


<p>

<i class="fa fa-users text-success"></i>

<b>Quota :</b>

<?php

echo htmlspecialchars(
    $row['quota']
);

?>

</p>


<!-- =========================
     STATUS
========================= -->

<p>

<?php

if($isPast)
{

?>

<span class="badge badge-close">

Completed

</span>

<?php

}
else
{

    if($row['status'] == "Open")
    {

?>

<span class="badge badge-open">

Open

</span>

<?php

    }
    else
    {

?>

<span class="badge badge-close">

Closed

</span>

<?php

    }

}

?>

</p>


<!-- =========================
     BUTTONS
========================= -->

<div class="d-grid gap-2">


<a
href="edit_event.php?id=<?php echo urlencode($row['event_id']); ?>"
class="btn btn-edit">

<i class="fa fa-pen"></i>

Edit Event

</a>


<a
href="delete_event.php?id=<?php echo urlencode($row['event_id']); ?>"
class="btn btn-delete"

onclick="return confirm('Are you sure want to delete this event?')">

<i class="fa fa-trash"></i>

Delete Event

</a>


</div>


</div>


</div>


</div>


<?php

    }

}
else
{

?>


<div class="col-12">


<div class="empty-box">


<i class="fa fa-calendar-xmark"></i>


<h5>

No Events Found

</h5>


<p class="text-muted">

<?php

if($category == "upcoming")
{
    echo "There are currently no upcoming programs.";
}
elseif($category == "past")
{
    echo "There are currently no past programs.";
}
else
{
    echo "There are currently no programs available.";
}

?>

</p>


</div>


</div>


<?php

}

?>


</div>


</div>


<!-- =========================
     JAVASCRIPT
========================= -->

<script>

function openSidebar()
{

    document
    .querySelector('.sidebar')
    .classList
    .add('active');


    document
    .querySelector('.sidebar-overlay')
    .classList
    .add('active');

}


function closeSidebar()
{

    document
    .querySelector('.sidebar')
    .classList
    .remove('active');


    document
    .querySelector('.sidebar-overlay')
    .classList
    .remove('active');

}

</script>


</body>

</html>