<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';


/* =========================================================
   CREATE MAILER
   ========================================================= */

function createMailer()
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    $mail->Username   = 'emsjpp26@gmail.com';

    $mail->Password   = 'roio kyaq cckz yume';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';

    $mail->SMTPDebug  = 0;

    $mail->setFrom(
        'emsjpp26@gmail.com',
        'JPP EventHub'
    );

    return $mail;
}


/* =========================================================
   1. WELCOME EMAIL
   Sent when user creates an account
   ========================================================= */

function sendWelcomeEmail($to, $name)
{
    try
    {
        $mail = createMailer();

        $mail->addAddress($to, $name);

        $mail->isHTML(true);

        $mail->Subject = 'Welcome to JPP EventHub';

        $mail->Body = '
        <div style="
            font-family:Arial, Helvetica, sans-serif;
            background:#f5f5f5;
            padding:30px;
        ">

            <div style="
                max-width:600px;
                margin:auto;
                background:#ffffff;
                border-radius:12px;
                overflow:hidden;
                box-shadow:0 3px 10px rgba(0,0,0,0.10);
            ">

                <div style="
                    background:#800020;
                    padding:25px;
                    text-align:center;
                ">

                    <div style="
                        color:#D4AF37;
                        font-size:25px;
                        font-weight:bold;
                    ">
                        JPP EventHub
                    </div>

                    <div style="
                        color:#ffffff;
                        font-size:13px;
                        margin-top:5px;
                    ">
                        Event Management System
                    </div>

                </div>


                <div style="padding:30px;">

                    <h2 style="
                        color:#800020;
                        margin-top:0;
                    ">
                        Welcome!
                    </h2>

                    <p>
                        Hello <strong>' . htmlspecialchars($name) . '</strong>,
                    </p>

                    <p>
                        Welcome to <strong>JPP EventHub</strong>.
                        Your account has been successfully created.
                    </p>

                    <p>
                        You can now log in to the system to view
                        and register for available JPP events.
                    </p>

                    <div style="
                        background:#f8e9ee;
                        padding:15px;
                        border-left:5px solid #800020;
                        margin:20px 0;
                        line-height:1.6;
                    ">

                        <strong style="color:#800020;">
                            Important
                        </strong><br>

                        Please keep your account information
                        secure and do not share your password
                        with others.

                    </div>

                    <p>
                        Thank you for using JPP EventHub.
                    </p>

                    <p>
                        Regards,<br>
                        <strong style="color:#800020;">
                            JPP EventHub
                        </strong><br>
                        JPP Event Management System<br>
                        Politeknik Ungku Omar
                    </p>

                </div>


                <div style="
                    height:3px;
                    background:#D4AF37;
                "></div>


                <div style="
                    background:#800020;
                    padding:18px;
                    text-align:center;
                    color:#ffffff;
                    font-size:12px;
                ">

                    JPP EventHub | Politeknik Ungku Omar

                </div>

            </div>

        </div>
        ';

        $mail->AltBody =
            "Hello " . $name . ",\n\n" .
            "Welcome to JPP EventHub.\n" .
            "Your account has been successfully created.\n\n" .
            "You can now log in and register for available JPP events.\n\n" .
            "Regards,\n" .
            "JPP EventHub\n" .
            "Politeknik Ungku Omar";

        return $mail->send();
    }
    catch (Exception $e)
    {
        error_log("Welcome Email Error: " . $e->getMessage());
        return false;
    }
}


/* =========================================================
   2. EVENT REGISTRATION CONFIRMATION
   Sent after user registers for an event
   ========================================================= */

function sendRegistrationEmail(
    $to,
    $name,
    $eventTitle,
    $eventDate,
    $startTime,
    $endTime,
    $venue,
    $qrCode
)
{
    try
    {
        $mail = createMailer();

        $mail->addAddress($to, $name);

        $mail->isHTML(true);

        $mail->Subject = 'Registration Confirmed - ' . $eventTitle;

        $formattedDate = date(
            'd F Y',
            strtotime($eventDate)
        );

        $formattedStartTime = date(
            'h:i A',
            strtotime($startTime)
        );

        $formattedEndTime = date(
            'h:i A',
            strtotime($endTime)
        );

        $mail->Body = '
        <div style="
            font-family:Arial, Helvetica, sans-serif;
            background:#f5f5f5;
            padding:30px;
        ">

            <div style="
                max-width:600px;
                margin:auto;
                background:#ffffff;
                border-radius:12px;
                overflow:hidden;
                box-shadow:0 3px 10px rgba(0,0,0,0.10);
            ">

                <div style="
                    background:#800020;
                    padding:25px;
                    text-align:center;
                ">

                    <div style="
                        color:#D4AF37;
                        font-size:25px;
                        font-weight:bold;
                    ">
                        JPP EventHub
                    </div>

                    <div style="
                        color:#ffffff;
                        font-size:13px;
                        margin-top:5px;
                    ">
                        Event Management System
                    </div>

                </div>


                <div style="padding:30px;">

                    <h2 style="
                        color:#800020;
                        margin-top:0;
                    ">
                        Registration Confirmed
                    </h2>

                    <p>
                        Hello <strong>' . htmlspecialchars($name) . '</strong>,
                    </p>

                    <p>
                        Your registration for the following event
                        has been successfully confirmed.
                    </p>


                    <div style="
                        background:#f8e9ee;
                        padding:20px;
                        border-left:5px solid #800020;
                        border-radius:8px;
                        margin:20px 0;
                    ">

                        <h3 style="
                            color:#800020;
                            margin-top:0;
                        ">
                            ' . htmlspecialchars($eventTitle) . '
                        </h3>

                        <p>
                            <strong>Date:</strong>
                            ' . $formattedDate . '
                        </p>

                        <p>
                            <strong>Time:</strong>
                            ' . $formattedStartTime . '
                            -
                            ' . $formattedEndTime . '
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            ' . htmlspecialchars($venue) . '
                        </p>

                    </div>


                    <h3 style="color:#800020;">
                        Attendance QR Code
                    </h3>

                    <p>
                        Please keep your QR code safe.
                        It will be used for attendance scanning
                        during the event.
                    </p>


                    <div style="
                        background:#f5f5f5;
                        padding:18px;
                        text-align:center;
                        border:1px solid #dddddd;
                        border-radius:8px;
                        font-size:20px;
                        font-weight:bold;
                        letter-spacing:2px;
                    ">
                        ' . htmlspecialchars($qrCode) . '
                    </div>


                    <p style="
                        margin-top:25px;
                        line-height:1.6;
                    ">
                        Please arrive at the venue according to
                        the event schedule.
                    </p>


                    <p>
                        Regards,<br>
                        <strong style="color:#800020;">
                            JPP EventHub
                        </strong><br>
                        JPP Event Management System<br>
                        Politeknik Ungku Omar
                    </p>

                </div>


                <div style="
                    height:3px;
                    background:#D4AF37;
                "></div>


                <div style="
                    background:#800020;
                    padding:18px;
                    text-align:center;
                    color:#ffffff;
                    font-size:12px;
                ">

                    JPP EventHub | Politeknik Ungku Omar

                </div>

            </div>

        </div>
        ';

        $mail->AltBody =
            "Hello " . $name . ",\n\n" .
            "Your registration for " . $eventTitle .
            " has been successfully confirmed.\n\n" .
            "Date: " . $formattedDate . "\n" .
            "Time: " . $formattedStartTime . " - " .
            $formattedEndTime . "\n" .
            "Venue: " . $venue . "\n\n" .
            "QR Code: " . $qrCode . "\n\n" .
            "Please arrive according to the event schedule.\n\n" .
            "Regards,\n" .
            "JPP EventHub\n" .
            "Politeknik Ungku Omar";

        return $mail->send();
    }
    catch (Exception $e)
    {
        error_log("Registration Email Error: " . $e->getMessage());
        return false;
    }
}


/* =========================================================
   3. EVENT REMINDER EMAIL
   Sent 24 hours or 1 hour before the event
   ========================================================= */

function sendReminderEmail(
    $to,
    $name,
    $eventTitle,
    $eventDate,
    $startTime,
    $endTime,
    $venue,
    $reminderType = '24_hours'
)
{
    try
    {
        $mail = createMailer();

        $mail->addAddress($to, $name);

        $mail->isHTML(true);

        $formattedDate = date(
            'd F Y',
            strtotime($eventDate)
        );

        $formattedStartTime = date(
            'h:i A',
            strtotime($startTime)
        );

        $formattedEndTime = date(
            'h:i A',
            strtotime($endTime)
        );


        if ($reminderType === '1_hour')
        {
            $mail->Subject =
                'Reminder: ' . $eventTitle . ' Starts in 1 Hour';

            $reminderMessage = '
                This is a reminder that you are registered
                for the following event, which will
                <strong>start in 1 hour</strong>.
            ';

            $reminderTitle = 'Event starts in 1 hour';
        }
        else
        {
            $mail->Subject =
                'Reminder: ' . $eventTitle . ' is Tomorrow';

            $reminderMessage = '
                This is a reminder that you are registered
                for the following event, which will take
                place <strong>tomorrow</strong>.
            ';

            $reminderTitle = 'Event is tomorrow';
        }


        $mail->Body = '
        <div style="
            font-family:Arial, Helvetica, sans-serif;
            background:#f5f5f5;
            padding:30px;
        ">

            <div style="
                max-width:600px;
                margin:auto;
                background:#ffffff;
                border-radius:12px;
                overflow:hidden;
                box-shadow:0 3px 10px rgba(0,0,0,0.10);
            ">

                <div style="
                    background:#800020;
                    padding:25px;
                    text-align:center;
                ">

                    <div style="
                        color:#D4AF37;
                        font-size:25px;
                        font-weight:bold;
                    ">
                        JPP EventHub
                    </div>

                    <div style="
                        color:#ffffff;
                        font-size:13px;
                        margin-top:5px;
                    ">
                        Event Management System
                    </div>

                </div>


                <div style="padding:30px;">

                    <h2 style="
                        color:#800020;
                        margin-top:0;
                    ">
                        Event Reminder
                    </h2>

                    <p>
                        Hello <strong>' . htmlspecialchars($name) . '</strong>,
                    </p>

                    <p style="line-height:1.6;">
                        ' . $reminderMessage . '
                    </p>


                    <div style="
                        background:#f8e9ee;
                        padding:20px;
                        border-left:5px solid #800020;
                        border-radius:8px;
                        margin:20px 0;
                    ">

                        <h3 style="
                            color:#800020;
                            margin-top:0;
                        ">
                            ' . htmlspecialchars($eventTitle) . '
                        </h3>

                        <p>
                            <strong>Date:</strong>
                            ' . $formattedDate . '
                        </p>

                        <p>
                            <strong>Time:</strong>
                            ' . $formattedStartTime . '
                            -
                            ' . $formattedEndTime . '
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            ' . htmlspecialchars($venue) . '
                        </p>

                    </div>


                    <div style="
                        background:#fff8e1;
                        padding:16px;
                        border-left:5px solid #D4AF37;
                        border-radius:5px;
                        margin:20px 0;
                        line-height:1.6;
                    ">

                        <strong style="color:#800020;">
                            ' . $reminderTitle . '
                        </strong><br>

                        Please make sure to attend the event
                        according to the date, time and venue above.

                    </div>


                    <p style="line-height:1.6;">
                        Please have your attendance QR code
                        ready for scanning during the event.
                    </p>


                    <p style="line-height:1.6;">
                        We look forward to seeing you at the event!
                    </p>


                    <p>
                        Regards,<br>
                        <strong style="color:#800020;">
                            JPP EventHub
                        </strong><br>
                        JPP Event Management System<br>
                        Politeknik Ungku Omar
                    </p>

                </div>


                <div style="
                    height:3px;
                    background:#D4AF37;
                "></div>


                <div style="
                    background:#800020;
                    padding:18px;
                    text-align:center;
                    color:#ffffff;
                    font-size:12px;
                    line-height:1.5;
                ">

                    JPP EventHub<br>
                    Politeknik Ungku Omar<br><br>

                    This is an automated reminder.
                    Please do not reply to this email.

                </div>

            </div>

        </div>
        ';


        if ($reminderType === '1_hour')
        {
            $mail->AltBody =
                "Hello " . $name . ",\n\n" .
                "This is a reminder that you are registered for " .
                $eventTitle . ", which will start in 1 hour.\n\n" .
                "Date: " . $formattedDate . "\n" .
                "Time: " . $formattedStartTime . " - " .
                $formattedEndTime . "\n" .
                "Venue: " . $venue . "\n\n" .
                "Please have your attendance QR code ready for scanning.\n\n" .
                "We look forward to seeing you at the event!\n\n" .
                "Regards,\n" .
                "JPP EventHub\n" .
                "Politeknik Ungku Omar";
        }
        else
        {
            $mail->AltBody =
                "Hello " . $name . ",\n\n" .
                "This is a reminder that you are registered for " .
                $eventTitle . ", which will take place tomorrow.\n\n" .
                "Date: " . $formattedDate . "\n" .
                "Time: " . $formattedStartTime . " - " .
                $formattedEndTime . "\n" .
                "Venue: " . $venue . "\n\n" .
                "Please have your attendance QR code ready for scanning.\n\n" .
                "We look forward to seeing you at the event!\n\n" .
                "Regards,\n" .
                "JPP EventHub\n" .
                "Politeknik Ungku Omar";
        }


        return $mail->send();
    }
    catch (Exception $e)
    {
        error_log("Reminder Email Error: " . $e->getMessage());
        return false;
    }
}

?>