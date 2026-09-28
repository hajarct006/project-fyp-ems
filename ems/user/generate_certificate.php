<?php

session_start();

/* =========================================================
   CHECK USER LOGIN
========================================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


/* =========================================================
   DATABASE
========================================================= */

include("../includes/db.php");


/* =========================================================
   DOMPDF
========================================================= */

$autoload_path = __DIR__ . "/../vendor/autoload.php";

if (!file_exists($autoload_path)) {
    die("ERROR: vendor/autoload.php was not found.");
}

require_once($autoload_path);

use Dompdf\Dompdf;
use Dompdf\Options;


/* =========================================================
   GET LOGGED-IN USER
========================================================= */

$user_id = intval($_SESSION['user_id']);


/* =========================================================
   GET REGISTRATION ID
========================================================= */

if (!isset($_GET['registration_id'])) {
    die("Registration ID is missing.");
}

$registration_id = intval($_GET['registration_id']);

if ($registration_id <= 0) {
    die("Invalid Registration ID.");
}


/* =========================================================
   GET REGISTRATION + USER + EVENT + ATTENDANCE
========================================================= */

$sql = "
    SELECT
        registrations.registration_id,
        registrations.user_id,
        registrations.event_id,

        users.full_name,

        events.event_title,
        events.event_date,
        events.venue,

        attendance.attendance_status,
        attendance.check_in

    FROM registrations

    INNER JOIN users
        ON registrations.user_id = users.user_id

    INNER JOIN events
        ON registrations.event_id = events.event_id

    LEFT JOIN attendance
        ON registrations.registration_id = attendance.registration_id

    WHERE registrations.registration_id = ?
    AND registrations.user_id = ?

    LIMIT 1
";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die(
        "Database query failed: " .
        mysqli_error($conn)
    );
}


mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $registration_id,
    $user_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$data = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* =========================================================
   CHECK REGISTRATION
========================================================= */

if (!$data) {

    die(
        "Registration not found or you are not authorized to access this certificate."
    );

}


/* =========================================================
   CHECK SURVEY COMPLETION
========================================================= */

$survey_sql = "
    SELECT
        (SELECT COUNT(*) FROM survey_questions WHERE event_id = registrations.event_id OR event_id IS NULL) AS total_questions,
        (SELECT COUNT(DISTINCT sa.question_id)
         FROM survey_answers sa
         WHERE sa.user_id = registrations.user_id
         AND sa.event_id = registrations.event_id) AS total_answered
    FROM registrations
    WHERE registration_id = ?
    LIMIT 1
";

$survey_stmt = mysqli_prepare($conn, $survey_sql);

if (!$survey_stmt) {
    die("Survey database query failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($survey_stmt, "i", $registration_id);
mysqli_stmt_execute($survey_stmt);
$survey_result = mysqli_stmt_get_result($survey_stmt);
$survey_data = mysqli_fetch_assoc($survey_result);
mysqli_stmt_close($survey_stmt);

$total_questions = intval($survey_data['total_questions'] ?? 0);
$total_answered = intval($survey_data['total_answered'] ?? 0);

if ($total_questions <= 0 || $total_answered < $total_questions) {
    die("Certificate is locked. Please complete the event survey before accessing your certificate.");
}


/* =========================================================
   CHECK ATTENDANCE
========================================================= */

$attendance_status = strtolower(
    trim(
        $data['attendance_status'] ?? ''
    )
);


if ($attendance_status !== 'present') {

    die(
        "Certificate is not available because attendance is not marked as Present."
    );

}


/* =========================================================
   PARTICIPANT INFORMATION
========================================================= */

$full_name = $data['full_name'];

$event_title = $data['event_title'];

$event_date = date(
    "d F Y",
    strtotime($data['event_date'])
);


/* =========================================================
   CHECK CERTIFICATE TABLE
========================================================= */

$check_sql = "
    SELECT
        certificates_id,
        certificates_no,
        issue_date,
        certificate_file

    FROM certificates

    WHERE registration_id = ?

    LIMIT 1
";


$check_stmt = mysqli_prepare(
    $conn,
    $check_sql
);


if (!$check_stmt) {

    die(
        "Certificate database query failed: " .
        mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $check_stmt,
    "i",
    $registration_id
);


mysqli_stmt_execute(
    $check_stmt
);


$check_result = mysqli_stmt_get_result(
    $check_stmt
);


$certificate = mysqli_fetch_assoc(
    $check_result
);


mysqli_stmt_close(
    $check_stmt
);


/* =========================================================
   CERTIFICATE NUMBER
========================================================= */

if ($certificate) {

    $certificate_no =
        $certificate['certificates_no'];

} else {

    $number_sql = "
        SELECT
            MAX(certificates_id) AS last_id
        FROM certificates
    ";


    $number_result = mysqli_query(
        $conn,
        $number_sql
    );


    if (!$number_result) {

        die(
            "Unable to generate certificate number: " .
            mysqli_error($conn)
        );

    }


    $number_data = mysqli_fetch_assoc(
        $number_result
    );


    $next_number =
        intval(
            $number_data['last_id']
        ) + 1;


    $certificate_no =
        "JPP-" .
        date("Y") .
        "-" .
        str_pad(
            $next_number,
            5,
            "0",
            STR_PAD_LEFT
        );

}


/* =========================================================
   CERTIFICATE TEMPLATE
========================================================= */

/*
   IMPORTANT:

   Your template filename is:

   certificate_template.png

   Therefore we use that exact filename.
*/

$template_path =
    __DIR__ .
    "/certificate_template.png";


/* =========================================================
   CHECK TEMPLATE
========================================================= */

if (!file_exists($template_path)) {

    die(
        "Certificate template not found.<br><br>" .
        "Make sure this file exists:<br>" .
        htmlspecialchars($template_path)
    );

}


/* =========================================================
   READ TEMPLATE
========================================================= */

$image_content = file_get_contents(
    $template_path
);


if ($image_content === false) {

    die(
        "Unable to read certificate_template.png."
    );

}


$image_data = base64_encode(
    $image_content
);


$image_src =
    "data:image/png;base64," .
    $image_data;


/* =========================================================
   ESCAPE TEXT
========================================================= */

$safe_name =
    htmlspecialchars(
        $full_name,
        ENT_QUOTES,
        'UTF-8'
    );


$safe_event =
    htmlspecialchars(
        $event_title,
        ENT_QUOTES,
        'UTF-8'
    );


$safe_date =
    htmlspecialchars(
        $event_date,
        ENT_QUOTES,
        'UTF-8'
    );


$safe_certificate_no =
    htmlspecialchars(
        $certificate_no,
        ENT_QUOTES,
        'UTF-8'
    );


/* =========================================================
   CERTIFICATE HTML
========================================================= */

$html = '

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<style>

@page {

    size: A4 landscape;

    margin: 0;

}


html,
body {

    margin: 0;

    padding: 0;

    width: 100%;

    height: 100%;

}


.certificate {

    position: relative;

    width: 297mm;

    height: 210mm;

    background-image: url("' . $image_src . '");

    background-size: 100% 100%;

    background-repeat: no-repeat;

}


/* =====================================================
   NAME
===================================================== */

.name {

    position: absolute;

    top: 92mm;

    left: 25mm;

    width: 247mm;

    text-align: center;

    font-family: DejaVu Serif;

    font-size: 26px;

    font-weight: bold;

}


/* =====================================================
   EVENT
===================================================== */

.event {

    position: absolute;

    top: 117mm;

    left: 40mm;

    width: 217mm;

    text-align: center;

    font-family: DejaVu Serif;

    font-size: 17px;

}


/* =====================================================
   DATE
===================================================== */

.date {

    position: absolute;

    top: 128mm;

    left: 40mm;

    width: 217mm;

    text-align: center;

    font-family: DejaVu Serif;

    font-size: 15px;

}


/* =====================================================
   CERTIFICATE NUMBER
===================================================== */

.certificate-no {

    position: absolute;

    bottom: 15mm;

    left: 20mm;

    width: 257mm;

    text-align: center;

    font-family: DejaVu Serif;

    font-size: 12px;

}

</style>

</head>


<body>


<div class="certificate">


    <div class="name">

        ' . $safe_name . '

    </div>


    <div class="event">

        ' . $safe_event . '

    </div>


    <div class="date">

        ' . $safe_date . '

    </div>


    <div class="certificate-no">

        Certificate No: ' .
        $safe_certificate_no .
        '

    </div>


</div>


</body>

</html>

';


/* =========================================================
   DOMPDF OPTIONS
========================================================= */

$options = new Options();

$options->set(
    'isRemoteEnabled',
    false
);

$options->set(
    'isHtml5ParserEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Serif'
);


/* =========================================================
   CREATE DOMPDF
========================================================= */

$dompdf =
    new Dompdf(
        $options
    );


/* =========================================================
   LOAD HTML
========================================================= */

$dompdf->loadHtml(
    $html
);


/* =========================================================
   SET PAPER
========================================================= */

$dompdf->setPaper(
    'A4',
    'landscape'
);


/* =========================================================
   GENERATE PDF
========================================================= */

$dompdf->render();


/* =========================================================
   PDF FILE NAME
========================================================= */

$filename =
    "certificate_" .
    $registration_id .
    ".pdf";


/* =========================================================
   GENERATED FOLDER
========================================================= */

$certificate_folder =
    __DIR__ .
    "/generated/";


/* =========================================================
   CREATE GENERATED FOLDER
========================================================= */

if (!is_dir($certificate_folder)) {

    if (!mkdir(
        $certificate_folder,
        0777,
        true
    )) {

        die(
            "Unable to create generated certificate folder."
        );

    }

}


/* =========================================================
   SAVE PDF
========================================================= */

$file_path =
    $certificate_folder .
    $filename;


$pdf_content =
    $dompdf->output();


if (
    file_put_contents(
        $file_path,
        $pdf_content
    ) === false
) {

    die(
        "Unable to save generated certificate."
    );

}


/* =========================================================
   SAVE DATABASE RECORD
========================================================= */

if (!$certificate) {

    $insert_sql = "
        INSERT INTO certificates
        (
            registration_id,
            certificates_no,
            issue_date,
            certificate_file
        )

        VALUES
        (
            ?,
            ?,
            CURDATE(),
            ?
        )
    ";


    $insert_stmt =
        mysqli_prepare(
            $conn,
            $insert_sql
        );


    if (!$insert_stmt) {

        die(
            "Certificate insert failed: " .
            mysqli_error($conn)
        );

    }


    mysqli_stmt_bind_param(
        $insert_stmt,
        "iss",
        $registration_id,
        $certificate_no,
        $filename
    );


    if (!mysqli_stmt_execute(
        $insert_stmt
    )) {

        die(
            "Unable to save certificate record: " .
            mysqli_stmt_error($insert_stmt)
        );

    }


    mysqli_stmt_close(
        $insert_stmt
    );

} else {

    /* =====================================================
       UPDATE FILE NAME IF OLD RECORD HAS EMPTY FILE
    ===================================================== */

    if (
        empty(
            $certificate['certificate_file']
        )
    ) {

        $update_sql = "
            UPDATE certificates

            SET certificate_file = ?

            WHERE registration_id = ?
        ";


        $update_stmt =
            mysqli_prepare(
                $conn,
                $update_sql
            );


        if ($update_stmt) {

            mysqli_stmt_bind_param(
                $update_stmt,
                "si",
                $filename,
                $registration_id
            );


            mysqli_stmt_execute(
                $update_stmt
            );


            mysqli_stmt_close(
                $update_stmt
            );

        }

    }

}


/* =========================================================
   DOWNLOAD
========================================================= */

$download_name =
    "Certificate-" .
    preg_replace(
        '/[^A-Za-z0-9_-]/',
        '_',
        $full_name
    ) .
    ".pdf";


$dompdf->stream(
    $download_name,
    [
        "Attachment" => true
    ]
);


exit();

?>