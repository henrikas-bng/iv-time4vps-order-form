<?php
session_start();

if (isset($_SESSION['logged_in']) && (int) $_SESSION['logged_in'] === 1) {
    session_destroy();
}

header('Location: /iv-time4vps-order-form/');
exit();
