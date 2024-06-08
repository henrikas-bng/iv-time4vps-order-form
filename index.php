<?php
    session_start();
    require_once(realpath(dirname(__FILE__)) . '/classes/api.php');
    require_once(realpath(dirname(__FILE__)) . '/classes/order.php');

    // checking if user is logged in
    if (!(isset($_SESSION['logged_in']) && (int) $_SESSION['logged_in'] === 1))
        header('Location: /iv-time4vps-order-form/pages/user/login');

    $order_placed = -1;
    
    // place an order
    if (isset($_POST['place_order_submit']) && (int) $_POST['place_order_submit'] === 1) {
        $product_id = $_POST['place_order_product'];
        $cycle = $_POST['place_order_billing'];
        $pay_method = $_POST['place_order_payment'];

        if (is_numeric($product_id) && ctype_alpha($cycle) && strlen($cycle) == 1 && is_numeric($pay_method)) {
            $post_data = [
                'product_id' => $product_id,
                'cycle' => $cycle,
                'pay_method' => $pay_method,
            ];

            $api_response = Api::call('/order/' . $product_id, 'post', $post_data);

            if ($api_response) {
                $temp_data = json_decode($api_response);

                $service_name = (isset($_POST['place_order_service_name'])) 
                    ? $_POST['place_order_service_name'] 
                    : '';

                $new_order = new Order();
                $new_order->user_id = (int) $_SESSION['user_id'];
                $new_order->order_number = $temp_data->order_num;
                $new_order->order_id = (int) $temp_data->items[0]->id;
                $new_order->invoice_id = (int) $temp_data->invoice_id;
                $new_order->product_id = (int) $product_id;
                $new_order->service_type = $temp_data->items[0]->type;
                $new_order->service_name = $service_name;
                $new_order->total_price = (float) $temp_data->total;
                $new_order->save();

                $order_placed = 1;
                unset($temp_data); // won't need this from here
            } else {
                $order_placed = 0;
            }
        } else {
            $order_placed = 0;
        }
    }
    
    $data = false;
    $step = 0;
    
    if (isset($_GET['product'])) {        
        if (is_numeric($_GET['product'])) {
            $api_response_product = Api::call('/order/' . $_GET['product']);
            $api_response_payments = Api::call('/payment');

            if ($api_response_product && $api_response_payments) {
                $temp_data = json_decode($api_response_product);
                $temp_payments = json_decode($api_response_payments, true);

                if (isset($temp_data->product) && isset($temp_payments['payments'])) {
                    if (isset($_GET['billing']) && isset($_GET['payment']) && ctype_alpha($_GET['billing']) && strlen($_GET['billing']) == 1 && is_numeric($_GET['payment'])) {
                        // Step 3: order overview
                        $data = $temp_data;
                        $payment = $temp_payments['payments'][$_GET['payment']];
                        $pricing = null;

                        foreach ($data->product->config->product[0]->items as $item) {
                            if ($item->value == $_GET['billing']) {
                                $pricing = $item->formatted;
                                break;
                            }
                        }
                        
                        $step = 3;
                    } else {
                        // Step 2: choose billing type and payment method for the chosen VPS plan
                        $data = $temp_data;
                        $product_prices = $data->product->config->product[0]->items;
                        $payment_methods = $temp_payments['payments'];
                        $step = 2;
                    }

                    // won't need this data anymore
                    unset($temp_data);
                    unset($temp_payments);
                }
            }
        }
    } else {
        // Step 1: display all available VPS plans
        $api_response = Api::call('/category/available/vps');

        if ($api_response) {
            $data = json_decode($api_response);
            $step = 1;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?php
                switch ($step) {
                    case 1:
                        echo 'Available VPS Plans - ';
                        break;
                    case 2:
                        echo 'Billing & Payment - ';
                        break;
                    case 3:
                        echo 'Order Overview - ';
                        break;
                }
            ?>
            IV Interview Task
        </title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="resources/js/mobile_menu.js"></script>
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
                                    class="text-[#2f3542] font-medium hover:text-[#57606f] active:underline transition-all"
                                    >
                                    New order
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="/iv-time4vps-order-form/pages/order/history" 
                                    class="text-[#747d8c] font-medium hover:text-[#57606f] active:underline transition-all"
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
            <?php if (!$data) { ?>
                <div class="container flex justify-center items-center mx-auto p-4">
                    <div class="w-full max-w-[600px] text-center text-sm text-white bg-[#ff4757] rounded-lg px-4 py-2 mb-4">
                        An error occured!
                    </div>
                </div>
            <?php } ?>
            <?php if ($order_placed === 0) { ?>
                <div class="container flex justify-center items-center mx-auto p-4">
                    <div class="w-full max-w-[600px] text-center text-sm text-white bg-[#ff4757] rounded-lg px-4 py-2 mb-4">
                        Could not place your order. Try again later!
                    </div>
                </div>
            <?php } ?>
            <?php if ($order_placed === 1) { ?>
                <div class="container flex justify-center items-center mx-auto p-4">
                    <div class="w-full max-w-[600px] text-center text-sm text-white bg-[#2ed573] rounded-lg px-4 py-2 mb-4">
                        Order placed successfully!
                    </div>
                </div>
            <?php } ?>
            <?php if ($step === 1) { ?>
                <div class="container flex justify-center items-stretch flex-wrap mx-auto px-4">
                    <?php foreach ($data as $item) { ?>
                        <div class="flex flex-col w-full max-w-[400px] bg-[#f1f2f6] hover:bg-white rounded-md shadow-lg hover:shadow-xl transition-all p-2 m-4">
                            <h3 class="text-lg text-center text-[#2f3542] font-semibold mb-4">
                                Virtual private server
                                <br>
                                <?= $item->name ?>
                            </h3>
                            <div class="text-[#57606f] flex flex-col items-center">
                                <h5 class="text-center text-md font-medium mb-2">Server specifications</h5>
                                <?= $item->description ?>
                            </div>
                            <div class="flex justify-center items-center text-lg font-semibold bg-gradient-to-r from-[#5352ed] to-[#2f3542] text-transparent bg-clip-text my-6">
                                Nuo <?= $item->prices->m ?> &euro;
                            </div>
                            <a 
                                href="/iv-time4vps-order-form/?product=<?= $item->id ?>"
                                class="flex justify-center items-center w-full text-sm font-medium text-white bg-gradient-to-r from-[#70a1ff] active:to-[#70a1ff] to-[#5352ed] relative hover:-translate-y-2 hover:shadow-md active:shadow-lg rounded-md transition-all p-4"
                                >
                                Continue
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if ($step === 2 && isset($data->product)) { ?>
                <div class="container mx-auto px-4">
                    <form 
                        class="w-full max-w-[1060px] bg-white rounded-md shadow-lg mx-auto p-8"
                        action="/iv-time4vps-order-form/" 
                        method="GET"
                        >
                        <input type="hidden" name="product" value="<?= $data->product->id ?>">
                        <div class="flex flex-wrap divide-y md:divide-x md:divide-y-0">
                            <div class="flex flex-col justify-center items-center flex-grow mb-8 md:me-8 md:mb-0">
                                <div class="flex justify-center items-center mb-8">
                                    <h2 class="text-xl font-semibold text-center text-[#2f3542]">
                                        Virtual private server
                                        <br>
                                        <?= $data->product->category_name ?> - <?= $data->product->name ?>
                                    </h2>
                                </div>
                                <div class="flex flex-col justify-center items-center mb-8">
                                    <p class="font-medium text-[#57606f] mb-2">
                                        Choose billing cycle:
                                    </p>
                                    <select 
                                        class="px-3 py-2 text-[#2f3542] focus:outline-[#5352ed] ring-0 focus:ring-0 focus:border-[#a4b0be] outline-none border border-[#ced6e0] hover:outline-[#70a1ff]/40 transition-all w-full rounded-md"
                                        name="billing"
                                        >
                                        <?php if (isset($product_prices)) { ?>
                                            <?php foreach ($product_prices as $key => $pricing) { ?>
                                                <option 
                                                    value="<?= $pricing->value ?>"
                                                    <?= ($key == 0) ? 'selected' : '' ?>
                                                    >
                                                    <?= $pricing->formatted ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="flex flex-col justify-center items-center">
                                    <p class="font-medium text-[#57606f] mb-2">
                                        Choose payment method:
                                    </p>
                                    <select 
                                        class="px-3 py-2 text-[#2f3542] focus:outline-[#5352ed] ring-0 focus:ring-0 focus:border-[#a4b0be] outline-none border border-[#ced6e0] hover:outline-[#70a1ff]/40 transition-all w-full rounded-md"
                                        name="payment"
                                        >
                                        <?php if (isset($payment_methods)) { ?>
                                            <?php foreach ($payment_methods as $pm_id => $pm_name) { ?>
                                                <option 
                                                    value="<?= $pm_id ?>"
                                                    <?= (array_key_first($payment_methods) == $pm_id) ? 'selected' : '' ?>
                                                    >
                                                    <?= $pm_name ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-center items-center flex-grow text-[#2f3542] pt-8 md:pt-0">
                                <?= $data->product->description ?>
                            </div>
                        </div>
                        <hr class="my-8">
                        <div class="flex justify-between items-center flex-wrap">
                            <button 
                                class="min-w-[256px] max-w-[529px] flex justify-center items-center w-full text-sm font-medium text-white bg-gradient-to-r from-[#70a1ff] active:to-[#70a1ff] to-[#5352ed] relative hover:-translate-y-2 hover:shadow-md active:shadow-lg rounded-md transition-all p-4"
                                type="submit"
                                >
                                Continue
                            </button>
                            <div class="flex flex-grow justify-center md:justify-end items-center mt-2 md:mt-0">
                                <a 
                                    href="/iv-time4vps-order-form/"
                                    class="text-[#ff6b81] stroke-[#ff6b81] font-medium hover:text-[#ff4757] hover:stroke-[#ff4757] active:underline transition-all p-3"
                                    >
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
            <?php if ($step === 3 && isset($data->product) && isset($payment) && isset($pricing)) { ?>
                <div class="container mx-auto px-4">
                    <form 
                        class="w-full max-w-[1060px] bg-white rounded-md shadow-lg mx-auto p-8"
                        action="/iv-time4vps-order-form/" 
                        method="POST"
                        >
                        <input type="hidden" name="place_order_product" value="<?= $data->product->id ?>">
                        <input type="hidden" name="place_order_billing" value="<?= $_GET['billing'] ?>">
                        <input type="hidden" name="place_order_payment" value="<?= $_GET['payment'] ?>">
                        <input type="hidden" name="place_order_service_name" value="<?= $data->product->name ?>">
                        <div class="flex flex-wrap divide-y md:divide-x md:divide-y-0">
                            <div class="flex flex-col justify-center items-center flex-grow mb-8 md:me-8 md:mb-0">
                                <div class="flex justify-center items-center mb-8">
                                    <h2 class="text-xl font-semibold text-center text-[#2f3542]">
                                        Virtual private server
                                        <br>
                                        <?= $data->product->category_name ?> - <?= $data->product->name ?>
                                    </h2>
                                </div>
                                <div class="flex flex-col justify-center items-center mb-8">
                                    <p class="font-medium text-[#57606f] mb-2">
                                        <b>Billing cycle:</b> <?= $pricing ?>
                                    </p>
                                </div>
                                <div class="flex flex-col justify-center items-center">
                                    <p class="font-medium text-[#57606f] mb-2">
                                        <b>Payment method:</b> <?= $payment ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-center items-center flex-grow text-[#2f3542] pt-8 md:pt-0">
                                <?= $data->product->description ?>
                            </div>
                        </div>
                        <hr class="my-8">
                        <div class="flex justify-between items-center flex-wrap">
                            <button 
                                class="min-w-[256px] max-w-[529px] flex justify-center items-center w-full text-sm font-medium text-white bg-gradient-to-r from-[#70a1ff] active:to-[#70a1ff] to-[#5352ed] relative hover:-translate-y-2 hover:shadow-md active:shadow-lg rounded-md transition-all p-4"
                                type="submit"
                                name="place_order_submit"
                                value="1"
                                >
                                Place order
                            </button>
                            <div class="flex flex-grow justify-center md:justify-end items-center mt-2 md:mt-0">
                                <a 
                                    href="/iv-time4vps-order-form/"
                                    class="text-[#ff6b81] stroke-[#ff6b81] font-medium hover:text-[#ff4757] hover:stroke-[#ff4757] active:underline transition-all p-3"
                                    >
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
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
