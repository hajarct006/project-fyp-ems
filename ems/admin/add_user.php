<?php

session_start();

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");
    exit();

}

include("../includes/db.php");


$message = "";
$message_type = "";


$full_name = "";
$ic_number = "";
$registration_no = "";
$education_level = "";
$semester = "";
$phone = "";
$email = "";
$department = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $full_name = trim($_POST['full_name'] ?? "");

    $ic_number = trim($_POST['ic_number'] ?? "");

    $registration_no = trim($_POST['registration_no'] ?? "");

    $education_level = trim($_POST['education_level'] ?? "");

    $semester = trim($_POST['semester'] ?? "");

    $phone = trim($_POST['phone'] ?? "");

    $email = trim($_POST['email'] ?? "");

    $department = trim($_POST['department'] ?? "");

    $password = $_POST['password'] ?? "";

    $confirm_password = $_POST['confirm_password'] ?? "";


    /*
    |--------------------------------------------------------------------------
    | REQUIRED FIELD VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $full_name === "" ||
        $ic_number === "" ||
        $registration_no === "" ||
        $education_level === "" ||
        $semester === "" ||
        $phone === "" ||
        $email === "" ||
        $department === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $message = "Please fill in all required fields.";

        $message_type = "danger";

    }


    /*
    |--------------------------------------------------------------------------
    | EMAIL VALIDATION
    |--------------------------------------------------------------------------
    */

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

        $message_type = "danger";

    }


    /*
    |--------------------------------------------------------------------------
    | PASSWORD MATCH
    |--------------------------------------------------------------------------
    */

    elseif ($password !== $confirm_password) {

        $message = "Password and confirm password do not match.";

        $message_type = "danger";

    }


    else {


        /*
        |--------------------------------------------------------------------------
        | CHECK EMAIL
        |--------------------------------------------------------------------------
        */

        $check_email = mysqli_prepare(
            $conn,
            "SELECT user_id FROM users WHERE email = ? LIMIT 1"
        );


        if ($check_email) {

            mysqli_stmt_bind_param(
                $check_email,
                "s",
                $email
            );

            mysqli_stmt_execute($check_email);

            mysqli_stmt_store_result($check_email);


            if (mysqli_stmt_num_rows($check_email) > 0) {

                $message = "This email is already registered.";

                $message_type = "danger";

                mysqli_stmt_close($check_email);

            }

            else {

                mysqli_stmt_close($check_email);


                /*
                |--------------------------------------------------------------------------
                | CHECK REGISTRATION NUMBER
                |--------------------------------------------------------------------------
                */

                $check_registration = mysqli_prepare(
                    $conn,
                    "SELECT user_id FROM users WHERE registration_no = ? LIMIT 1"
                );


                if ($check_registration) {

                    mysqli_stmt_bind_param(
                        $check_registration,
                        "s",
                        $registration_no
                    );

                    mysqli_stmt_execute($check_registration);

                    mysqli_stmt_store_result(
                        $check_registration
                    );


                    if (
                        mysqli_stmt_num_rows(
                            $check_registration
                        ) > 0
                    ) {

                        $message =
                            "This registration number is already registered.";

                        $message_type = "danger";

                        mysqli_stmt_close(
                            $check_registration
                        );

                    }

                    else {

                        mysqli_stmt_close(
                            $check_registration
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | HASH PASSWORD
                        |--------------------------------------------------------------------------
                        */

                        $hashed_password =
                            password_hash(
                                $password,
                                PASSWORD_DEFAULT
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | INSERT USER
                        |--------------------------------------------------------------------------
                        */

                        $insert = mysqli_prepare(
                            $conn,
                            "
                            INSERT INTO users
                            (
                                full_name,
                                ic_number,
                                registration_no,
                                education_level,
                                semester,
                                phone,
                                email,
                                password,
                                profile_picture,
                                department
                            )
                            VALUES
                            (?, ?, ?, ?, ?, ?, ?, ?, '', ?)
                            "
                        );


                        if ($insert) {


                            mysqli_stmt_bind_param(
                                $insert,
                                "sssssssss",
                                $full_name,
                                $ic_number,
                                $registration_no,
                                $education_level,
                                $semester,
                                $phone,
                                $email,
                                $hashed_password,
                                $department
                            );


                            if (
                                mysqli_stmt_execute(
                                    $insert
                                )
                            ) {

                                mysqli_stmt_close(
                                    $insert
                                );


                                header(
                                    "Location: manage_users.php?added=1"
                                );

                                exit();

                            }

                            else {

                                $message =
                                    "Unable to add user. Database Error: " .
                                    mysqli_error($conn);

                                $message_type = "danger";

                            }


                            mysqli_stmt_close($insert);

                        }

                        else {

                            $message =
                                "Unable to prepare database query: " .
                                mysqli_error($conn);

                            $message_type = "danger";

                        }

                    }

                }

                else {

                    $message =
                        "Unable to check registration number.";

                    $message_type = "danger";

                }

            }

        }

        else {

            $message =
                "Unable to check email.";

            $message_type = "danger";

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

<title>Add User</title>


<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>


<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    rel="stylesheet"
>


<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap"
    rel="stylesheet"
>


<style>

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

    font-family:'DM Sans',sans-serif;

}


body {

    background: #f5f5f5;

}


.sidebar {

    position: fixed;

    width: 260px;

    height: 100vh;

    background: #800020;

    color: white;

    z-index: 1100;

}


.logo {

    text-align: center;

    padding: 30px 20px;

    border-bottom: 1px solid rgba(255,255,255,.2);

}


.logo img {

    width: 80px;

    margin-bottom: 10px;

}


.logo h4 {

    font-weight: 700;

}


.sidebar a {

    display: block;

    padding: 16px 25px;

    color: white;

    text-decoration: none;

    transition: .3s;

    font-size: 16px;

}


.sidebar a:hover {

    background: #a00028;

    padding-left: 35px;

}


.sidebar i {

    width: 25px;

}


.main {

    margin-left: 260px;

    padding: 30px;

}


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


.form-card {

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


.section-title {

    color: #800020;

    font-size: 17px;

    font-weight: 600;

    margin-top: 10px;

    margin-bottom: 18px;

    padding-bottom: 10px;

    border-bottom: 1px solid #eeeeee;

}


.form-label {

    font-weight: 500;

}


.form-control,
.form-select {

    border-radius: 10px;

    min-height: 44px;

}


.form-control:focus,
.form-select:focus {

    border-color: #800020;

    box-shadow: 0 0 0 .2rem rgba(128,0,32,.12);

}


.btn-save {

    background: #800020;

    color: white;

    border: none;

    border-radius: 10px;

    padding: 11px 22px;

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

    padding: 11px 22px;

}


.btn-cancel:hover {

    background: #b9972e;

    color: #222;

}


.menu-toggle {

    display: none;

    position: fixed;

    top: 14px;

    left: 14px;

    z-index: 1200;

    background: #800020;

    color: white;

    border: 2px solid rgba(255,255,255,.9);

    width: 36px;

    height: 36px;

    border-radius: 9px;

    font-size: 15px;

    box-shadow: 0 4px 12px rgba(0,0,0,.5);

    align-items: center;

    justify-content: center;

}


.sidebar-overlay {

    display: none;

    position: fixed;

    top: 0;

    left: 0;

    width: 100%;

    height: 100%;

    background: rgba(0,0,0,.55);

    z-index: 1099;

}


.sidebar-close {

    display: none;

    position: absolute;

    top: 14px;

    right: 14px;

    background: transparent;

    border: none;

    color: white;

    font-size: 22px;

}


@media (max-width: 991px) {

    .menu-toggle {

        display: flex;

    }


    .sidebar {

        transform: translateX(-100%);

        transition: .3s ease;

        box-shadow: 0 0 30px rgba(0,0,0,.3);

        overflow-y: auto;

    }


    .sidebar.active {

        transform: translateX(0);

    }


    .sidebar-close {

        display: block;

    }


    .sidebar-overlay.active {

        display: block;

    }


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


    .form-card {

        padding: 20px;

    }

}

</style>

</head>


<body>


<?php include("admin_menu.php"); ?>


<div class="main">


    <div class="topbar">

        <h3>

            Add User

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


    <div class="form-card">


        <div class="page-title">

            <i class="fa fa-user-plus"></i>

            Add New User

        </div>


        <?php if ($message !== "") { ?>

            <div class="alert alert-<?php echo $message_type; ?>">

                <i class="fa fa-circle-exclamation"></i>

                <?php

                echo htmlspecialchars($message);

                ?>

            </div>

        <?php } ?>


        <form
            method="POST"
            action="add_user.php"
        >


            <div class="section-title">

                <i class="fa fa-id-card"></i>

                Personal & Academic Information

            </div>


            <div class="row g-3">


                <div class="col-md-6">

                    <label class="form-label">

                        Full Name

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="full_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($full_name); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        IC Number

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="ic_number"
                        class="form-control"
                        placeholder="e.g. 010101-01-0101"
                        value="<?php echo htmlspecialchars($ic_number); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        Registration No.

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="registration_no"
                        class="form-control"
                        placeholder="e.g. 01DIT24F1217"
                        value="<?php echo htmlspecialchars($registration_no); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        Education Level

                        <span class="text-danger">*</span>

                    </label>


                    <select
                        name="education_level"
                        class="form-select"
                        required
                    >

                        <option value="">

                            Select Education Level

                        </option>


                        <option
                            value="Diploma"
                            <?php
                            if ($education_level === "Diploma") {
                                echo "selected";
                            }
                            ?>
                        >

                            Diploma

                        </option>

                        <option
                            value="Degree"
                            <?php
                            if ($education_level === "Degree") {
                                echo "selected";
                            }
                            ?>
                        >

                            Degree

                        </option>


                    </select>

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        Semester

                        <span class="text-danger">*</span>

                    </label>


                    <select
                        name="semester"
                        class="form-select"
                        required
                    >

                        <option value="">

                            Select Semester

                        </option>


                        <option
                            value="1"
                            <?php
                            if ($semester === "1") {
                                echo "selected";
                            }
                            ?>
                        >

                            Semester 1

                        </option>


                        <option
                            value="2"
                            <?php
                            if ($semester === "2") {
                                echo "selected";
                            }
                            ?>
                        >

                            Semester 2

                        </option>


                        <option
                            value="3"
                            <?php
                            if ($semester === "3") {
                                echo "selected";
                            }
                            ?>
                        >

                            Semester 3

                        </option>


                        <option
                            value="4"
                            <?php
                            if ($semester === "4") {
                                echo "selected";
                            }
                            ?>
                        >

                            Semester 4

                        </option>


                        <option
                            value="5"
                            <?php
                            if ($semester === "5") {
                                echo "selected";
                            }
                            ?>
                        >

                            Semester 5

                        </option>


                        <option
                            value="6"
                            <?php
                            if ($semester === "6") {
                                echo "selected";
                            }
                            ?>
                        >

                            Semester 6

                        </option>


                    </select>

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        Department

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="department"
                        class="form-control"
                        placeholder="e.g. JTMK"
                        value="<?php echo htmlspecialchars($department); ?>"
                        required
                    >

                </div>


            </div>


            <div class="section-title mt-4">

                <i class="fa fa-phone"></i>

                Contact Information

            </div>


            <div class="row g-3">


                <div class="col-md-6">

                    <label class="form-label">

                        Phone

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        placeholder="e.g. 0123456789"
                        value="<?php echo htmlspecialchars($phone); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        Email

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="example@email.com"
                        value="<?php echo htmlspecialchars($email); ?>"
                        required
                    >

                </div>


            </div>


            <div class="section-title mt-4">

                <i class="fa fa-lock"></i>

                Login Information

            </div>


            <div class="row g-3">


                <div class="col-md-6">

                    <label class="form-label">

                        Password

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">

                        Confirm Password

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="password"
                        name="confirm_password"
                        class="form-control"
                        required
                    >

                </div>


            </div>


            <div class="d-flex justify-content-end gap-2 mt-4">


                <a
                    href="manage_users.php"
                    class="btn btn-cancel"
                >

                    <i class="fa fa-arrow-left"></i>

                    Cancel

                </a>


                <button
                    type="submit"
                    class="btn btn-save"
                >

                    <i class="fa fa-user-plus"></i>

                    Add User

                </button>


            </div>


        </form>


    </div>


</div>


</body>

</html>