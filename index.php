<?php
    session_start();

    // checking if user is logged in
    if (!(isset($_SESSION['logged_in']) && (int) $_SESSION['logged_in'] === 1))
        header('Location: /iv-time4vps-order-form/pages/user/login');

    if (isset($_SESSION)) {
        var_dump($_SESSION);
        exit();
    } // FIXME: testing
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>IV Interview Task</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="flex flex-col min-h-screen bg-[#dfe4ea]">
        <header></header>
        <main class="flex-grow">
            Hello World!
        </main>
        <footer></footer>
    </body>
</html>
