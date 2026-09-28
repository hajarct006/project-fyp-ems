<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

include("../includes/db.php");

$user_id = intval($_SESSION['user_id']);

$event_id = $_GET['id'] ?? $_GET['event_id'] ?? $_POST['event_id'] ?? '';

if(empty($event_id) || !is_numeric($event_id))
{
    die("Invalid event.");
}

$event_id = intval($event_id);


// =====================================================
// GET EVENT
// =====================================================

$event_query = mysqli_query(
    $conn,
    "
    SELECT event_id, event_title, event_date, venue
    FROM events
    WHERE event_id='$event_id'
    LIMIT 1
    "
);

if(!$event_query)
{
    die("Event query failed: " . mysqli_error($conn));
}

$event = mysqli_fetch_assoc($event_query);

if(!$event)
{
    die("Event not found.");
}


// =====================================================
// CHECK REGISTRATION
// =====================================================

$registration_query = mysqli_query(
    $conn,
    "
    SELECT registration_id
    FROM registrations
    WHERE user_id='$user_id'
    AND event_id='$event_id'
    AND LOWER(TRIM(status))='registered'
    LIMIT 1
    "
);

if(!$registration_query)
{
    die("Registration query failed: " . mysqli_error($conn));
}

if(mysqli_num_rows($registration_query) == 0)
{
    die("You are not registered for this event.");
}

$registration = mysqli_fetch_assoc($registration_query);

$registration_id = intval($registration['registration_id']);


// =====================================================
// CHECK ATTENDANCE
// =====================================================

$attendance_query = mysqli_query(
    $conn,
    "
    SELECT attendance_status
    FROM attendance
    WHERE registration_id='$registration_id'
    LIMIT 1
    "
);

if(!$attendance_query)
{
    die("Attendance query failed: " . mysqli_error($conn));
}

$attendance = mysqli_fetch_assoc($attendance_query);

if(
    !$attendance ||
    strtolower(trim($attendance['attendance_status'])) != 'present'
)
{
    die("You have not attended this event.");
}


// =====================================================
// CHECK IF SURVEY ALREADY SUBMITTED
// =====================================================

$confirmation_query = mysqli_query(
    $conn,
    "
    SELECT confirmation_id
    FROM survey_confirmations
    WHERE user_id='$user_id'
    AND event_id='$event_id'
    LIMIT 1
    "
);

if(!$confirmation_query)
{
    die("Survey confirmation query failed: " . mysqli_error($conn));
}

$already_submitted = mysqli_num_rows($confirmation_query) > 0;


// =====================================================
// VIEW ANSWERS
// =====================================================

$view_answers = isset($_GET['view']) && $_GET['view'] == 'answers';

$submitted_answers = [];

if($view_answers && $already_submitted)
{
    $answers_query = mysqli_query(
        $conn,
        "
        SELECT
            sq.question_number,
            sq.section,
            sq.question_text,
            sa.rating
        FROM survey_answers sa
        INNER JOIN survey_questions sq
            ON sa.question_id = sq.question_id
        WHERE sa.user_id='$user_id'
        AND sa.event_id='$event_id'
        ORDER BY sq.question_number ASC
        "
    );

    if(!$answers_query)
    {
        die("Answers query failed: " . mysqli_error($conn));
    }

    while($answer = mysqli_fetch_assoc($answers_query))
    {
        $submitted_answers[] = $answer;
    }
}


// =====================================================
// SUBMIT SURVEY
// =====================================================

$message = "";
$message_type = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && !$already_submitted)
{
    $questions_query = mysqli_query(
        $conn,
        "
        SELECT *
        FROM survey_questions
        ORDER BY question_number ASC
        "
    );

    if(!$questions_query)
    {
        die("Questions query failed: " . mysqli_error($conn));
    }

    $answers = [];

    $valid = true;

    while($question = mysqli_fetch_assoc($questions_query))
    {
        $question_id = intval($question['question_id']);

        $rating = $_POST['question_'.$question_id] ?? '';

        if(!in_array($rating, ['1','2','3','4'], true))
        {
            $valid = false;
            break;
        }

        $answers[] = [
            'question_id' => $question_id,
            'rating' => intval($rating)
        ];
    }

    if(!$valid)
    {
        $message = "Sila jawab semua soalan sebelum menghantar.";

        $message_type = "error";
    }
    else
    {
        mysqli_begin_transaction($conn);

        $success = true;

        foreach($answers as $answer)
        {
            $question_id = $answer['question_id'];

            $rating = $answer['rating'];

            $insert_answer = mysqli_query(
                $conn,
                "
                INSERT INTO survey_answers
                (
                    question_id,
                    user_id,
                    event_id,
                    rating
                )
                VALUES
                (
                    '$question_id',
                    '$user_id',
                    '$event_id',
                    '$rating'
                )
                "
            );

            if(!$insert_answer)
            {
                $success = false;
                break;
            }
        }

        if($success)
        {
            $insert_confirmation = mysqli_query(
                $conn,
                "
                INSERT INTO survey_confirmations
                (
                    user_id,
                    event_id
                )
                VALUES
                (
                    '$user_id',
                    '$event_id'
                )
                "
            );

            if(!$insert_confirmation)
            {
                $success = false;
            }
        }

        if($success)
        {
            mysqli_commit($conn);

            $already_submitted = true;

            $message = "Terima kasih atas maklum balas anda.";

            $message_type = "success";
        }
        else
        {
            mysqli_rollback($conn);

            $message = "Survey gagal dihantar. Sila cuba lagi.";

            $message_type = "error";
        }
    }
}


// =====================================================
// GET QUESTIONS
// =====================================================

$questions_query = mysqli_query(
    $conn,
    "
    SELECT *
    FROM survey_questions
    ORDER BY question_number ASC
    "
);

if(!$questions_query)
{
    die("Questions query failed: " . mysqli_error($conn));
}

$sections = [];

while($question = mysqli_fetch_assoc($questions_query))
{
    $sections[$question['section']][] = $question;
}


// =====================================================
// RATING LABEL
// =====================================================

function getRatingLabel($rating)
{
    if($rating == 1)
    {
        return "Sangat Tidak Setuju";
    }

    if($rating == 2)
    {
        return "Tidak Setuju";
    }

    if($rating == 3)
    {
        return "Setuju";
    }

    if($rating == 4)
    {
        return "Sangat Setuju";
    }

    return "";
}

?>

<!DOCTYPE html>

<html lang="ms">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"

>

<title>
    Survey -
    <?= htmlspecialchars($event['event_title']) ?>
</title>

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

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'DM Sans',sans-serif;

}

body{

    background:#f5f5f5;

}

.main{

    margin-left:250px;

    padding:30px;

}

.survey-container{

    max-width:1100px;

    margin:0 auto;

}

.survey-header{

    background:#800020;

    color:white;

    padding:30px;

    border-radius:15px 15px 0 0;

}

.survey-header h2{

    margin:0;

    font-weight:600;

}

.event-info{

    margin-top:15px;

    font-size:14px;

    line-height:1.8;

}

.survey-card{

    background:white;

    padding:25px;

    border-radius:0 0 15px 15px;

    box-shadow:0 3px 15px rgba(0,0,0,0.08);

}

.instruction{

    background:#fff8e1;

    border-left:5px solid #D4AF37;

    padding:15px;

    margin-bottom:25px;

    font-size:14px;

}

.section-title{

    background:#800020;

    color:white;

    padding:12px 15px;

    margin-top:30px;

    margin-bottom:0;

    font-size:16px;

    font-weight:600;

    border-radius:8px 8px 0 0;

}

.table-responsive{

    border:1px solid #ddd;

    border-top:none;

}

.survey-table{

    width:100%;

    border-collapse:collapse;

    margin:0;

}

.survey-table th,

.survey-table td{

    border:1px solid #ddd;

    padding:12px;

    text-align:center;

    vertical-align:middle;

}

.survey-table th{

    background:#f5eeee;

    color:#800020;

    font-size:13px;

    font-weight:600;

}

.survey-table th:first-child{

    text-align:left;

    width:55%;

}

.survey-table td:first-child{

    text-align:left;

    font-size:14px;

}

.survey-table input[type="radio"]{

    width:18px;

    height:18px;

    accent-color:#800020;

    cursor:pointer;

}

.question-number{

    font-weight:600;

    color:#800020;

}

.submit-area{

    text-align:center;

    margin-top:35px;

}

.submit-btn{

    background:#800020;

    color:white;

    border:none;

    padding:12px 35px;

    border-radius:8px;

    font-weight:500;

}

.submit-btn:hover{

    background:#600018;

    color:white;

}

.view-btn{

    background:#800020;

    color:white;

    border:none;

    padding:12px 25px;

    border-radius:8px;

    font-weight:500;

    text-decoration:none;

    display:inline-block;

    margin:5px;

}

.view-btn:hover{

    background:#600018;

    color:white;

}

.back-btn{

    background:#6c757d;

    color:white;

    border:none;

    padding:12px 25px;

    border-radius:8px;

    font-weight:500;

    text-decoration:none;

    display:inline-block;

    margin:5px;

}

.back-btn:hover{

    background:#5a6268;

    color:white;

}

.success-message{

    background:#e8f5e9;

    color:#2e7d32;

    padding:15px;

    border-radius:8px;

    margin-bottom:20px;

    text-align:center;

}

.error-message{

    background:#ffebee;

    color:#c62828;

    padding:15px;

    border-radius:8px;

    margin-bottom:20px;

    text-align:center;

}

.already-box{

    background:#f5f5f5;

    border-radius:10px;

    padding:30px;

    text-align:center;

    margin-top:20px;

}

.already-box h4{

    color:#800020;

    margin-bottom:10px;

}

.answer-table{

    width:100%;

    border-collapse:collapse;

    margin-top:25px;

}

.answer-table th,

.answer-table td{

    border:1px solid #ddd;

    padding:12px;

    vertical-align:middle;

}

.answer-table th{

    background:#f5eeee;

    color:#800020;

    font-size:13px;

    font-weight:600;

}

.answer-table td{

    font-size:14px;

}

.answer-number{

    width:7%;

    text-align:center;

    font-weight:600;

    color:#800020;

}

.answer-value{

    width:20%;

    text-align:center;

    font-weight:500;

    color:#800020;

}

.answer-section{

    background:#800020;

    color:white;

    padding:12px 15px;

    margin-top:30px;

    font-size:16px;

    font-weight:600;

    border-radius:8px 8px 0 0;

}

@media(max-width:991px){

    .main{

        margin-left:0;

        padding:24px 18px;

        padding-top:68px;

    }

}

@media(max-width:576px){

    .main{

        padding:20px 14px;

        padding-top:64px;

    }

    .survey-card{

        padding:15px;

    }

    .survey-header{

        padding:20px;

    }

    .survey-header h2{

        font-size:20px;

    }

    .survey-table th,

    .survey-table td{

        padding:8px 5px;

    }

    .survey-table th{

        font-size:10px;

    }

    .survey-table td:first-child{

        font-size:12px;

    }

    .survey-table input[type="radio"]{

        width:15px;

        height:15px;

    }

    .answer-table th,

    .answer-table td{

        padding:8px 6px;

        font-size:11px;

    }

    .answer-value{

        width:30%;

    }

}

</style>

</head>

<body>

<?php

include("user_menu.php");

?>

<div class="main">

<div class="survey-container">

    <div class="survey-header">

        <h2>

            Penilaian Program

        </h2>

        <div class="event-info">

            <strong>

                <?= htmlspecialchars($event['event_title']) ?>

            </strong>

            <br>

            Tarikh:

            <?= date(
                "d/m/Y",
                strtotime($event['event_date'])
            ) ?>

            <br>

            Tempat:

            <?= htmlspecialchars($event['venue']) ?>

        </div>

    </div>

    <div class="survey-card">

        <?php if($message_type == "success"): ?>

            <div class="success-message">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php elseif($message_type == "error"): ?>

            <div class="error-message">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <?php if($view_answers && $already_submitted): ?>

            <div class="already-box">

                <h4>

                    <i class="fa fa-circle-check"></i>

                    Jawapan Survey Anda

                </h4>

                <p>

                    Berikut adalah jawapan yang telah anda
                    hantar untuk program ini.

                </p>

            </div>


            <?php

            $current_section = "";

            foreach($submitted_answers as $answer):

                if($current_section != $answer['section']):

                    if($current_section != "")
                    {
                        echo "</tbody></table></div>";
                    }

                    $current_section = $answer['section'];

            ?>

                    <div class="answer-section">

                        <?= htmlspecialchars($current_section) ?>

                    </div>

                    <div class="table-responsive">

                        <table class="answer-table">

                            <thead>

                                <tr>

                                    <th class="answer-number">

                                        No.

                                    </th>

                                    <th>

                                        Pernyataan

                                    </th>

                                    <th class="answer-value">

                                        Jawapan Anda

                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                <?php endif; ?>


                                <tr>

                                    <td class="answer-number">

                                        <?= htmlspecialchars(
                                            $answer['question_number']
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $answer['question_text']
                                        ) ?>

                                    </td>

                                    <td class="answer-value">

                                        <?= htmlspecialchars(
                                            getRatingLabel(
                                                intval($answer['rating'])
                                            )
                                        ) ?>

                                    </td>

                                </tr>


            <?php endforeach; ?>


            <?php if(!empty($submitted_answers)): ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>


            <?php if(empty($submitted_answers)): ?>

                <div class="error-message">

                    Tiada jawapan survey dijumpai.

                </div>

            <?php endif; ?>


            <div class="submit-area">

                <a
                    href="survey.php?id=<?= $event_id ?>"
                    class="back-btn"
                >

                    <i class="fa fa-arrow-left"></i>

                    Back to Survey

                </a>

                <a
                    href="exemption.php?id=<?= $event_id ?>"
                    class="view-btn"
                >

                    <i class="fa fa-file-lines"></i>

                    Back to Documents

                </a>

            </div>


        <?php elseif($already_submitted): ?>

            <div class="already-box">

                <h4>

                    <i class="fa fa-circle-check"></i>

                    Survey Telah Dihantar

                </h4>

                <p>

                    Anda telah memberikan maklum balas
                    untuk program ini.

                </p>

                <div class="submit-area">

                    <a
                        href="survey.php?id=<?= $event_id ?>&view=answers"
                        class="view-btn"
                    >

                        <i class="fa fa-eye"></i>

                        View My Answers

                    </a>

                    <a
                        href="exemption.php?id=<?= $event_id ?>"
                        class="back-btn"
                    >

                        <i class="fa fa-file-lines"></i>

                        Back to Documents

                    </a>

                </div>

            </div>


        <?php else: ?>

            <div class="instruction">

                <strong>Arahan:</strong>

                <br>

                Sila tandakan satu jawapan bagi setiap
                pernyataan berdasarkan pengalaman anda
                sepanjang mengikuti program ini.

                <br><br>

                <strong>Skala:</strong>

                1 = Sangat Tidak Setuju

                &nbsp;&nbsp;

                2 = Tidak Setuju

                &nbsp;&nbsp;

                3 = Setuju

                &nbsp;&nbsp;

                4 = Sangat Setuju

            </div>


            <form
                method="POST"
                action="survey.php?id=<?= $event_id ?>"
            >

                <input
                    type="hidden"
                    name="event_id"
                    value="<?= $event_id ?>"
                >


                <?php foreach($sections as $section => $questions): ?>

                    <div class="section-title">

                        <?= htmlspecialchars($section) ?>

                    </div>

                    <div class="table-responsive">

                        <table class="survey-table">

                            <thead>

                                <tr>

                                    <th>

                                        Pernyataan

                                    </th>

                                    <th>

                                        1

                                        <br>

                                        <small>
                                            Sangat Tidak Setuju
                                        </small>

                                    </th>

                                    <th>

                                        2

                                        <br>

                                        <small>
                                            Tidak Setuju
                                        </small>

                                    </th>

                                    <th>

                                        3

                                        <br>

                                        <small>
                                            Setuju
                                        </small>

                                    </th>

                                    <th>

                                        4

                                        <br>

                                        <small>
                                            Sangat Setuju
                                        </small>

                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach($questions as $question): ?>

                                    <tr>

                                        <td>

                                            <span class="question-number">

                                                <?= $question['question_number'] ?>.

                                            </span>

                                            <?= htmlspecialchars(
                                                $question['question_text']
                                            ) ?>

                                        </td>

                                        <?php for(
                                            $rating = 1;
                                            $rating <= 4;
                                            $rating++
                                        ): ?>

                                            <td>

                                                <input
                                                    type="radio"
                                                    name="question_<?= $question['question_id'] ?>"
                                                    value="<?= $rating ?>"
                                                    required
                                                >

                                            </td>

                                        <?php endfor; ?>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endforeach; ?>


                <div class="submit-area">

                    <button
                        type="submit"
                        class="submit-btn"
                    >

                        <i class="fa fa-paper-plane"></i>

                        Hantar Maklum Balas

                    </button>

                </div>

            </form>

        <?php endif; ?>

    </div>

</div>

</div>

</body>

</html>
