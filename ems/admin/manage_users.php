<?php

session_start();

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");
    exit();

}

include("../includes/db.php");


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
| DELETE USER
|--------------------------------------------------------------------------
*/

if (isset($_GET['delete'])) {

    if (!$is_super_admin) {

        header("Location: manage_users.php?error=no_permission");
        exit();

    }

    $user_id = intval($_GET['delete']);

    if ($user_id > 0) {

        $delete_query = mysqli_prepare(
            $conn,
            "DELETE FROM users WHERE user_id = ?"
        );

        if ($delete_query) {

            mysqli_stmt_bind_param(
                $delete_query,
                "i",
                $user_id
            );

            mysqli_stmt_execute($delete_query);

            mysqli_stmt_close($delete_query);

        }

    }

    header("Location: manage_users.php?deleted=1");
    exit();

}


/*
|--------------------------------------------------------------------------
| SEARCH / FILTER
|--------------------------------------------------------------------------
*/

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : "";

$department = isset($_GET['department'])
    ? trim($_GET['department'])
    : "";

$education_level = isset($_GET['education_level'])
    ? trim($_GET['education_level'])
    : "";

$semester = isset($_GET['semester'])
    ? trim($_GET['semester'])
    : "";


/*
|--------------------------------------------------------------------------
| USER QUERY
|--------------------------------------------------------------------------
*/

$sql = "
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
    WHERE 1=1
";

$params = [];
$types = "";


if ($search !== "") {

    $sql .= "
        AND (
            full_name LIKE ?
            OR ic_number LIKE ?
            OR registration_no LIKE ?
            OR phone LIKE ?
            OR email LIKE ?
            OR department LIKE ?
            OR education_level LIKE ?
        )
    ";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "sssssss";
}


if ($department !== "") {

    $sql .= " AND department = ? ";

    $params[] = $department;

    $types .= "s";

}


if ($education_level !== "") {

    $sql .= " AND education_level = ? ";

    $params[] = $education_level;

    $types .= "s";

}


if ($semester !== "") {

    $sql .= " AND semester = ? ";

    $params[] = $semester;

    $types .= "s";

}


$sql .= " ORDER BY full_name ASC";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    die(
        "Database Error: " .
        htmlspecialchars(mysqli_error($conn))
    );

}


if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );

}


mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


/*
|--------------------------------------------------------------------------
| DEPARTMENT LIST
|--------------------------------------------------------------------------
*/

$department_result = mysqli_query(
    $conn,
    "
    SELECT DISTINCT department
    FROM users
    WHERE department IS NOT NULL
    AND department != ''
    ORDER BY department ASC
    "
);


/*
|--------------------------------------------------------------------------
| EDUCATION LEVEL LIST
|--------------------------------------------------------------------------
*/

$education_result = mysqli_query(
    $conn,
    "
    SELECT DISTINCT education_level
    FROM users
    WHERE education_level IS NOT NULL
    AND education_level != ''
    ORDER BY education_level ASC
    "
);


/*
|--------------------------------------------------------------------------
| SEMESTER LIST
|--------------------------------------------------------------------------
*/

$semester_result = mysqli_query(
    $conn,
    "
    SELECT DISTINCT semester
    FROM users
    WHERE semester IS NOT NULL
    AND semester != ''
    ORDER BY semester ASC
    "
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>Manage Users</title>


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


.user-card {

    background: white;

    padding: 25px;

    border-radius: 20px;

    box-shadow: 0 10px 25px rgba(0,0,0,.08);

}


.page-title {

    font-size: 22px;

    font-weight: bold;

    color: #800020;

}


.search-card {

    background: #fafafa;

    border: 1px solid #eeeeee;

    border-radius: 15px;

    padding: 20px;

    margin-bottom: 25px;

}


.search-title {

    color: #800020;

    font-size: 17px;

    font-weight: 600;

    margin-bottom: 15px;

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


.btn-search {

    background: #800020;

    color: white;

    border: none;

    border-radius: 10px;

    min-height: 44px;

    padding: 0 20px;

}


.btn-search:hover {

    background: #a00028;

    color: white;

}


.btn-reset {

    background: #D4AF37;

    color: #222;

    border: none;

    border-radius: 10px;

    min-height: 44px;

    padding: 0 20px;

}


.btn-reset:hover {

    background: #b9972e;

    color: #222;

}


.btn-add-user {

    background: linear-gradient(145deg,#e0bb45,#b8890f);

    color: #2b000b;font-weight:800;

    border: none;

    border-radius: 10px;

    padding: 10px 18px;

}


.btn-add-user:hover {

    background: linear-gradient(145deg,#ecc858,#c8991a);

    color: #2b000b;

}


.table {

    margin-bottom: 0;

}


.table thead th {

    background: #800020;

    color: white;

    white-space: nowrap;

    vertical-align: middle;

}


.table td {

    vertical-align: middle;

    white-space: nowrap;

}


.user-photo {

    width: 58px;

    height: 58px;

    border-radius: 50%;

    object-fit: cover;

    border: 3px solid #D4AF37;

    display: block;

}


.no-photo {

    width: 58px;

    height: 58px;

    border-radius: 50%;

    background: #800020;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 3px solid #D4AF37;

    font-size: 22px;

}


.btn-view {

    background: #D4AF37;

    color: #222;

    border: none;

}


.btn-view:hover {

    background: #b9972e;

    color: #222;

}


.btn-edit {

    background: #800020;

    color: white;

    border: none;

}


.btn-edit:hover {

    background: #a00028;

    color: white;

}


.btn-delete {

    background: #dc3545;

    color: white;

    border: none;

}


.btn-delete:hover {

    background: #bb2d3b;

    color: white;

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


    .user-card {

        padding: 18px;

    }


    .search-card {

        padding: 15px;

    }


    .user-header {

        flex-direction: column;

        align-items: stretch !important;

        gap: 12px;

    }


    .btn-add-user {

        width: 100%;

    }

}

</style>

</head>


<body>


<?php include("admin_menu.php"); ?>


<div class="main">


    <div class="topbar">

        <h3>
            Manage Users
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


    <?php if (isset($_GET['deleted'])) { ?>

        <div class="alert alert-success">

            <i class="fa fa-check-circle"></i>

            User deleted successfully.

        </div>

    <?php } ?>


    <?php if (
        isset($_GET['error']) &&
        $_GET['error'] == 'no_permission'
    ) { ?>

        <div class="alert alert-danger">

            <i class="fa fa-lock"></i>

            Only the Super Admin can delete users.

        </div>

    <?php } ?>


    <?php if (isset($_GET['added'])) { ?>

        <div class="alert alert-success">

            <i class="fa fa-check-circle"></i>

            User added successfully.

        </div>

    <?php } ?>


    <div class="user-card">


        <div class="user-header d-flex justify-content-between align-items-center mb-4">


            <div class="page-title">

                <i class="fa fa-users"></i>

                System Users

            </div>


            <a
                href="add_user.php"
                class="btn btn-add-user"
            >

                <i class="fa fa-user-plus"></i>

                Add User

            </a>


        </div>


        <div class="search-card">


            <div class="search-title">

                <i class="fa fa-filter"></i>

                Search & Filter Users

            </div>


            <form
                method="GET"
                action="manage_users.php"
            >


                <div class="row g-3">


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">

                            Search User

                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Name, IC, Registration No, Phone, Email..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>


                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">

                            Department

                        </label>

                        <select
                            name="department"
                            class="form-select"
                        >

                            <option value="">

                                All Departments

                            </option>


                            <?php

                            if ($department_result) {

                                while (
                                    $department_row =
                                    mysqli_fetch_assoc(
                                        $department_result
                                    )
                                ) {

                                    $dept =
                                        $department_row['department'];

                            ?>

                                <option
                                    value="<?php echo htmlspecialchars($dept); ?>"
                                    <?php
                                    if ($department === $dept) {
                                        echo "selected";
                                    }
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars($dept);
                                    ?>

                                </option>

                            <?php

                                }

                            }

                            ?>

                        </select>

                    </div>


                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">

                            Education Level

                        </label>

                        <select
                            name="education_level"
                            class="form-select"
                        >

                            <option value="">

                                All Levels

                            </option>


                            <?php

                            if ($education_result) {

                                while (
                                    $education_row =
                                    mysqli_fetch_assoc(
                                        $education_result
                                    )
                                ) {

                                    $level =
                                        $education_row['education_level'];

                            ?>

                                <option
                                    value="<?php echo htmlspecialchars($level); ?>"
                                    <?php
                                    if ($education_level === $level) {
                                        echo "selected";
                                    }
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars($level);
                                    ?>

                                </option>

                            <?php

                                }

                            }

                            ?>

                        </select>

                    </div>


                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">

                            Semester

                        </label>

                        <select
                            name="semester"
                            class="form-select"
                        >

                            <option value="">

                                All Semesters

                            </option>


                            <?php

                            if ($semester_result) {

                                while (
                                    $semester_row =
                                    mysqli_fetch_assoc(
                                        $semester_result
                                    )
                                ) {

                                    $sem =
                                        $semester_row['semester'];

                            ?>

                                <option
                                    value="<?php echo htmlspecialchars($sem); ?>"
                                    <?php
                                    if ($semester === $sem) {
                                        echo "selected";
                                    }
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars($sem);
                                    ?>

                                </option>

                            <?php

                                }

                            }

                            ?>

                        </select>

                    </div>


                    <div class="col-lg-2 col-md-12">

                        <label class="form-label d-none d-lg-block">

                            &nbsp;

                        </label>


                        <div class="d-flex gap-2">


                            <button
                                type="submit"
                                class="btn btn-search flex-fill"
                            >

                                <i class="fa fa-search"></i>

                                Search

                            </button>


                            <a
                                href="manage_users.php"
                                class="btn btn-reset"
                                title="Reset"
                            >

                                <i class="fa fa-rotate-left"></i>

                            </a>


                        </div>

                    </div>


                </div>


            </form>

        </div>


        <div class="table-responsive">


            <table class="table table-hover align-middle">


                <thead>

                    <tr>

                        <th>No.</th>

                        <th>Photo</th>

                        <th>Full Name</th>

                        <th>IC Number</th>

                        <th>Registration No.</th>

                        <th>Education Level</th>

                        <th>Semester</th>

                        <th>Phone</th>

                        <th>Email</th>

                        <th>Department</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $result &&
                    mysqli_num_rows($result) > 0
                ) {

                    $no = 1;


                    while (
                        $row =
                        mysqli_fetch_assoc($result)
                    ) {

                ?>


                    <tr>


                        <td>

                            <?php echo $no++; ?>

                        </td>


                        <td>

                            <?php

                            $photo = trim(
                                $row['profile_picture'] ?? ''
                            );

                            $photo_url = "";


                            if ($photo !== "") {

                                if (
                                    strpos($photo, 'http://') === 0 ||
                                    strpos($photo, 'https://') === 0
                                ) {

                                    $photo_url = $photo;

                                }

                                elseif (
                                    strpos($photo, 'uploads/profile/') === 0
                                ) {

                                    $photo_url = "../" . $photo;

                                }

                                elseif (
                                    strpos($photo, 'profile/') === 0
                                ) {

                                    $photo_url =
                                        "../uploads/" . $photo;

                                }

                                else {

                                    $photo_url =
                                        "../uploads/profile/" .
                                        basename($photo);

                                }

                            }


                            if ($photo_url !== "") {

                            ?>

                                <img
                                    src="<?php echo htmlspecialchars($photo_url); ?>"
                                    class="user-photo"
                                    alt="Profile Picture"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <div
                                    class="no-photo"
                                    style="display:none;"
                                >

                                    <i class="fa fa-user"></i>

                                </div>

                            <?php

                            }

                            else {

                            ?>

                                <div class="no-photo">

                                    <i class="fa fa-user"></i>

                                </div>

                            <?php

                            }

                            ?>

                        </td>


                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row['full_name']
                                );

                                ?>

                            </strong>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['ic_number']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['registration_no']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['education_level']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['semester']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['phone']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['email']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['department']
                            );

                            ?>

                        </td>


                        <td>

                            <div class="d-flex gap-1">


                                <a
                                    href="view_user.php?id=<?php echo urlencode($row['user_id']); ?>"
                                    class="btn btn-sm btn-view"
                                    title="View User"
                                >

                                    <i class="fa fa-eye"></i>

                                </a>


                                <?php if ($is_super_admin) { ?>


                                    <a
                                        href="edit_user.php?id=<?php echo urlencode($row['user_id']); ?>"
                                        class="btn btn-sm btn-edit"
                                        title="Edit User"
                                    >

                                        <i class="fa fa-pen"></i>

                                    </a>


                                    <a
                                        href="manage_users.php?delete=<?php echo urlencode($row['user_id']); ?>"
                                        class="btn btn-sm btn-delete"
                                        title="Delete User"
                                        onclick="
                                            return confirm(
                                                'Are you sure you want to delete this user?'
                                            );
                                        "
                                    >

                                        <i class="fa fa-trash"></i>

                                    </a>


                                <?php } ?>


                            </div>

                        </td>


                    </tr>


                <?php

                    }

                }

                else {

                ?>


                    <tr>

                        <td
                            colspan="11"
                            class="text-center py-5"
                        >

                            <i
                                class="fa fa-users fa-2x mb-3"
                            ></i>

                            <br>

                            <strong>

                                No users found.

                            </strong>

                            <br>

                            <small class="text-muted">

                                Try changing your search or filters.

                            </small>

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