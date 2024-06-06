<?php
    session_start();
    require_once('../../classes/user.php');

    // checking if user is logged in (can't login if you're already logged in)
    if (isset($_SESSION['logged_in']) && (int) $_SESSION['logged_in'] === 1)
        header('Location: /iv-time4vps-order-form/');

    $user_auth_error = 0; // 0 - no error notification, 1 - show error notification

    // checking user credentials if the form was submitted and data is valid
    if ($_POST && isset($_POST['login_form_submit']) && (int) $_POST['login_form_submit'] === 1) {
        $email = $_POST['login_form_email'];
        $password = $_POST['login_form_password'];
        
        // somewhat of a validation for email
        $is_email_valid = (
            strlen($email) >= 5
            && strlen($email) <= 64
            && str_contains($email, '@')
            && str_contains($email, '.')
        );

        // also somewhat of a validation for password
        $is_password_valid = (
            strlen($password) >= 8
            && strlen($password) <= 32
        );

        // logging in user if validation was successful
        // setting SESSION variables
        // and redirecting user to main page
        if ($is_email_valid && $is_password_valid) {
            try {
                $user = User::authenticate($email, $password);
            } catch (Exception $_) {
                $user = null;
            }

            if ($user) {
                $_SESSION['logged_in'] = '1';
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_email'] = $user->email;
                header('Location: /iv-time4vps-order-form/');
                exit();
            } else {
                $user_auth_error = 1;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prisijungti - IV Interview Task</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="flex flex-col min-h-screen bg-[#dfe4ea]">
        <div class="flex justify-center items-center flex-grow container py-24 px-4">
            <div class="flex flex-col w-full max-w-[400px] bg-white rounded-lg shadow-xl py-10 px-8">
                <h2 class="text-xl font-bold text-center text-[#2f3542] mb-10">
                    <?php if ($user_auth_error) { ?>
                        <div class="text-center text-sm text-white bg-[#ff4757] rounded-lg px-4 py-2 mb-4">
                            Nepavyko prisijungti!
                        </div>
                    <?php } ?>
                    Prisijunkite
                </h2>
                <form action="/iv-time4vps-order-form/pages/user/login" method="POST">
                    <input 
                        class="px-3 py-2 mb-4 focus:outline-[#5352ed] ring-0 focus:ring-0 focus:border-[#a4b0be] outline-none border border-[#ced6e0] hover:outline-[#70a1ff]/40 transition-all w-full rounded-md" 
                        type="email" 
                        name="login_form_email" 
                        value="" 
                        placeholder="El. paštas" 
                        minlength="5"
                        maxlength="64"
                        required
                        >
                    <input 
                        class="px-3 py-2 mb-8 focus:outline-[#5352ed] ring-0 focus:ring-0 focus:border-[#a4b0be] outline-none border border-[#ced6e0] hover:outline-[#70a1ff]/40 transition-all w-full rounded-md" 
                        type="password" 
                        name="login_form_password" 
                        value="" 
                        placeholder="Slaptažodis"
                        minlength="8"
                        maxlength="32"
                        required
                        >
                    <button 
                        class="w-full outline-none outline-4 hover:outline-[#70a1ff]/40 active:outline-[#5352ed]/60 rounded-full bg-gradient-to-r from-[#70a1ff] to-[#5352ed] text-white transition-all py-4 px-4 font-medium" 
                        type="submit" 
                        name="login_form_submit" 
                        value="1"
                        >
                        Prisijungti
                    </button>
                </form>
                <div class="flex justify-center items-center mt-10">
                    <p class="text-[#57606f] me-4">
                        Dar neturite vartotojo?
                    </p>
                    <a 
                        href="/iv-time4vps-order-form/pages/user/register" 
                        class="text-[#5352ed] hover:text-[#70a1ff] active:underline transition-all font-medium"
                        >
                        Registruotis
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
