<?php

session_start();

/*
|--------------------------------------------------------------------------
| ADMIN LOGIN SECURITY
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");
    exit();

}

include("../includes/db.php");


/*
|--------------------------------------------------------------------------
| DETERMINE SUPER ADMIN
|--------------------------------------------------------------------------
| The first admin account (lowest admin_id) is the Super Admin.
|--------------------------------------------------------------------------
*/

$min_admin_query = mysqli_query(
    $conn,
    "SELECT MIN(admin_id) AS min_id FROM admins"
);

if (!$min_admin_query) {

    die(
        "Database Error: " .
        htmlspecialchars(mysqli_error($conn))
    );

}

$min_admin_row = mysqli_fetch_assoc($min_admin_query);

$super_admin_id = $min_admin_row['min_id'] ?? 0;

$is_super_admin = (
    $_SESSION['admin_id'] == $super_admin_id
);


/*
|--------------------------------------------------------------------------
| ONLY SUPER ADMIN CAN EDIT USERS
|--------------------------------------------------------------------------
*/

if (!$is_super_admin) {

    header("Location: manage_users.php?error=no_permission");
    exit();

}


/*
|--------------------------------------------------------------------------
| GET USER ID
|--------------------------------------------------------------------------
*/

$user_id = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;

if ($user_id <= 0) {

    header("Location: manage_users.php");
    exit();

}


/*
|--------------------------------------------------------------------------
| GET CURRENT USER
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "
    SELECT
        user_id,
        full_name,
        ic_number,
        registration_no,
        education_level,
        semester,
        phone,
        email,
        profile_picture,
        department
    FROM users
    WHERE user_id = ?
    LIMIT 1
    "
);

if (!$stmt) {

    die(
        "Database Error: " .
        htmlspecialchars(mysqli_error($conn))
    );

}

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);

    header(
        "Location: manage_users.php?error=user_not_found"
    );

    exit();

}

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| MESSAGE
|--------------------------------------------------------------------------
*/

$message = "";
$error = "";


/*
|--------------------------------------------------------------------------
| UPDATE USER INFORMATION
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| profile_picture is NOT included.
|
| Therefore the user's existing photo is never changed.
|
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | GET FORM VALUES
    |--------------------------------------------------------------------------
    */

    $full_name = trim(
        $_POST['full_name'] ?? ''
    );

    $ic_number = trim(
        $_POST['ic_number'] ?? ''
    );

    $registration_no = trim(
        $_POST['registration_no'] ?? ''
    );

    $education_level = trim(
        $_POST['education_level'] ?? ''
    );

    $semester = trim(
        $_POST['semester'] ?? ''
    );

    $phone = trim(
        $_POST['phone'] ?? ''
    );

    $email = trim(
        $_POST['email'] ?? ''
    );

    $department = trim(
        $_POST['department'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($full_name === "") {

        $error = "Full name is required.";

    }

    elseif ($ic_number === "") {

        $error = "IC number is required.";

    }

    elseif ($registration_no === "") {

        $error = "Registration number is required.";

    }

    elseif (
        $email !== "" &&
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid email address.";

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    |
    | profile_picture is deliberately NOT updated.
    |
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        $update_stmt = mysqli_prepare(
            $conn,
            "
            UPDATE users
            SET
                full_name = ?,
                ic_number = ?,
                registration_no = ?,
                education_level = ?,
                semester = ?,
                phone = ?,
                email = ?,
                department = ?
            WHERE user_id = ?
            "
        );

        if (!$update_stmt) {

            $error =
                "Database Error: " .
                mysqli_error($conn);

        }

        else {

            mysqli_stmt_bind_param(
                $update_stmt,
                "ssssssssi",
                $full_name,
                $ic_number,
                $registration_no,
                $education_level,
                $semester,
                $phone,
                $email,
                $department,
                $user_id
            );


            if (
                mysqli_stmt_execute(
                    $update_stmt
                )
            ) {

                $message =
                    "User information updated successfully.";

                /*
                |--------------------------------------------------------------------------
                | UPDATE DISPLAYED DATA
                |--------------------------------------------------------------------------
                */

                $user['full_name'] =
                    $full_name;

                $user['ic_number'] =
                    $ic_number;

                $user['registration_no'] =
                    $registration_no;

                $user['education_level'] =
                    $education_level;

                $user['semester'] =
                    $semester;

                $user['phone'] =
                    $phone;

                $user['email'] =
                    $email;

                $user['department'] =
                    $department;

                /*
                |--------------------------------------------------------------------------
                | profile_picture remains exactly the same
                |--------------------------------------------------------------------------
                */

            }

            else {

                $error =
                    "Unable to update user information: " .
                    mysqli_error($conn);

            }

            mysqli_stmt_close(
                $update_stmt
            );

        }

    }

}


/*
|--------------------------------------------------------------------------
| PROFILE PHOTO URL
|--------------------------------------------------------------------------
|
| Your actual folder:
|
| uploads/profile/
|
| This file:
|
| admin/edit_user.php
|
| Browser path:
|
| ../uploads/profile/filename.jpg
|
|--------------------------------------------------------------------------
*/

$photo = trim(
    $user['profile_picture'] ?? ''
);

$photo_url = "";

if ($photo !== "") {

    /*
    |--------------------------------------------------------------------------
    | IF DATABASE STORES FULL HTTP/HTTPS URL
    |--------------------------------------------------------------------------
    */

    if (
        strpos($photo, 'http://') === 0 ||
        strpos($photo, 'https://') === 0
    ) {

        $photo_url = $photo;

    }

    else {

        /*
        |--------------------------------------------------------------------------
        | GET ONLY FILE NAME
        |--------------------------------------------------------------------------
        |
        | Supports:
        |
        | photo.jpg
        | profile/photo.jpg
        | uploads/profile/photo.jpg
        |
        |--------------------------------------------------------------------------
        */

        $photo_filename = basename(
            str_replace(
                "\\",
                "/",
                $photo
            )
        );


        if ($photo_filename !== "") {

            $photo_url =
                "../uploads/profile/" .
                $photo_filename;

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>Edit User</title>


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


<!-- GOOGLE FONT -->

<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
    rel="stylesheet"
>


<style>

/* =========================================================
   GLOBAL
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

    margin-left: 260px;

    padding: 30px;

}


/* =========================================================
   TOPBAR
========================================================= */

.topbar {

    background: white;

    padding: 18px 30px;

    border-radius: 15px;

    box-shadow: 0 10px 30px rgba(0,0,0,.08);

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;

}


.topbar h3 {

    margin: 0;

    color: #800020;

    font-weight: bold;

}


.profile {

    font-weight: 600;

}


/* =========================================================
   CARD
========================================================= */

.user-card {

    background: white;

    padding: 30px;

    border-radius: 20px;

    box-shadow: 0 10px 25px rgba(0,0,0,.08);

}


.page-title {

    font-size: 22px;

    font-weight: bold;

    color: #800020;

    margin-bottom: 25px;

}


/* =========================================================
   PROFILE PHOTO
========================================================= */

.profile-section {

    text-align: center;

    margin-bottom: 35px;

}


.user-photo {

    width: 140px;

    height: 140px;

    border-radius: 50%;

    object-fit: cover;

    border: 5px solid #D4AF37;

    display: block;

    margin: 0 auto;

}


.no-photo {

    width: 140px;

    height: 140px;

    border-radius: 50%;

    background: #800020;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 5px solid #D4AF37;

    margin: auto;

    font-size: 45px;

}


.user-name {

    margin-top: 15px;

    color: #800020;

    font-size: 20px;

    font-weight: 700;

}


.photo-locked {

    display: inline-block;

    margin-top: 10px;

    padding: 7px 14px;

    background: #f8e9ed;

    color: #800020;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;

}


.photo-note {

    margin-top: 10px;

    font-size: 13px;

    color: #777;

}


/* =========================================================
   FORM
========================================================= */

.form-label {

    font-weight: 600;

    color: #555;

}


.form-control,

.form-select {

    min-height: 46px;

    border-radius: 10px;

}


.form-control:focus,

.form-select:focus {

    border-color: #800020;

    box-shadow:
        0 0 0 .2rem
        rgba(128,0,32,.12);

}


/* =========================================================
   BUTTONS
========================================================= */

.btn-save {

    background: #800020;

    color: white;

    border: none;

    border-radius: 10px;

    padding: 11px 25px;

}


.btn-save:hover {

    background: #a00028;

    color: white;

}


.btn-cancel {

    background: #D4AF37;

    color: #222;

    border: none;

    border-radius: 10px;

    padding: 11px 25px;

}


.btn-cancel:hover {

    background: #b9972e;

    color: #222;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991px) {

    .main {

        margin-left: 0;

        padding: 24px 18px;

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


@media (max-width: 576px) {

    .main {

        padding: 20px 14px;

        padding-top: 64px;

    }


    .user-card {

        padding: 18px;

    }

}

</style>

</head>


<body>


<!-- ADMIN MENU -->

<?php include("admin_menu.php"); ?>


<!-- MAIN -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <h3>

            Edit User

        </h3>


        <div class="profile">

            Welcome,

            <b>

                <?php

                echo htmlspecialchars(
                    $_SESSION['admin_name'] ?? 'Admin'
                );

                ?>

            </b>

        </div>

    </div>


    <!-- USER CARD -->

    <div class="user-card">


        <div class="page-title">

            <i class="fa fa-user-pen"></i>

            Edit User Information

        </div>


        <!-- SUCCESS MESSAGE -->

        <?php if ($message !== "") { ?>

            <div class="alert alert-success">

                <i class="fa fa-check-circle"></i>

                <?php

                echo htmlspecialchars(
                    $message
                );

                ?>

            </div>

        <?php } ?>


        <!-- ERROR MESSAGE -->

        <?php if ($error !== "") { ?>

            <div class="alert alert-danger">

                <i class="fa fa-exclamation-circle"></i>

                <?php

                echo htmlspecialchars(
                    $error
                );

                ?>

            </div>

        <?php } ?>


        <!-- =================================================
             PROFILE PHOTO
             VIEW ONLY
        ================================================== -->

        <div class="profile-section">


            <?php if ($photo_url !== "") { ?>

                <img
                    src="<?php echo htmlspecialchars($photo_url); ?>"
                    class="user-photo"
                    alt="Profile Picture"
                    onerror="
                        this.style.display='none';
                        document.getElementById('noPhoto').style.display='flex';
                    "
                >


                <div
                    id="noPhoto"
                    class="no-photo"
                    style="display:none;"
                >

                    <i class="fa fa-user"></i>

                </div>


            <?php } else { ?>


                <div
                    id="noPhoto"
                    class="no-photo"
                >

                    <i class="fa fa-user"></i>

                </div>


            <?php } ?>


            <div class="user-name">

                <?php

                echo htmlspecialchars(
                    $user['full_name']
                );

                ?>

            </div>


            <div class="photo-locked">

                <i class="fa fa-lock"></i>

                Profile Photo Cannot Be Changed

            </div>


            <div class="photo-note">

                Administrators can view this profile photo,
                but cannot change it.

            </div>


        </div>


        <!-- =================================================
             EDIT USER INFORMATION
        ================================================== -->

        <form
            method="POST"
            action="edit_user.php?id=<?php echo urlencode($user_id); ?>"
            id="editUserForm"
        >


            <div class="row g-4">


                <!-- FULL NAME -->

                <div class="col-md-6">

                    <label class="form-label">

                        Full Name

                    </label>

                    <input
                        type="text"
                        name="full_name"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $user['full_name']
                            );
                        ?>"
                        required
                    >

                </div>


                <!-- IC NUMBER -->

                <div class="col-md-6">

                    <label class="form-label">

                        IC Number

                    </label>

                    <input
                        type="text"
                        name="ic_number"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $user['ic_number']
                            );
                        ?>"
                        required
                    >

                </div>


                <!-- REGISTRATION NUMBER -->

                <div class="col-md-6">

                    <label class="form-label">

                        Registration Number

                    </label>

                    <input
                        type="text"
                        name="registration_no"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $user['registration_no']
                            );
                        ?>"
                        required
                    >

                </div>


                <!-- EDUCATION LEVEL -->

                <div class="col-md-6">

                    <label class="form-label">

                        Education Level

                    </label>

                    <input
                        type="text"
                        name="education_level"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $user['education_level']
                            );
                        ?>"
                    >

                </div>


                <!-- SEMESTER -->

                <div class="col-md-6">

                    <label class="form-label">

                        Semester

                    </label>

                    <input
                        type="text"
                        name="semester"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $user['semester']
                            );
                        ?>"
                    >

                </div>


                <!-- PHONE -->

                <div class="col-md-6">

                    <label class="form-label">

                        Phone

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
                    >

                </div>


                <!-- EMAIL -->

                <div class="col-md-6">

                    <label class="form-label">

                        Email

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
                    >

                </div>


                <!-- DEPARTMENT -->

                <div class="col-md-6">

                    <label class="form-label">

                        Department

                    </label>

                    <input
                        type="text"
                        name="department"
                        class="form-control"
                        value="<?php
                            echo htmlspecialchars(
                                $user['department']
                            );
                        ?>"
                    >

                </div>


            </div>


            <!-- BUTTONS -->

            <div class="d-flex gap-2 mt-4">


                <button
                    type="submit"
                    class="btn btn-save"
                    onclick="
                        return confirm(
                            'Are you sure you want to update this user information?\\n\\nThe profile photo will NOT be changed.'
                        );
                    "
                >

                    <i class="fa fa-save"></i>

                    Save Changes

                </button>


                <a
                    href="view_user.php?id=<?php echo urlencode($user_id); ?>"
                    class="btn btn-cancel"
                >

                    <i class="fa fa-arrow-left"></i>

                    Cancel

                </a>


            </div>


        </form>


    </div>


</div>


<!-- BOOTSTRAP JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>