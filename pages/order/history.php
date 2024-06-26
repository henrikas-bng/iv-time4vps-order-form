<?php
    session_start();
    require_once(realpath(dirname(__FILE__)) . '/../../classes/order.php');

    // checking if user is logged in
    if (!(isset($_SESSION['logged_in']) && (int) $_SESSION['logged_in'] === 1))
        header('Location: /iv-time4vps-order-form/pages/user/login');

    $orders = false;

    // getting user orders from database
    if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];
        $orders = Order::getAllByUserId($user_id);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            Order History - IV Interview Task
        </title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="/iv-time4vps-order-form/resources/js/mobile_menu.js"></script>
    </head>
    <body class="flex flex-col min-h-screen bg-[#dfe4ea]">
        <div
            id="mobile-menu" 
            class="hidden absolute w-full h-full top-0 left-0 flex flex-col bg-[#2f3542]/60 backdrop-blur z-50"
            >
            <div class="flex justify-between items-center p-4">
                <span class="text-xl font-semibold text-[#dfe4ea]">
                    Menu
                </span>
                <button
                    class="flex items-center text-[#dfe4ea] font-medium hover:text-white active:text-[#70a1ff] transition-all"
                    onclick="toggleMobileMenu()"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 p-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-col flex-grow py-8 overflow-y-auto">
                <a 
                    href="/iv-time4vps-order-form/"
                    class="flex justify-center items-center text-xl font-medium text-[#ced6e0] hover:bg-white/10 p-4 active:text-[#70a1ff]"
                    >
                    New order
                </a>
                <a 
                    href="/iv-time4vps-order-form/pages/order/history"
                    class="flex justify-center items-center text-xl font-medium text-[#ced6e0] hover:bg-white/10 p-4 active:text-[#70a1ff]"
                    >
                    Order history
                </a>
            </div>
            <div class="flex justify-center items-center p-4">                
                <a 
                    href="/iv-time4vps-order-form/pages/user/logout" 
                    class="flex items-center text-[#ff6b81] stroke-[#ff6b81] font-medium hover:text-[#ff4757] hover:stroke-[#ff4757] active:underline transition-all"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    Sign out
                </a>
            </div>
        </div>
        <header>
            <nav class="bg-[#ced6e0] py-4">
                <div class="container flex items-center mx-auto px-4">
                    <div class="flex items-center me-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-[#5352ed] size-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                        </svg>
                        <span class="text-lg font-semibold bg-gradient-to-r from-[#5352ed] to-[#2f3542] text-transparent bg-clip-text ms-2">
                            IV Task
                        </span>
                    </div>
                    <div class="flex md:hidden justify-end items-center flex-grow">
                        <button
                            class="flex items-center text-[#747d8c] stroke-[#747d8c] font-medium hover:text-[#57606f] hover:stroke-[#57606f] active:underline transition-all"
                            onclick="toggleMobileMenu()"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            Menu
                        </button>
                    </div>
                    <div class="hidden md:flex justify-between items-center flex-grow">
                        <ul class="flex justify-center items-center">
                            <li class="me-4">
                                <a 
                                    href="/iv-time4vps-order-form/" 
                                    class="text-[#747d8c] font-medium hover:text-[#57606f] active:underline transition-all"
                                    >
                                    New order
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="/iv-time4vps-order-form/pages/order/history" 
                                    class="text-[#2f3542] font-medium hover:text-[#57606f] active:underline transition-all"
                                    >
                                    Order history
                                </a>
                            </li>
                        </ul>
                        <a 
                            href="/iv-time4vps-order-form/pages/user/logout" 
                            class="flex items-center text-[#ff6b81] stroke-[#ff6b81] font-medium hover:text-[#ff4757] hover:stroke-[#ff4757] active:underline transition-all"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            Sign out
                        </a>
                    </div>
                </div>
            </nav>
        </header>
        <main class="flex-grow py-10">
            <?php if ($orders === false) { ?>
                <div class="container flex justify-center items-center mx-auto p-4">
                    <div class="w-full max-w-[600px] text-center text-sm text-white bg-[#ff4757] rounded-lg px-4 py-2 mb-4">
                        An error occured!
                    </div>
                </div>
            <?php } ?>
            <?php if (is_array($orders) && empty($orders)) { ?>
                <div class="container flex justify-center items-center mx-auto p-4">
                    <div class="w-full max-w-[600px] text-center text-sm text-white bg-[#57606f] rounded-lg px-4 py-2 mb-4">
                        You have no orders yet.
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($orders)) { ?>
                <div class="container mx-auto px-4">
                    <div class="flex flex-col w-full max-w-[1060px] bg-white rounded-md shadow-lg mx-auto p-8 overflow-x-auto">
                        <h2 class="text-2xl font-bold text-[#2f3542] mb-10">
                            Your order history
                        </h2>
                        <table class="table-auto border-collapse flex-grow">
                            <thead>
                                <tr class="border-b-2">
                                    <th class="text-start text-nowrap p-4">Order No.</th>
                                    <th class="text-start text-nowrap p-4">Service Type</th>
                                    <th class="text-start text-nowrap p-4">Service Name</th>
                                    <th class="text-start text-nowrap p-4">Total Price, Eur</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) { ?>
                                    <tr 
                                        id="<?= $order['order_id'] ?>"
                                        class="hover:bg-[#dfe4ea]/25 transition-all"
                                        >
                                        <td class="text-start p-4"><?= $order['order_number'] ?></td>
                                        <td class="text-start p-4"><?= $order['service_type'] ?></td>
                                        <td class="text-start p-4"><?= $order['service_name'] ?></td>
                                        <td class="text-start p-4"><?= $order['total_price'] ?></td>
                                        <td>
                                            <div class="flex justify-center items-center">
                                                <a 
                                                    href="/iv-time4vps-order-form/pages/order/details/?id=<?= $order['order_id'] ?>"
                                                    class="text-white text-sm font-medium bg-[#5352ed] outline-none hover:outline-[#70a1ff]/40 active:outline-[#70a1ff]/80 rounded-md py-1.5 px-2.5 transition-all"
                                                    >
                                                    Details
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </main>
        <footer class="bg-[#ced6e0] py-4">
            <div class="container mx-auto px-4">
                <p class="text-sm text-center text-[#747d8c]">
                    github.com/henrikas-bng
                </p>
            </div>
        </footer>
    </body>
</html>
