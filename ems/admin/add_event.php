<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

$message = "";

if(isset($_POST['save']))
{

    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $description = mysqli_real_escape_string($conn,$_POST['description']);
    $venue = mysqli_real_escape_string($conn,$_POST['venue']);
    $date = $_POST['event_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $quota = $_POST['quota'];
    $status = $_POST['status'];

    $poster="";

    if($_FILES['poster']['name']!="")
    {

        if(!is_dir("../uploads"))
        {
            mkdir("../uploads",0777,true);
        }

        $poster=time()."_".$_FILES['poster']['name'];

        move_uploaded_file(
            $_FILES['poster']['tmp_name'],
            "../uploads/".$poster
        );

    }

    $sql="INSERT INTO events
    (
        event_title,
        description,
        venue,
        event_date,
        start_time,
        end_time,
        quota,
        poster,
        status
    )

    VALUES

    (
        '$title',
        '$description',
        '$venue',
        '$date',
        '$start',
        '$end',
        '$quota',
        '$poster',
        '$status'
    )";

    if(mysqli_query($conn,$sql))
    {

        $message="<div class='alert alert-success'>
        Event Successfully Added.
        </div>";

    }
    else
    {

        $message="<div class='alert alert-danger'>
        Failed to Add Event.
        </div>";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Event</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'DM Sans',sans-serif;
}

body{
background:#f4f4f4;
}

.sidebar{
position:fixed;
width:250px;
height:100vh;
background:#800020;
}

.sidebar .logo{
text-align:center;
padding:30px;
color:white;
}

.sidebar .logo img{
width:80px;
margin-bottom:10px;
}

.sidebar a{
display:block;
padding:16px 25px;
text-decoration:none;
color:white;
transition:.3s;
}

.sidebar a:hover{
background:#a00028;
padding-left:35px;
}

.main{
margin-left:250px;
padding:30px;
}

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

.form-card{
background:white;
padding:35px;
border-radius:20px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.form-control,
.form-select{
border-radius:10px;
height:48px;
}

textarea{
height:140px !important;
resize:none;
}

.btn-save{
background:#D4AF37;
color:#800020;
font-weight:bold;
border:none;
border-radius:30px;
padding:12px;
}

.btn-save:hover{
background:#FFD700;
}

.btn-back{
background:#800020;
color:white;
border-radius:30px;
padding:12px;
}

.preview{
width:100%;
height:250px;
border:2px dashed #ddd;
border-radius:15px;
object-fit:cover;
margin-top:10px;
}


/* ================= RESPONSIVE SIDEBAR ================= */

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

@media (max-width:991px){

.menu-toggle{
display:flex;
}

.sidebar{
transform:translateX(-100%);
transition:.3s ease;
z-index:1100;
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

}

@media (max-width:576px){

.main{
padding:20px 14px;
padding-top:64px;
}

.card-box{
padding:20px;
}

.table-card, .qr-card{
padding:18px;
}

}

</style>

</head>

<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.add('active');document.querySelector('.sidebar-overlay').classList.add('active');">
<i class="fa fa-bars"></i>
</button>

<div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('active');this.classList.remove('active');"></div>

<div class="sidebar">
<button class="sidebar-close" onclick="document.querySelector('.sidebar').classList.remove('active');document.querySelector('.sidebar-overlay').classList.remove('active');">
<i class="fa fa-xmark"></i>
</button>


<div class="logo">

<img src="../assets/images/logo.png">

<h4>JPP EMS</h4>

</div>

<a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>

<a href="events.php"><i class="fa fa-calendar"></i> Events</a>

<a href="participants.php"><i class="fa fa-users"></i> Participants</a>

<a href="attendance.php"><i class="fa fa-qrcode"></i> Attendance</a>

<a href="reports.php"><i class="fa fa-chart-column"></i> Reports</a>

<a href="exemption.php"><i class="fa fa-file-shield"></i> Exemption</a>

<a href="manage_admins.php"><i class="fa fa-user-shield"></i> Manage Admins</a>

<a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>

</div>

<div class="main">

<div class="topbar">

<h3 style="color:#800020;font-weight:bold;">

Add New Event

</h3>

<span>

Welcome,

<b><?php echo $_SESSION['admin_name']; ?></b>

</span>

</div>

<div class="form-card">

<?php echo $message; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Event Title

</label>

<input
type="text"
name="title"
class="form-control"
placeholder="Enter Event Title"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Venue

</label>

<input
type="text"
name="venue"
class="form-control"
placeholder="Enter Venue"
required>

</div>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
placeholder="Event Description"
required></textarea>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Event Date

</label>

<input
    type="text"
    id="event_date"
    name="event_date"
    class="form-control"
    placeholder="Select event date"
    autocomplete="off"
    required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Start Time

</label>

<input
type="text"
id="start_time"
name="start_time"
class="form-control"
placeholder="Select start time"
autocomplete="off"
required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

End Time

</label>

<input
type="text"
id="end_time"
name="end_time"
class="form-control"
placeholder="Select end time"
autocomplete="off"
required>

</div>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Quota

</label>

<input
type="number"
name="quota"
class="form-control"
required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option value="Open">

Open

</option>

<option value="Closed">

Closed

</option>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Event Poster

</label>

<input
type="file"
name="poster"
id="poster"
class="form-control"
accept=".jpg,.jpeg,.png">

</div>

</div>

<div class="mb-4">

<img
src="../assets/images/no-image.png"
id="preview"
class="preview">

</div>

<div class="row">

<div class="col-md-6">

<a
href="events.php"
class="btn btn-back w-100">

<i class="fa fa-arrow-left"></i>

Back

</a>

</div>

<div class="col-md-6">

<button
type="submit"
name="save"
class="btn btn-save w-100">

<i class="fa fa-floppy-disk"></i>

Save Event

</button>

</div>

</div>

</form>

</div>

</div>

<script>

document
.getElementById("poster")
.onchange=function(e)
{

const file=e.target.files[0];

if(file)
{

document
.getElementById("preview")
.src=URL.createObjectURL(file);

}

}

</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

flatpickr("#event_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d M Y",
    minDate: "today",
    disableMobile: true
});

flatpickr("#start_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    disableMobile: true
});

flatpickr("#end_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    disableMobile: true
});

</script>

</body>

</html>