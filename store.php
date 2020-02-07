<?php
SESSION_START();

ob_start();

$page_name = $_GET['page'];
if (empty($page_name)) $page_name = 'server';

switch ($page_name) {
    case 'server':
    case 'packages':
    case 'customjob':
    case 'global':
    case 'store':
        $page = 'store';
        $page_title = 'Store';

        break;
    case 'credits':
        $page = 'credits';
        $page_title = 'Credits';

        break;
    case 'advent':
        $page = 'advent';
        $page_title = 'Advent calendar';

        break;
    case 'raffle':
        $page = 'raffle';
        $page_title = 'Raffles';

        break;
    case 'required':
        $depends = [
            'teamspeak'
        ];

        $page = 'required';
        $page_title = 'Action required!';

        break;
    case 'purchase':
        $depends = [
            'paymentwall'
        ];

        $page = 'purchase';
        $page_title = 'Choose payment gateway';

        break;
    case 'dedipass':

        $page = 'dedipass';
        $page_title = 'Dedipass';

        break;
    default:
        die('Invalid page!');

        break;
}

require_once('inc/functions.php');

$message = new FlashMessages();

if (!prometheus::loggedin()) {
    include('inc/login.php');
} else {
    $UID = $_SESSION['uid'];
}

// DENY ACCESS IF INVALID
if (isset($_GET['credits']) && !gateways::enabled('credits')) {
    die('Invalid request. Credits are not enabled as a payment gateway!');
}

if (getSetting('maintenance', 'value2') == 1 && !prometheus::isAdmin()) {
    die('Maintenance mode is active. Please check back at a later time!');
}

if (prometheus::loggedin() && isBlacklisted($_SESSION['uid'])) {
    die(lang('blacklisted_you', 'You are blacklisted from purchasing any package on this community'));
}

if (store::countServers() == 1 && isset($_GET['page']) && $_GET['page'] == 'server') {
    util::redirect('store.php?page=packages&id=' . store::getOnlyServerID());
}

if (store::countServers() != 1 && store::countServers() != 0 && store::countGlobals() == store::countPackages() && getSetting('enable_globalPackages', 'value2') == 1 && isset($_GET['page']) && $_GET['page'] == 'server') {
    util::redirect('store.php?page=global');
}

if (isset($_POST['cuid_submit'])) {
    if(!csrf_check())
        return util::error("Invalid CSRF token!");

    $GET = $_GET;
    $GET['uid'] = null;
    $url = http_build_query($GET);
    $url = str_replace('%', '%%', $url);

    if (is_numeric($_POST['cuid']) && strlen($_POST['cuid']) == 17 && steam_userExists($_POST['cuid'])) {
        $cuid = $_POST['cuid'];

        util::redirect('store.php?' . $url . '&uid=' . $cuid);
    } elseif (strpos($_POST['cuid'], 'STEAM_0:') !== FALSE) {
        if (is_numeric(convertSteamIdToCommunityId($_POST['cuid'])) && strlen(convertSteamIdToCommunityId($_POST['cuid'])) == 17 && steam_userExists(convertSteamIdToCommunityId($_POST['cuid']))) {
            $cuid = convertSteamIdToCommunityId($_POST['cuid']);

            util::redirect('store.php?' . $url . '&uid=' . $cuid);
        }
    } else {
        $message->Add('danger', 'This is not a valid SteamID.');
    }
}

if (isset($_POST['coupon_submit'])) {
    if(!csrf_check())
        return util::error("Invalid CSRF token!");

    $GET = $_GET;
    $GET['coupon'] = null;
    $url = http_build_query($GET);
    $url = str_replace('%', '%%', $url);

    if(coupon::isValid($_POST['coupon'], $_GET['pid'])){
        $coupon = $_POST['coupon'];

        util::redirect('store.php?'. $url . '&coupon=' . $coupon);
    } else {
        $message->Add('danger', 'This is not a valid coupon code.');
    }
}

if (!prometheus::loggedin()) {
    include('inc/login.php');
}

if (prometheus::loggedin()) {
    $UID = $_SESSION['uid'];
}

// new stripe logic
if (isset($_GET['gateway']) && $_GET['gateway'] === 'stripe') {
    $session = stripe::checkout($_GET['type'], $_GET['pid'], $_GET['uid'], isset($_GET['price']) ? $_GET['price'] : null);
    $json = json_decode($session, true);

    if ($json && isset($json['id'])) {
      die($json['id']);
    }

    // otherwise fatal error

    die();
}

if (isset($_POST['tos_submit'])) {
    if(!csrf_check())
        return util::error("Invalid CSRF token!");
    
    tos::agree();
}

if (isset($_POST['customprice_submit'])) {
    $error = false;

    if (!is_numeric($_POST['amount'])) {
        $error = true;

        $message->add('danger', 'You need to enter a numeric price!');
    }

    if ($_POST['amount'] == '') {
        $error = true;

        $message->add('danger', 'You need to enter a custom price!');
    }

    if (!$error) {
        util::redirect('store.php?page=purchase&type=pkg&pid=' . $_POST['pid'] . '&price=' . $_POST['amount']);
    }
}

if (prometheus::loggedin() && !actions::delivered() && $page != 'required') {
    util::redirect('store.php?page=required');
}

if (prometheus::loggedin() && is_numeric(actions::delivered('customjob', $_SESSION['uid'])) && $_GET['page'] != 'customjob') {
    util::redirect('store.php?page=customjob&pid=' . actions::delivered('customjob', $_SESSION['uid']));
}

ob_end_clean();
?>

<?php include('inc/header.php'); ?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <?php
                    if (isset($_GET['page'])) include('pages/store/' . $_GET['page'] . '.php');
                    else include('pages/store/server.php');
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('inc/footer.php'); ?>
