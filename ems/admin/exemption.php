<?php

session_start();

if(!isset($_SESSION['admin_id']))
{
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

$message = "";


// =========================================================
// UPLOAD / REPLACE MEMO
// =========================================================

if(isset($_POST['upload_memo']))
{
    $event_id = mysqli_real_escape_string(
        $conn,
        $_POST['event_id']
    );

    if(
        isset($_FILES['memo_file']) &&
        $_FILES['memo_file']['name'] != ""
    )
    {
        if(!is_dir("../uploads/memos"))
        {
            mkdir("../uploads/memos", 0777, true);
        }

        $file_name = time() . "_" .
            preg_replace(
                '/[^A-Za-z0-9._-]/',
                '_',
                $_FILES['memo_file']['name']
            );

        $upload_path =
            "../uploads/memos/" . $file_name;

        if(
            move_uploaded_file(
                $_FILES['memo_file']['tmp_name'],
                $upload_path
            )
        )
        {
            mysqli_query(
                $conn,
                "DELETE FROM exemption_memos
                 WHERE event_id='$event_id'"
            );

            mysqli_query(
                $conn,
                "INSERT INTO exemption_memos
                (event_id, memo_file)
                VALUES
                ('$event_id', '$file_name')"
            );

            $message = "
            <div class='alert alert-success'>
                <i class='fa fa-circle-check'></i>
                Exemption Memo Successfully Uploaded.
            </div>";
        }
        else
        {
            $message = "
            <div class='alert alert-danger'>
                <i class='fa fa-circle-xmark'></i>
                Failed to upload memo.
            </div>";
        }
    }
    else
    {
        $message = "
        <div class='alert alert-danger'>
            <i class='fa fa-circle-xmark'></i>
            Please choose a memo file to upload.
        </div>";
    }
}


// =========================================================
// DELETE MEMO
// =========================================================

if(isset($_GET['delete_memo']))
{
    $memo_id = mysqli_real_escape_string(
        $conn,
        $_GET['delete_memo']
    );

    $memo_file_result = mysqli_query(
        $conn,
        "SELECT memo_file
         FROM exemption_memos
         WHERE memo_id='$memo_id'"
    );

    if(
        $memo_file_result &&
        mysqli_num_rows($memo_file_result) > 0
    )
    {
        $memo_file_data =
            mysqli_fetch_assoc(
                $memo_file_result
            );

        $file_path =
            "../uploads/memos/" .
            $memo_file_data['memo_file'];

        if(file_exists($file_path))
        {
            unlink($file_path);
        }
    }

    mysqli_query(
        $conn,
        "DELETE FROM exemption_memos
         WHERE memo_id='$memo_id'"
    );

    $message = "
    <div class='alert alert-success'>
        <i class='fa fa-circle-check'></i>
        Memo Deleted.
    </div>";
}


// =========================================================
// ADD SURVEY QUESTION
// =========================================================

if(isset($_POST['add_question']))
{
    $question_text = mysqli_real_escape_string(
        $conn,
        trim($_POST['question_text'])
    );

    if($question_text != "")
    {
        $number_result = mysqli_query(
            $conn,
            "SELECT MAX(question_number) AS max_number
             FROM survey_questions"
        );

        $number_data =
            mysqli_fetch_assoc(
                $number_result
            );

        $next_number =
            intval($number_data['max_number']) + 1;

        mysqli_query(
            $conn,
            "INSERT INTO survey_questions
            (
                section,
                question_number,
                question_text
            )
            VALUES
            (
                'General',
                '$next_number',
                '$question_text'
            )"
        );

        $message = "
        <div class='alert alert-success'>
            <i class='fa fa-circle-check'></i>
            Survey Question Added.
        </div>";
    }
}


// =========================================================
// EDIT SURVEY QUESTION
// =========================================================

if(isset($_POST['edit_question']))
{
    $question_id = mysqli_real_escape_string(
        $conn,
        $_POST['question_id']
    );

    $question_text = mysqli_real_escape_string(
        $conn,
        trim($_POST['question_text'])
    );

    if($question_text != "")
    {
        mysqli_query(
            $conn,
            "UPDATE survey_questions
             SET question_text='$question_text'
             WHERE question_id='$question_id'"
        );

        $message = "
        <div class='alert alert-success'>
            <i class='fa fa-circle-check'></i>
            Survey Question Updated Successfully.
        </div>";
    }
    else
    {
        $message = "
        <div class='alert alert-danger'>
            <i class='fa fa-circle-xmark'></i>
            Question cannot be empty.
        </div>";
    }
}


// =========================================================
// DELETE SURVEY QUESTION
// =========================================================

if(isset($_GET['delete_question']))
{
    $question_id = mysqli_real_escape_string(
        $conn,
        $_GET['delete_question']
    );

    mysqli_query(
        $conn,
        "DELETE FROM survey_questions
         WHERE question_id='$question_id'"
    );

    $message = "
    <div class='alert alert-success'>
        <i class='fa fa-circle-check'></i>
        Survey Question Deleted.
    </div>";
}


// =========================================================
// GET EVENTS
// =========================================================

$events_result = mysqli_query(
    $conn,
    "SELECT
        event_id,
        event_title,
        event_date
     FROM events
     ORDER BY event_date DESC"
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

<title>Exemption - Memo & Survey</title>


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


<link
    rel="stylesheet"
    href="menu.css"
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

    overflow-x:hidden;
}

.main{
    margin-left:250px;

    padding:30px;

    width:calc(100% - 250px);

    min-height:100vh;
}

.topbar{
    background:white;

    padding:18px 25px;

    border-radius:15px;

    box-shadow:
        0 10px 25px
        rgba(0,0,0,.08);

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

    flex-wrap:wrap;

    gap:12px;
}

.topbar h3{
    margin:0;

    font-size:24px;

    color:#800020;
}

.topbar span{
    font-size:15px;
}

.card-box{
    background:white;

    padding:25px;

    border-radius:20px;

    box-shadow:
        0 10px 25px
        rgba(0,0,0,.08);

    margin-bottom:20px;

    width:100%;

    overflow:hidden;
}

.page-description{
    color:#666;

    margin-bottom:20px;

    line-height:1.6;
}

.event-title-row{
    display:flex;

    justify-content:space-between;

    align-items:flex-start;

    flex-wrap:wrap;

    gap:10px;

    margin-bottom:20px;
}

.event-title-row h5{
    color:#800020;

    font-weight:700;

    margin:0;

    word-break:break-word;

    overflow-wrap:anywhere;

    line-height:1.5;
}

.event-title-row small{
    font-size:15px;

    font-weight:normal;
}

.col-md-6{
    min-width:0;
}

.col-md-6 h6{
    margin-bottom:12px;
}

.memo-file{
    display:flex;

    align-items:flex-start;

    justify-content:space-between;

    gap:12px;

    background:#f9f5f6;

    border-radius:12px;

    padding:13px 15px;

    margin-bottom:12px;

    width:100%;

    min-width:0;

    overflow:hidden;
}

.memo-file > span{
    display:flex;

    align-items:flex-start;

    gap:8px;

    min-width:0;

    flex:1;

    word-break:break-word;

    overflow-wrap:anywhere;

    line-height:1.5;
}

.memo-file > span i{
    flex-shrink:0;

    margin-top:4px;
}

.memo-file > div{
    display:flex;

    gap:5px;

    flex-shrink:0;
}

.memo-file .btn{
    width:38px;

    height:38px;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:0;

    flex-shrink:0;
}

.memo-upload-form{
    display:flex;

    gap:8px;

    width:100%;

    align-items:center;
}

.memo-upload-form input[type="file"]{
    min-width:0;

    flex:1;

    width:100%;
}

.memo-upload-form button{
    flex-shrink:0;
}

.question-item{
    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:12px;

    background:#f9f5f6;

    border-radius:10px;

    padding:12px 15px;

    margin-bottom:8px;

    width:100%;

    min-width:0;

    overflow:hidden;
}

.question-text{
    flex:1;

    min-width:0;

    word-break:break-word;

    overflow-wrap:anywhere;

    line-height:1.5;
}

.question-buttons{
    display:flex;

    gap:5px;

    flex-shrink:0;
}

.question-buttons .btn{
    width:38px;

    height:38px;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:0;
}

.question-edit-form{
    display:flex;

    gap:8px;

    width:100%;

    align-items:center;
}

.question-edit-form input{
    min-width:0;

    flex:1;

    width:100%;
}

.question-edit-buttons{
    display:flex;

    gap:5px;

    flex-shrink:0;
}

.question-edit-buttons .btn{
    width:38px;

    height:38px;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:0;
}

.survey-form{
    display:flex;

    gap:8px;

    width:100%;
}

.survey-form input{
    min-width:0;

    flex:1;

    width:100%;
}

.survey-form button{
    flex-shrink:0;
}

.btn-maroon{
    background:#800020;

    color:white;

    border:none;
}

.btn-maroon:hover{
    background:#a00028;

    color:white;
}

.btn-gold{
    background:#800020;

    color:#fff;

    font-weight:bold;

    border:none;
}

.btn-gold:hover{
    background:#5c0017;

    color:#fff;
}

.btn-edit{
    background:#800020;

    color:#fff;

    border:none;
}

.btn-edit:hover{
    background:#FFD700;

    color:#800020;
}

.search-box{
    background:white;

    padding:18px 20px;

    border-radius:15px;

    box-shadow:
        0 10px 25px
        rgba(0,0,0,.08);

    margin-bottom:20px;
}

.search-wrapper{
    position:relative;
}

.search-wrapper > i{
    position:absolute;

    left:15px;

    top:50%;

    transform:translateY(-50%);

    color:#800020;

    z-index:2;
}

.search-wrapper input{
    padding-left:45px;

    padding-right:45px;

    height:45px;

    border-radius:10px;

    border:1px solid #ddd;

    width:100%;
}

.search-wrapper input:focus{
    border-color:#800020;

    box-shadow:
        0 0 0 0.2rem
        rgba(128,0,32,.15);
}

.clear-search{
    position:absolute;

    right:15px;

    top:50%;

    transform:translateY(-50%);

    border:none;

    background:none;

    color:#888;

    display:none;

    cursor:pointer;

    z-index:2;
}

.clear-search:hover{
    color:#800020;
}

.no-search-result{
    display:none;

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:
        0 10px 25px
        rgba(0,0,0,.08);

    text-align:center;

    color:#777;

    margin-bottom:20px;
}

.survey-info-box{
    display:flex;

    align-items:flex-start;

    gap:14px;

    background:
        linear-gradient(
            135deg,
            #fff9ef,
            #fef3e2
        );

    border:1px solid
        rgba(212,175,55,.35);

    border-radius:14px;

    padding:16px 18px;

    box-shadow:
        0 4px 14px
        rgba(128,0,32,.06);

    margin-bottom:20px;
}

.survey-info-box i{
    color:#2e7d32;

    font-size:22px;

    margin-top:2px;
}

@media(max-width:991px){

    .main{
        margin-left:0;

        width:100%;

        padding:24px 18px;

        padding-top:68px;
    }

}

@media(max-width:767px){

    .main{
        padding:18px 14px;

        padding-top:64px;
    }

    .topbar{
        padding:16px;

        border-radius:14px;

        align-items:flex-start;
    }

    .topbar h3{
        font-size:18px;

        line-height:1.4;
    }

    .topbar span{
        font-size:13px;
    }

    .card-box{
        padding:18px;

        border-radius:16px;
    }

    .event-title-row h5{
        font-size:17px;

        line-height:1.5;
    }

    .event-title-row small{
        display:block;

        margin-top:3px;

        font-size:13px;
    }

    .memo-upload-form{
        flex-direction:column;

        align-items:stretch;
    }

    .memo-upload-form input[type="file"]{
        width:100%;
    }

    .memo-upload-form button{
        width:100%;
    }

    .memo-file{
        align-items:flex-start;

        padding:11px;
    }

    .memo-file > span{
        font-size:13px;

        line-height:1.5;
    }

    .memo-file > div{
        flex-direction:column;
    }

    .question-item{
        align-items:flex-start;

        padding:11px;
    }

    .question-text{
        font-size:14px;
    }

    .question-buttons{
        flex-direction:column;
    }

    .question-edit-form{
        flex-direction:column;

        align-items:stretch;
    }

    .question-edit-form input{
        width:100%;
    }

    .question-edit-buttons{
        width:100%;
    }

    .question-edit-buttons .btn{
        flex:1;

        width:auto;
    }

    .survey-form{
        flex-direction:column;
    }

    .survey-form input{
        width:100%;
    }

    .survey-form button{
        width:100%;
    }

}

@media(max-width:480px){

    .main{
        padding-left:10px;

        padding-right:10px;
    }

    .card-box{
        padding:15px;

        border-radius:14px;
    }

    .search-box{
        padding:15px;
    }

    .memo-file{
        padding:10px;
    }

    .memo-file > span{
        font-size:12px;
    }

    .question-item{
        padding:10px;
    }

    .question-text{
        font-size:13px;
    }

}

</style>

</head>


<body>


<?php include("admin_menu.php"); ?>


<div class="main">


<div class="topbar">

    <h3>

        <i class="fa fa-file-shield"></i>

        Exemption Memo & Survey

    </h3>


    <span>

        Welcome,

        <b>

            <?php
            echo htmlspecialchars(
                $_SESSION['admin_name']
            );
            ?>

        </b>

    </span>

</div>


<?= $message ?>


<p class="page-description">

    Upload the exemption memo and set up the survey questions
    for each event.

    Students must complete the survey before they can access
    the exemption memo.

</p>


<div class="search-box">

    <label
        class="fw-semibold mb-2"
        style="color:#800020;"
    >

        <i class="fa fa-search"></i>

        Search Event

    </label>


    <div class="search-wrapper">

        <i class="fa fa-search"></i>


        <input
            type="text"
            id="eventSearch"
            class="form-control"
            placeholder="Search event name..."
            autocomplete="off"
        >


        <button
            type="button"
            id="clearSearch"
            class="clear-search"
            onclick="clearEventSearch()"
        >

            <i class="fa fa-xmark"></i>

        </button>

    </div>

</div>


<div id="eventList">


<?php

if(
    $events_result &&
    mysqli_num_rows($events_result) > 0
)
{

    while(
        $event =
        mysqli_fetch_assoc($events_result)
    )
    {

        $eid =
            $event['event_id'];


        // =====================================================
        // GET MEMO
        // =====================================================

        $memo_result =
            mysqli_query(
                $conn,
                "SELECT *
                 FROM exemption_memos
                 WHERE event_id='$eid'"
            );


        $memo =
            (
                $memo_result &&
                mysqli_num_rows($memo_result) > 0
            )
            ?
            mysqli_fetch_assoc($memo_result)
            :
            null;


        // =====================================================
        // GET SURVEY QUESTIONS
        // =====================================================

        $questions_result =
            mysqli_query(
                $conn,
                "SELECT
                    question_id,
                    section,
                    question_number,
                    question_text
                 FROM survey_questions
                 ORDER BY question_number ASC"
            );

?>


<div
    class="card-box event-card"
    data-event-title="<?php
        echo htmlspecialchars(
            strtolower(
                $event['event_title']
            )
        );
    ?>"
>


<div class="event-title-row">

    <h5>

        <i class="fa fa-calendar-check"></i>

        <?php

        echo htmlspecialchars(
            $event['event_title']
        );

        ?>


        <small class="text-muted">

            -

            <?php

            if(!empty($event['event_date']))
            {

                echo date(
                    "d M Y",
                    strtotime(
                        $event['event_date']
                    )
                );

            }
            else
            {
                echo "-";
            }

            ?>

        </small>

    </h5>

</div>


<div class="row">


<div class="col-md-6 mb-3">

    <h6
        style="
            color:#800020;
            font-weight:600;
        "
    >

        <i class="fa fa-file-pdf"></i>

        Exemption Memo

    </h6>


<?php

if($memo)
{

?>


<div class="memo-file">


    <span>

        <i class="fa fa-paperclip"></i>


        <?php

        echo htmlspecialchars(
            $memo['memo_file']
        );

        ?>

    </span>


    <div>


        <a
            href="../uploads/memos/<?php
                echo htmlspecialchars(
                    $memo['memo_file']
                );
            ?>"
            target="_blank"
            class="btn btn-sm btn-maroon"
            title="View Memo"
        >

            <i class="fa fa-eye"></i>

        </a>


        <a
            href="exemption.php?delete_memo=<?php
                echo $memo['memo_id'];
            ?>"
            class="btn btn-sm btn-danger"
            title="Delete Memo"
            onclick="
                return confirm(
                    'Delete this memo?'
                )
            "
        >

            <i class="fa fa-trash"></i>

        </a>


    </div>

</div>


<?php

}
else
{

?>


<p class="text-muted small">

    No memo uploaded yet for this event.

</p>


<?php

}

?>


<form
    method="POST"
    enctype="multipart/form-data"
    class="memo-upload-form mt-2"
>


    <input
        type="hidden"
        name="event_id"
        value="<?php
            echo $eid;
        ?>"
    >


    <input
        type="file"
        name="memo_file"
        class="form-control form-control-sm"
        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
        required
    >


    <button
        type="submit"
        name="upload_memo"
        class="btn btn-sm btn-gold text-nowrap"
    >

        <i class="fa fa-upload"></i>

        Upload

    </button>


</form>


</div>


<div class="col-md-6 mb-3">


    <h6
        style="
            color:#800020;
            font-weight:600;
        "
    >

        <i class="fa fa-clipboard-check"></i>

        Survey Questions (Tick)

    </h6>


    <p class="text-muted small mb-2">

        A general satisfaction survey (Bahagian B & C) is
        already applied automatically to every event. You can
        add extra tick-confirmation questions below just for
        this event - students tick every box before their
        certificate and exemption memo are unlocked.

    </p>


<?php

if(
    $questions_result &&
    mysqli_num_rows($questions_result) > 0
)
{

    $current_admin_section = null;

    while(
        $q =
        mysqli_fetch_assoc(
            $questions_result
        )
    )
    {

        $q_section =
            trim(
                (string)$q['section']
            );


        if(
            $q_section != "" &&
            $q_section != $current_admin_section
        )
        {

            $current_admin_section =
                $q_section;

?>


<div
    class="small fw-bold mt-2 mb-1"
    style="color:#800020;"
>

    <?php

    echo htmlspecialchars(
        $current_admin_section
    );

    ?>

</div>


<?php

        }

?>


<div
    class="question-item"
    id="question-<?php
        echo $q['question_id'];
    ?>"
>


    <span class="question-text">

        <i class="fa fa-square-check text-muted"></i>

        &nbsp;

        <?php

        echo htmlspecialchars(
            $q['question_number']
        );

        ?>.


        <?php

        echo htmlspecialchars(
            $q['question_text']
        );

        ?>

    </span>


    <div class="question-buttons">


        <button
            type="button"
            class="btn btn-sm btn-edit"
            title="Edit Question"
            onclick="
                editQuestion(
                    <?php
                    echo $q['question_id'];
                    ?>
                )
            "
        >

            <i class="fa fa-pen"></i>

        </button>


        <a
            href="exemption.php?delete_question=<?php
                echo $q['question_id'];
            ?>"
            class="btn btn-sm btn-danger"
            title="Delete Question"
            onclick="
                return confirm(
                    'Delete this question?'
                )
            "
        >

            <i class="fa fa-trash"></i>

        </a>


    </div>


</div>


<?php

    }

}
else
{

?>


<p class="text-muted small">

    No survey questions yet.

    Students will be able to access the memo directly.

</p>


<?php

}

?>


<form
    method="POST"
    class="survey-form mt-2"
>


    <input
        type="text"
        name="question_text"
        class="form-control form-control-sm"
        placeholder="e.g. I attended the full event"
        required
    >


    <button
        type="submit"
        name="add_question"
        class="btn btn-sm btn-maroon text-nowrap"
    >

        <i class="fa fa-plus"></i>

        Add

    </button>


</form>


</div>


</div>


</div>


<?php

    }

}
else
{

?>


<div class="card-box text-center text-muted">

    <i class="fa fa-circle-info"></i>

    No events found.

    Please add an event first.

</div>


<?php

}

?>


</div>


<div
    id="noSearchResult"
    class="no-search-result"
>

    <i
        class="fa fa-magnifying-glass fa-2x mb-2"
    ></i>


    <p class="mb-0">

        No event found.

    </p>

</div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>

const eventSearch =
    document.getElementById(
        "eventSearch"
    );


const clearSearch =
    document.getElementById(
        "clearSearch"
    );


const eventCards =
    document.querySelectorAll(
        ".event-card"
    );


const noSearchResult =
    document.getElementById(
        "noSearchResult"
    );


eventSearch.addEventListener(
    "keyup",
    function()
    {

        const searchValue =
            this.value
                .toLowerCase()
                .trim();


        let found = false;


        if(searchValue !== "")
        {
            clearSearch.style.display =
                "block";
        }
        else
        {
            clearSearch.style.display =
                "none";
        }


        eventCards.forEach(
            function(card)
            {

                const eventTitle =
                    card.getAttribute(
                        "data-event-title"
                    );


                if(
                    eventTitle.includes(
                        searchValue
                    )
                )
                {

                    card.style.display =
                        "block";

                    found = true;

                }
                else
                {

                    card.style.display =
                        "none";

                }

            }
        );


        if(
            searchValue !== "" &&
            !found
        )
        {

            noSearchResult.style.display =
                "block";

        }
        else
        {

            noSearchResult.style.display =
                "none";

        }

    }
);


function clearEventSearch()
{

    eventSearch.value = "";


    clearSearch.style.display =
        "none";


    eventCards.forEach(
        function(card)
        {

            card.style.display =
                "block";

        }
    );


    noSearchResult.style.display =
        "none";


    eventSearch.focus();

}


function editQuestion(questionId)
{

    const questionBox =
        document.getElementById(
            "question-" +
            questionId
        );


    const questionText =
        questionBox
            .querySelector(
                ".question-text"
            )
            .innerText
            .trim();


    const cleanText =
        questionText
            .replace(
                /^\d+\.\s*/,
                ""
            );


    questionBox.innerHTML = `

        <form
            method="POST"
            class="question-edit-form"
        >

            <input
                type="hidden"
                name="question_id"
                value="${questionId}"
            >


            <input
                type="text"
                name="question_text"
                class="form-control form-control-sm"
                value="${escapeHtml(cleanText)}"
                required
                autofocus
            >


            <div class="question-edit-buttons">


                <button
                    type="submit"
                    name="edit_question"
                    class="btn btn-sm btn-maroon"
                    title="Save"
                >

                    <i class="fa fa-check"></i>

                </button>


                <button
                    type="button"
                    class="btn btn-sm btn-secondary"
                    title="Cancel"
                    onclick="location.reload()"
                >

                    <i class="fa fa-xmark"></i>

                </button>


            </div>

        </form>

    `;

}


function escapeHtml(text)
{

    const div =
        document.createElement(
            "div"
        );


    div.textContent =
        text;


    return div.innerHTML;

}

</script>


</body>

</html>