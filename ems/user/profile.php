<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

include("../includes/db.php");

$user_id = intval($_SESSION['user_id']);

$message = "";


/* =========================================================
   GET USER INFORMATION
========================================================= */

$sql = "
    SELECT *
    FROM users
    WHERE user_id='$user_id'
    LIMIT 1
";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0)
{
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$user = mysqli_fetch_assoc($result);


/* =========================================================
   UPDATE PROFILE
========================================================= */

if(isset($_POST['update']))
{

    $education_level = mysqli_real_escape_string(
        $conn,
        $_POST['education_level']
    );

    $semester = mysqli_real_escape_string(
        $conn,
        $_POST['semester']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );


    /* =====================================================
       PROFILE PICTURE
    ===================================================== */

    $profile_picture =
        $user['profile_picture'];


    if(
        isset($_FILES['profile_picture']) &&
        $_FILES['profile_picture']['error'] == 0
    )
    {

        $file_name =
            $_FILES['profile_picture']['name'];

        $file_tmp =
            $_FILES['profile_picture']['tmp_name'];

        $file_size =
            $_FILES['profile_picture']['size'];


        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];


        $extension =
            strtolower(
                pathinfo(
                    $file_name,
                    PATHINFO_EXTENSION
                )
            );


        /* Check file type */

        if(!in_array($extension, $allowed_extensions))
        {

            $message = "
            <div class='alert alert-danger'>
                <i class='fa fa-circle-xmark'></i>
                Only JPG, JPEG, PNG and WEBP images are allowed.
            </div>
            ";

        }


        /* Check file size - 5MB */

        elseif($file_size > 5 * 1024 * 1024)
        {

            $message = "
            <div class='alert alert-danger'>
                <i class='fa fa-circle-xmark'></i>
                Profile picture must be smaller than 5MB.
            </div>
            ";

        }


        else
        {

            /*
             * Generate unique filename
             */

            $new_file_name =
                "user_" .
                $user_id .
                "_" .
                time() .
                "." .
                $extension;


            $upload_directory =
                "../uploads/profile/";


            /*
             * Create folder if it doesn't exist
             */

            if(!is_dir($upload_directory))
            {
                mkdir(
                    $upload_directory,
                    0777,
                    true
                );
            }


            $upload_path =
                $upload_directory .
                $new_file_name;


            if(move_uploaded_file(
                $file_tmp,
                $upload_path
            ))
            {

                /*
                 * Delete old profile picture
                 * if it exists
                 */

                if(
                    !empty($user['profile_picture']) &&
                    file_exists(
                        $upload_directory .
                        $user['profile_picture']
                    )
                )
                {

                    unlink(
                        $upload_directory .
                        $user['profile_picture']
                    );

                }


                $profile_picture =
                    $new_file_name;

            }
            else
            {

                $message = "
                <div class='alert alert-danger'>
                    <i class='fa fa-circle-xmark'></i>
                    Failed to upload profile picture.
                </div>
                ";

            }

        }

    }


    /* =====================================================
       UPDATE DATABASE
    ===================================================== */

    if(empty($message))
    {

        $update_sql = "

            UPDATE users

            SET

                education_level='$education_level',

                semester='$semester',

                phone='$phone',

                email='$email',

                profile_picture='$profile_picture'

            WHERE user_id='$user_id'

        ";


        if(mysqli_query($conn, $update_sql))
        {

            /*
             * Update session name
             * just in case it is needed elsewhere
             */

            $_SESSION['full_name'] =
                $user['full_name'];


            /*
             * Redirect back to profile
             */

            header(
                "Location: profile.php?updated=1"
            );

            exit();

        }
        else
        {

            $message = "
            <div class='alert alert-danger'>
                <i class='fa fa-circle-xmark'></i>
                Failed to update profile.
            </div>
            ";

        }

    }

}


/* =====================================================
   SUCCESS MESSAGE AFTER UPDATE
===================================================== */

if(isset($_GET['updated']))
{

    $message = "

    <div
        class='alert alert-success alert-dismissible fade show'
        role='alert'
    >

        <i class='fa fa-circle-check'></i>

        <strong>
            Profile Updated Successfully!
        </strong>

        <br>

        Your profile information has been updated.

        <button
            type='button'
            class='btn-close'
            data-bs-dismiss='alert'
        ></button>

    </div>

    ";

}


/* =====================================================
   REFRESH USER INFORMATION
===================================================== */

$result = mysqli_query(
    $conn,
    $sql
);

$user = mysqli_fetch_assoc($result);


/* =========================================================
   GET USER IC FOR QR CODE
========================================================= */

$qr_value = "";

$qr_sql = "

    SELECT ic_number

    FROM users

    WHERE user_id='$user_id'

    LIMIT 1

";

$qr_result =
    mysqli_query(
        $conn,
        $qr_sql
    );

if(
    $qr_result &&
    mysqli_num_rows($qr_result) > 0
)
{

    $qr_data =
        mysqli_fetch_assoc(
            $qr_result
        );

    $qr_value =
        trim($qr_data['ic_number']);

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>User Profile</title>


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


<!-- QR CODE -->

<script
src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
</script>


<style>

/* =====================================================
   GENERAL
===================================================== */

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'DM Sans',sans-serif;

}

body{

    background:#f5f5f5;

    overflow-x:hidden;

}


/* =====================================================
   MAIN
===================================================== */

.main{

    margin-left:250px;

    padding:30px;

    min-height:100vh;

}


/* =====================================================
   TOPBAR
===================================================== */

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

    gap:12px;

    flex-wrap:wrap;

}


/* =====================================================
   PROFILE CARD
===================================================== */

.profile-card{

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

}


/* =====================================================
   PROFILE PICTURE
===================================================== */

.profile-picture-wrapper{

    text-align:center;

    margin-bottom:30px;

}

.profile-picture{

    width:180px;

    height:180px;

    border-radius:50%;

    object-fit:cover;

    border:6px solid white;

    box-shadow:
        0 8px 25px rgba(0,0,0,.18);

    cursor:pointer;

    transition:.3s;

}

.profile-picture:hover{

    transform:scale(1.04);

    box-shadow:
        0 12px 30px rgba(128,0,32,.25);

}


.profile-picture-placeholder{

    width:180px;

    height:180px;

    border-radius:50%;

    background:#800020;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:70px;

    margin:auto;

    border:6px solid white;

    box-shadow:
        0 8px 25px rgba(0,0,0,.18);

}


.profile-name{

    margin-top:18px;

    color:#800020;

    font-weight:700;

    font-size:24px;

}


.profile-role{

    color:#777;

    font-size:14px;

}


/* =====================================================
   INFORMATION
===================================================== */

.profile-info{

    margin-top:20px;

}

.info-box{

    background:#f8f8f8;

    border-radius:12px;

    padding:16px;

    min-height:75px;

}

.info-label{

    font-size:13px;

    color:#777;

    margin-bottom:5px;

}

.info-value{

    font-weight:500;

    color:#333;

    word-break:break-word;

}


/* =====================================================
   FORM
===================================================== */

.form-control,
.form-select{

    min-height:48px;

    border-radius:10px;

}

.form-control:focus,
.form-select:focus{

    border-color:#800020;

    box-shadow:
        0 0 0 .2rem rgba(128,0,32,.12);

}


/* =====================================================
   READ ONLY
===================================================== */

.readonly-field{

    background:#eeeeee;

    cursor:not-allowed;

}


/* =====================================================
   BUTTONS
===================================================== */

.btn-save{

    background:#D4AF37;

    color:#800020;

    font-weight:bold;

    border:none;

    border-radius:30px;

    padding:12px;

}

.btn-save:hover{

    background:#c9a52d;

    color:#800020;

}


.btn-edit{

    background:#800020;

    color:white;

    border:none;

    border-radius:30px;

    padding:12px 30px;

    font-weight:bold;

}

.btn-edit:hover{

    background:#a00028;

    color:white;

}

.btn-cancel{

    background:#777;

    color:white;

    border:none;

    border-radius:30px;

    padding:12px 30px;

}

.btn-cancel:hover{

    background:#555;

    color:white;

}


/* =====================================================
   QR
===================================================== */

.user-qr{

    margin-top:35px;

    padding-top:30px;

    border-top:1px solid #ddd;

    text-align:center;

}

.user-qr h5{

    color:#800020;

    font-weight:bold;

}

#qrcode{

    display:flex;

    justify-content:center;

    margin:20px auto;

}

.qr-box{

    background:#f8f8f8;

    border-radius:15px;

    padding:20px;

    display:inline-block;

}

.qr-security{

    font-size:13px;

    color:#777;

    max-width:500px;

    margin:0 auto;

}


/* =====================================================
   PROFILE PICTURE UPLOAD
===================================================== */

.upload-box{

    border:2px dashed #d8b7bf;

    background:#fffafa;

    padding:25px;

    border-radius:15px;

    text-align:center;

}

.upload-box i{

    color:#800020;

    font-size:35px;

    margin-bottom:10px;

}

.upload-box input{

    margin-top:10px;

}


/* =====================================================
   RESPONSIVE
===================================================== */

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

    .topbar h3{

        font-size:20px;

    }

}


@media(max-width:576px){

    .main{

        padding:20px 14px;

        padding-top:64px;

    }

    .profile-card{

        padding:20px;

        border-radius:16px;

    }

    .profile-picture,
    .profile-picture-placeholder{

        width:150px;

        height:150px;

    }

    .profile-picture-placeholder{

        font-size:55px;

    }

    .profile-name{

        font-size:21px;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     USER MENU
===================================================== -->

<?php include("user_menu.php"); ?>


<!-- =====================================================
     MAIN
===================================================== -->

<div class="main">


<!-- =====================================================
     TOPBAR
===================================================== -->

<div class="topbar">

<h3 style="color:#800020;font-weight:bold;margin:0;">

<i class="fa fa-user"></i>

&nbsp; My Profile

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


<!-- =====================================================
     PROFILE CARD
===================================================== -->

<div class="profile-card">


<?php echo $message; ?>


<?php if(!isset($_GET['edit'])) { ?>


<!-- =====================================================
     PROFILE VIEW
===================================================== -->

<div class="profile-picture-wrapper">


<?php

if(
    !empty($user['profile_picture']) &&
    file_exists(
        "../uploads/profile/" .
        $user['profile_picture']
    )
)
{

?>

<img
src="../uploads/profile/<?php
echo htmlspecialchars(
    $user['profile_picture']
);
?>"
class="profile-picture"
alt="Profile Picture"
onclick="openProfilePicture(this.src)"
>

<?php

}
else
{

?>

<div class="profile-picture-placeholder">

<i class="fa fa-user"></i>

</div>

<?php

}

?>


<div class="profile-name">

<?php

echo htmlspecialchars(
    $user['full_name']
);

?>

</div>


<div class="profile-role">

<?php

echo htmlspecialchars(
    $user['department']
);

?>

</div>


</div>


<!-- =====================================================
     INFORMATION
===================================================== -->

<div class="profile-info">

<div class="row">


<!-- FULL NAME -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Full Name
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['full_name']
);

?>

</div>

</div>

</div>


<!-- IC -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
IC Number
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['ic_number']
);

?>

</div>

</div>

</div>


<!-- REGISTRATION -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Registration Number
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['registration_no']
);

?>

</div>

</div>

</div>


<!-- EDUCATION -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Education Level
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['education_level']
);

?>

</div>

</div>

</div>


<!-- DEPARTMENT -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Department
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['department']
);

?>

</div>

</div>

</div>


<!-- SEMESTER -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Semester
</div>

<div class="info-value">

Semester

<?php

echo htmlspecialchars(
    $user['semester']
);

?>

</div>

</div>

</div>


<!-- PHONE -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Phone Number
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['phone']
);

?>

</div>

</div>

</div>


<!-- EMAIL -->

<div class="col-md-6 mb-3">

<div class="info-box">

<div class="info-label">
Email Address
</div>

<div class="info-value">

<?php

echo htmlspecialchars(
    $user['email']
);

?>

</div>

</div>

</div>


</div>


<!-- =====================================================
     EDIT PROFILE BUTTON ONLY
===================================================== -->

<div class="row mt-3">

<div class="col-12">

<a
href="profile.php?edit=1"
class="btn btn-edit w-100">

<i class="fa fa-pen"></i>

&nbsp; Edit Profile

</a>

</div>

</div>


</div>


<!-- =====================================================
     QR CODE
===================================================== -->

<div class="user-qr">

<h5>

<i class="fa fa-qrcode"></i>

&nbsp; My Attendance QR Code

</h5>


<p class="text-muted">

Show this QR code to the event administrator
for attendance.

</p>


<?php if(!empty($qr_value)) { ?>


<div class="qr-box">

<div id="qrcode"></div>

</div>


<div class="qr-security mt-3">

<i
class="fa fa-shield-halved"
style="color:#800020;"
></i>

This QR code is used for attendance verification.

</div>


<?php } else { ?>


<div class="alert alert-warning">

<i class="fa fa-triangle-exclamation"></i>

Your attendance QR code has not been generated yet.

</div>


<?php } ?>


</div>


<?php } else { ?>


<!-- =====================================================
     EDIT PROFILE
===================================================== -->

<h4 style="color:#800020;font-weight:bold;">

<i class="fa fa-pen"></i>

&nbsp; Edit Profile

</h4>


<p class="text-muted">

You can only change your education level,
semester, phone number, email and profile picture.

</p>


<hr>


<form
method="POST"
enctype="multipart/form-data"
onsubmit="return confirmProfileUpdate();"
>


<div class="row">


<!-- =====================================================
     PROFILE PICTURE
===================================================== -->

<div class="col-12 mb-4">

<label class="form-label fw-bold">

Profile Picture

</label>


<div class="upload-box">


<?php

if(
    !empty($user['profile_picture']) &&
    file_exists(
        "../uploads/profile/" .
        $user['profile_picture']
    )
)
{

?>

<img
src="../uploads/profile/<?php
echo htmlspecialchars(
    $user['profile_picture']
);
?>"
id="previewImage"
class="profile-picture"
alt="Current Profile Picture"
>

<?php

}
else
{

?>

<div
id="previewPlaceholder"
class="profile-picture-placeholder"
>

<i class="fa fa-user"></i>

</div>

<img
id="previewImage"
class="profile-picture"
style="display:none;"
alt="Profile Preview"
>

<?php

}

?>


<br><br>


<i class="fa fa-camera"></i>


<h6>

Change Profile Picture

</h6>


<p class="text-muted small">

JPG, JPEG, PNG or WEBP only. Maximum 5MB.

</p>


<input
type="file"
name="profile_picture"
id="profile_picture"
class="form-control"
accept=".jpg,.jpeg,.png,.webp"
onchange="previewProfilePicture(this)"
>


</div>

</div>


<!-- =====================================================
     FULL NAME - READ ONLY
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Full Name

</label>

<input
type="text"
class="form-control readonly-field"
value="<?php
echo htmlspecialchars(
    $user['full_name']
);
?>"
readonly
>

</div>


<!-- =====================================================
     IC - READ ONLY
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

IC Number

</label>

<input
type="text"
class="form-control readonly-field"
value="<?php
echo htmlspecialchars(
    $user['ic_number']
);
?>"
readonly
>

</div>


<!-- =====================================================
     REGISTRATION - READ ONLY
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Registration Number

</label>

<input
type="text"
class="form-control readonly-field"
value="<?php
echo htmlspecialchars(
    $user['registration_no']
);
?>"
readonly
>

</div>


<!-- =====================================================
     DEPARTMENT - READ ONLY
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Department

</label>

<input
type="text"
class="form-control readonly-field"
value="<?php
echo htmlspecialchars(
    $user['department']
);
?>"
readonly
>

</div>


<!-- =====================================================
     EDUCATION LEVEL - EDITABLE
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Education Level

</label>


<select
name="education_level"
class="form-select"
required
>


<option
value="Diploma"
<?php

echo (
    $user['education_level']
    ==
    "Diploma"
)
?
"selected"
:
"";

?>
>

Diploma

</option>


<option
value="Degree"
<?php

echo (
    $user['education_level']
    ==
    "Degree"
)
?
"selected"
:
"";

?>
>

Degree

</option>


</select>

</div>


<!-- =====================================================
     SEMESTER - EDITABLE
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Semester

</label>


<select
name="semester"
class="form-select"
required
>

<?php

$current_semester =
    $user['semester'];

?>


<?php

for(
    $i=1;
    $i<=8;
    $i++
)
{

?>

<option
value="<?php echo $i; ?>"
<?php

echo (
    $current_semester == $i
)
?
"selected"
:
"";

?>
>

Semester <?php echo $i; ?>

</option>

<?php

}

?>

</select>

</div>


<!-- =====================================================
     PHONE - EDITABLE
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Phone Number

</label>

<input
type="text"
name="phone"
class="form-control"
value="<?php
echo htmlspecialchars(
    $user['phone']
);
?>"
required
>

</div>


<!-- =====================================================
     EMAIL - EDITABLE
===================================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php
echo htmlspecialchars(
    $user['email']
);
?>"
required
>

</div>


</div>


<hr class="mt-4">


<!-- =====================================================
     BUTTONS
===================================================== -->

<div class="row mt-4">


<div class="col-md-6 mb-2">

<a
href="profile.php"
class="btn btn-cancel w-100">

<i class="fa fa-arrow-left"></i>

&nbsp; Back to Profile

</a>

</div>


<div class="col-md-6 mb-2">

<button
type="submit"
name="update"
class="btn btn-save w-100">

<i class="fa fa-floppy-disk"></i>

&nbsp; Save Changes

</button>

</div>


</div>


</form>


<?php } ?>


</div>


</div>


<!-- =====================================================
     PROFILE IMAGE MODAL
===================================================== -->

<div
class="modal fade"
id="profilePictureModal"
tabindex="-1"
>

<div class="modal-dialog modal-dialog-centered">

<div
class="modal-content"
style="background:transparent;border:none;"
>

<div class="modal-body text-center p-0">


<img
id="largeProfilePicture"
src=""
style="
max-width:90vw;
max-height:80vh;
border-radius:15px;
object-fit:contain;
box-shadow:0 10px 40px rgba(0,0,0,.5);
"
>


</div>

</div>

</div>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<script>


/* =====================================================
   PROFILE IMAGE PREVIEW
===================================================== */

function previewProfilePicture(input)
{

    if(
        input.files &&
        input.files[0]
    )
    {

        var file =
            input.files[0];


        var allowedTypes = [

            "image/jpeg",

            "image/png",

            "image/webp"

        ];


        if(
            !allowedTypes.includes(
                file.type
            )
        )
        {

            alert(
                "Only JPG, JPEG, PNG and WEBP images are allowed."
            );

            input.value = "";

            return;

        }


        if(
            file.size >
            5 * 1024 * 1024
        )
        {

            alert(
                "Profile picture must be smaller than 5MB."
            );

            input.value = "";

            return;

        }


        var reader =
            new FileReader();


        reader.onload =
        function(e)
        {

            var image =
                document.getElementById(
                    "previewImage"
                );


            image.src =
                e.target.result;


            image.style.display =
                "inline-block";


            var placeholder =
                document.getElementById(
                    "previewPlaceholder"
                );


            if(placeholder)
            {

                placeholder.style.display =
                    "none";

            }

        };


        reader.readAsDataURL(
            file
        );

    }

}


/* =====================================================
   CONFIRM BEFORE UPDATE
===================================================== */

function confirmProfileUpdate()
{

    return confirm(
        "Are you sure you want to change your profile information?\n\n" +
        "The changes you made will be saved to your account."
    );

}


/* =====================================================
   VIEW LARGE PROFILE PICTURE
===================================================== */

function openProfilePicture(imageSrc)
{

    document.getElementById(
        "largeProfilePicture"
    ).src = imageSrc;


    var modal =
        new bootstrap.Modal(
            document.getElementById(
                "profilePictureModal"
            )
        );


    modal.show();

}


/* =====================================================
   QR CODE
===================================================== */

<?php if(!empty($qr_value) && !isset($_GET['edit'])) { ?>

var qrValue =
<?php echo json_encode($qr_value); ?>;


new QRCode(
    document.getElementById("qrcode"),
    {

        text:qrValue,

        width:200,

        height:200,

        correctLevel:
            QRCode.CorrectLevel.H

    }
);

<?php } ?>


</script>


</body>

</html>