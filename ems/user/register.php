<?php

session_start();

include("../includes/db.php");

$message = "";

if (isset($_POST['register'])) {

    // Get form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $ic_number = mysqli_real_escape_string($conn, $_POST['ic_number']);
    $registration_no = mysqli_real_escape_string($conn, $_POST['registration_no']);
    $education_level = mysqli_real_escape_string($conn, $_POST['education_level']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Check IC Number
    $checkIC = mysqli_query(
        $conn,
        "SELECT * FROM users 
         WHERE ic_number='$ic_number'"
    );

    if (mysqli_num_rows($checkIC) > 0) {

        $message = "
            <div class='alert alert-danger'>
                IC Number already exists.
            </div>
        ";

    } else {

        // Check Registration Number
        $checkReg = mysqli_query(
            $conn,
            "SELECT * FROM users 
             WHERE registration_no='$registration_no'"
        );

        if (mysqli_num_rows($checkReg) > 0) {

            $message = "
                <div class='alert alert-danger'>
                    Registration Number already exists.
                </div>
            ";

        } else {

            // Check Email
            $checkEmail = mysqli_query(
                $conn,
                "SELECT * FROM users 
                 WHERE email='$email'"
            );

            if (mysqli_num_rows($checkEmail) > 0) {

                $message = "
                    <div class='alert alert-danger'>
                        Email already exists.
                    </div>
                ";

            } else {

                // Insert new user
                $sql = "
                    INSERT INTO users (
                        full_name,
                        ic_number,
                        registration_no,
                        education_level,
                        semester,
                        phone,
                        email,
                        department
                    )
                    VALUES (
                        '$full_name',
                        '$ic_number',
                        '$registration_no',
                        '$education_level',
                        '$semester',
                        '$phone',
                        '$email',
                        '$department'
                    )
                ";

                if (mysqli_query($conn, $sql)) {

                    $message = "
                        <div class='alert alert-success'>
                            Registration Successful.
                        </div>
                    ";

                } else {

                    $message = "
                        <div class='alert alert-danger'>
                            Failed to Register: " .
                            mysqli_error($conn) .
                        "
                        </div>
                    ";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>User Registration</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
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
            background: linear-gradient(
                135deg,
                #5c0017,
                #800020,
                #a00028
            );

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 30px 15px;
        }

        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;

            box-shadow:
                0 20px 45px rgba(0, 0, 0, .35);
        }

        .card-header {
            background: #800020;
            color: white;

            text-align: center;

            padding: 30px;
        }

        .card-header h3 {
            margin-bottom: 5px;
        }

        .card-header p {
            margin-bottom: 0;
        }

        .logo {
            width: 90px;
            height: 90px;

            background: white;

            padding: 8px;

            border-radius: 50%;

            margin-bottom: 15px;
        }

        .card-body {
            padding: 35px;
        }

        .form-control {
            height: 48px;
            border-radius: 10px;
        }

        .form-select {
            height: 48px;
            border-radius: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #800020;

            box-shadow:
                0 0 10px rgba(128, 0, 32, .25);
        }

        .btn-register {
            background: #D4AF37;
            color: #800020;

            font-weight: bold;

            padding: 12px;

            border: none;
            border-radius: 30px;

            transition: .3s;
        }

        .btn-register:hover {
            background: #FFD700;

            transform: scale(1.03);
        }

        .back {
            text-decoration: none;

            color: #800020;

            font-weight: bold;
        }

        .back:hover {
            color: #D4AF37;
        }

        @media (max-width: 576px) {

            .card-header {
                padding: 20px;
            }

            .card-header h3 {
                font-size: 19px;
            }

            .card-body {
                padding: 20px;
            }

            .logo {
                width: 70px;
                height: 70px;
            }
        }

    </style>

</head>

<body>

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-7">

                <div class="card">

                    <!-- Header -->
                    <div class="card-header">

                        <img
                            src="../assets/images/logo.png"
                            class="logo"
                            alt="Logo"
                        >

                        <h3>
                            JPP Event Management System
                        </h3>

                        <p>
                            Student Registration
                        </p>

                    </div>


                    <!-- Body -->
                    <div class="card-body">

                        <!-- Message -->
                        <?php echo $message; ?>


                        <!-- Registration Form -->
                        <form method="POST">

                            <!-- Full Name & IC Number -->
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Full Name
                                    </label>

                                    <input
                                        type="text"
                                        name="full_name"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        IC Number
                                    </label>

                                    <input
                                        type="text"
                                        name="ic_number"
                                        class="form-control"
                                        placeholder="010101101234"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- Registration Number & Education Level -->
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Registration Number
                                    </label>

                                    <input
                                        type="text"
                                        name="registration_no"
                                        class="form-control"
                                        placeholder="01DIT24F0000"
                                        required
                                    >

                                </div>


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
                                            value=""
                                            selected
                                            disabled
                                        >
                                            -- Select Education Level --
                                        </option>

                                        <option value="Diploma">
                                            Diploma
                                        </option>

                                        <option value="Degree">
                                            Degree
                                        </option>

                                    </select>

                                </div>

                            </div>


                            <!-- Department & Semester -->
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Department
                                    </label>

                                    <select
                                        name="department"
                                        class="form-select"
                                        required
                                    >

                                        <option
                                            value=""
                                            selected
                                            disabled
                                        >
                                            -- Select Department --
                                        </option>

                                        <option value="JKA">
                                            JKA
                                        </option>

                                        <option value="JKP">
                                            JKP
                                        </option>

                                        <option value="JKE">
                                            JKE
                                        </option>

                                        <option value="JKM">
                                            JKM
                                        </option>

                                        <option value="JP">
                                            JP
                                        </option>

                                        <option value="JTMK">
                                            JTMK
                                        </option>

                                        <option value="JMSK">
                                            JMSK
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Semester
                                    </label>

                                    <select
                                        name="semester"
                                        class="form-select"
                                        required
                                    >

                                        <option
                                            value=""
                                            selected
                                            disabled
                                        >
                                            -- Select Semester --
                                        </option>

                                        <option value="1">
                                            Semester 1
                                        </option>

                                        <option value="2">
                                            Semester 2
                                        </option>

                                        <option value="3">
                                            Semester 3
                                        </option>

                                        <option value="4">
                                            Semester 4
                                        </option>

                                        <option value="5">
                                            Semester 5
                                        </option>

                                    </select>

                                </div>

                            </div>


                            <!-- Phone & Email -->
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Phone Number
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        placeholder="01XXXXXXXX"
                                        required
                                    >

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Email Address
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="student@email.com"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- Register Button -->
                            <div class="d-grid mt-3">

                                <button
                                    type="submit"
                                    name="register"
                                    class="btn btn-register"
                                >

                                    <i class="fa fa-user-plus"></i>

                                    REGISTER ACCOUNT

                                </button>

                            </div>


                            <!-- Back To Login -->
                            <div class="text-center mt-4">

                                Already have an account?

                                <br><br>

                                <a
                                    href="../login.php"
                                    class="back"
                                >

                                    <i class="fa fa-arrow-left"></i>

                                    Back To Login

                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>