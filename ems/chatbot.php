/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userMessage = htmlspecialchars($_POST["message"]);
    $reply = "";

    if (stripos($userMessage, "hai") !== false) {
        $reply = "Haiii 💕 Saya ChatChibi! Nak tanya apa hari ni?";
    } elseif (stripos($userMessage, "nama") !== false) {
        $reply = "Nama saya ChatChibi 🧸, teman comel awak!";
    } else {
        $reply = "Hmm 🤔 saya cuba faham... boleh ulang semula dengan cara lain?";
    }

    echo json_encode(["reply" => $reply]);
    exit;
}
*/
