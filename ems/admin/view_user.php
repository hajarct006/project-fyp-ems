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
| GET USER INFORMATION
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


if (!$result || mysqli_num_rows($result) === 0) {

    mysqli_stmt_close($stmt);

    header("Location: manage_users.php?error=user_not_found");
    exit();

}


$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| DETERMINE SUPER ADMIN
|--------------------------------------------------------------------------
*/

$min_admin_query = mysqli_query(
    $conn,
    "SELECT MIN(admin_id) AS min_id FROM admins"
);

$min_admin_row = mysqli_fetch_assoc($min_admin_query);

$super_admin_id = $min_admin_row['min_id'];

$is_super_admin = (
    $_SESSION['admin_id'] == $super_admin_id
);


/*
|--------------------------------------------------------------------------
| PROFILE PHOTO
|--------------------------------------------------------------------------
|
| Database:
| users.profile_picture
|
| Folder:
| uploads/profile/
|--------------------------------------------------------------------------
*/

$photo = trim(
    $user['profile_picture'] ?? ''
);

$photo_url = "";


if ($photo !== "") {

    /*
    |--------------------------------------------------------------------------
    | EXTERNAL PHOTO
    |--------------------------------------------------------------------------
    */

    if (
        strpos($photo, 'http://') === 0 ||
        strpos($photo, 'https://') === 0
    ) {

        $photo_url = $photo;

    }

    /*
    |--------------------------------------------------------------------------
    | LOCAL PHOTO
    |--------------------------------------------------------------------------
    */

    else {

        $photo_name = basename($photo);

        $photo_url =
            "../uploads/profile/" .
            $photo_name;

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

<title>View User</title>


<!-- =====================================================
     BOOTSTRAP
===================================================== -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>


<!-- =====================================================
     FONT AWESOME
===================================================== -->

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    rel="stylesheet"
>


<!-- =====================================================
     GOOGLE FONT
===================================================== -->

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
   USER CARD
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
   PROFILE
========================================================= */

.profile-section {

    text-align: center;

    padding: 20px;

    margin-bottom: 25px;

}


/* =========================================================
   PROFILE PHOTO
========================================================= */

.user-photo {

    width: 140px;

    height: 140px;

    border-radius: 50%;

    object-fit: cover;

    border: 5px solid #D4AF37;

    cursor: pointer;

    transition: 0.3s;

}


.user-photo:hover {

    transform: scale(1.05);

    box-shadow: 0 8px 25px rgba(0,0,0,.2);

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

    font-size: 50px;

}


/* =========================================================
   PHOTO HINT
========================================================= */

.photo-hint {

    margin-top: 10px;

    font-size: 13px;

    color: #777;

}


.user-name {

    margin-top: 15px;

    color: #800020;

    font-size: 24px;

    font-weight: 700;

}


/* =========================================================
   INFORMATION
========================================================= */

.info-card {

    background: #fafafa;

    border: 1px solid #eeeeee;

    border-radius: 15px;

    padding: 20px;

    margin-bottom: 20px;

}


.info-label {

    font-size: 13px;

    color: #777;

    margin-bottom: 4px;

}


.info-value {

    font-size: 16px;

    font-weight: 600;

    color: #333;

    word-break: break-word;

}


/* =========================================================
   BUTTONS
========================================================= */

.btn-back {

    background: #D4AF37;

    color: #222;

    border: none;

    border-radius: 10px;

    padding: 10px 20px;

}


.btn-back:hover {

    background: #b9972e;

    color: #222;

}


.btn-edit {

    background: #800020;

    color: white;

    border: none;

    border-radius: 10px;

    padding: 10px 20px;

}


.btn-edit:hover {

    background: #a00028;

    color: white;

}


/* =========================================================
   LARGE PHOTO MODAL
========================================================= */

.photo-modal-content {

    background: transparent;

    border: none;

}


.photo-modal-body {

    text-align: center;

    padding: 10px;

}


.large-profile-photo {

    max-width: 90vw;

    max-height: 80vh;

    width: auto;

    height: auto;

    object-fit: contain;

    border-radius: 15px;

    border: 5px solid #D4AF37;

    box-shadow: 0 10px 40px rgba(0,0,0,.5);

}


.photo-modal-close {

    position: absolute;

    top: 10px;

    right: 15px;

    z-index: 10;

    width: 40px;

    height: 40px;

    border-radius: 50%;

    background: white;

    border: none;

    color: #800020;

    font-size: 20px;

    display: flex;

    align-items: center;

    justify-content: center;

    box-shadow: 0 4px 15px rgba(0,0,0,.3);

}


.photo-modal-close:hover {

    background: #D4AF37;

    color: #222;

}


/* =========================================================
   MOBILE
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


    .user-photo {

        width: 120px;

        height: 120px;

    }


    .no-photo {

        width: 120px;

        height: 120px;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     ADMIN MENU
===================================================== -->

<?php include("admin_menu.php"); ?>


<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main">


    <!-- =================================================
         TOPBAR
    ================================================== -->

    <div class="topbar">

        <h3>

            View User

        </h3>


        <div class="profile">

            Welcome,

            <b>

                <?php

                echo htmlspecialchars(
                    $_SESSION['admin_name']
                );

                ?>

            </b>

        </div>

    </div>


    <!-- =================================================
         USER CARD
    ================================================== -->

    <div class="user-card">


        <div class="page-title">

            <i class="fa fa-user"></i>

            User Details

        </div>


        <!-- =================================================
             PROFILE SECTION
        ================================================== -->

        <div class="profile-section">


            <?php if ($photo_url !== "") { ?>


                <!-- PROFILE PHOTO -->

                <img
                    src="<?php echo htmlspecialchars($photo_url); ?>"
                    class="user-photo"
                    alt="Profile Picture"
                    data-bs-toggle="modal"
                    data-bs-target="#photoModal"
                    onerror="
                        this.style.display='none';
                        document.getElementById('noPhoto').style.display='flex';
                        document.getElementById('photoHint').style.display='none';
                    "
                >


                <!-- FALLBACK -->

                <div
                    id="noPhoto"
                    class="no-photo"
                    style="display:none;"
                >

                    <i class="fa fa-user"></i>

                </div>


                <!-- PHOTO HINT -->

                <div
                    id="photoHint"
                    class="photo-hint"
                >

                    <i class="fa fa-magnifying-glass-plus"></i>

                    Click the photo to view larger

                </div>


            <?php } else { ?>


                <!-- NO PHOTO -->

                <div class="no-photo">

                    <i class="fa fa-user"></i>

                </div>


            <?php } ?>


            <!-- USER NAME -->

            <div class="user-name">

                <?php

                echo htmlspecialchars(
                    $user['full_name']
                );

                ?>

            </div>


        </div>


        <!-- =================================================
             INFORMATION
        ================================================== -->

        <div class="row g-3">


            <!-- IC NUMBER -->

            <div class="col-md-6">

                <div class="info-card">

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


            <!-- REGISTRATION NUMBER -->

            <div class="col-md-6">

                <div class="info-card">

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


            <!-- EDUCATION LEVEL -->

            <div class="col-md-6">

                <div class="info-card">

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


            <!-- SEMESTER -->

            <div class="col-md-6">

                <div class="info-card">

                    <div class="info-label">

                        Semester

                    </div>

                    <div class="info-value">

                        <?php

                        echo htmlspecialchars(
                            $user['semester']
                        );

                        ?>

                    </div>

                </div>

            </div>


            <!-- PHONE -->

            <div class="col-md-6">

                <div class="info-card">

                    <div class="info-label">

                        Phone

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

            <div class="col-md-6">

                <div class="info-card">

                    <div class="info-label">

                        Email

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


            <!-- DEPARTMENT -->

            <div class="col-md-12">

                <div class="info-card">

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


        </div>


        <!-- =================================================
             BUTTONS
        ================================================== -->

        <div class="d-flex gap-2 mt-3">


            <!-- BACK -->

            <a
                href="manage_users.php"
                class="btn btn-back"
            >

                <i class="fa fa-arrow-left"></i>

                Back

            </a>


            <!-- EDIT - SUPER ADMIN ONLY -->

            <?php if ($is_super_admin) { ?>


                <a
                    href="edit_user.php?id=<?php echo urlencode($user['user_id']); ?>"
                    class="btn btn-edit"
                >

                    <i class="fa fa-pen"></i>

                    Edit User

                </a>


            <?php } ?>


        </div>


    </div>


</div>


<!-- =====================================================
     LARGE PROFILE PHOTO MODAL
===================================================== -->

<?php if ($photo_url !== "") { ?>

<div
    class="modal fade"
    id="photoModal"
    tabindex="-1"
    aria-hidden="true"
>


    <div
        class="modal-dialog modal-dialog-centered modal-xl"
    >


        <div class="modal-content photo-modal-content">


            <!-- CLOSE BUTTON -->

            <button
                type="button"
                class="photo-modal-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            >

                <i class="fa fa-xmark"></i>

            </button>


            <!-- LARGE PHOTO -->

            <div class="photo-modal-body">

                <img
                    src="<?php echo htmlspecialchars($photo_url); ?>"
                    class="large-profile-photo"
                    alt="Large Profile Picture"
                >

            </div>


        </div>

    </div>

</div>

<?php } ?>


<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>