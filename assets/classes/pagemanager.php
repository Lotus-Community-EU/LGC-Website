<?php

try {
    Functions::ConnectDB();
} catch(Exception $e) {
    die('Website is currently under maintenance! We are working on getting it back online as soon as possible!<br>Sorry for the inconveniences!');
}

include('assets/classes/log.php');
include('assets/classes/user.php');
include('assets/classes/rank.php');
include('assets/classes/todo.php');
include('assets/classes/settings.php');
include('assets/classes/discordwebhook.php');
$settings = new Settings();

$page = isset($_GET['url']) ? $_GET['url'] : 'index';

$GET = explode('/', $page);

$page = $GET[0];
$_GET['url'] = $page;
switch($page) {
    case 'admin':
		if($_SERVER["REQUEST_METHOD"] == "POST") {
            $position = implode('/', $GET);
			LoadHandler($position);
			break;
		}
		else {
            $user = new User(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : (isset($_COOKIE['remember']) ? $_COOKIE['remember'] : 'guest'));
			switch($GET[1]) {
                case 'user': 
                    LoadAdminView($GET['1'],'admin_user_list');
                    break;
                case 'ranks':
                    LoadAdminView($GET['1'],'admin_rank_management');
                    break;
                case 'translation':
                    LoadAdminView($GET['1'],'admin_translation_list');
                    break;
                case 'website_settings':
                    LoadAdminView($GET['1'],'admin_website_settings');
                    break;
                case 'todo':
                    LoadAdminView($GET['1'],'admin_todo_access');
                    break;
                case 'changelog':
                    LoadAdminView($GET['1'],'admin_changelog_access');
                    break;
				default:
					LoadAdminView($GET[1]);
					break;
			}
			break;
		}

    default:
		if($_SERVER["REQUEST_METHOD"] == "POST") {
            $position = implode('/', $GET);
			LoadHandler($position);
			break;
		}
		else {
			switch($page) {
                case 'logout':
                    User::logout();
                    break;
				default:
                    $user = new User(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : (isset($_COOKIE['remember']) ? $_COOKIE['remember'] : 'guest'));
                    if($page == 'login' && $user->getID() != 0) {
                        header("Location: /");
                        exit;
                    }
					LoadView($page);
                    break;
			}
		}
}

function CheckCookies() {
    if(!isset($_COOKIES['cookie_consent'])) {
        ?>
        <link rel="stylesheet" href="/assets/css/cookieconsent.css">
        <div class="alert text-center cookiealert" role="alert">
            <b>Do you like cookies?</b> &#x1F36A; We use cookies to ensure you get the best experience on our website. <a href="https://cookiesandyou.com/" target="_blank">Learn more</a>

            <button type="button" class="btn btn-primary btn-sm acceptcookies">
                I understand
            </button>
        </div>
        <?php
    }
}

function LoadView($page = '', $page_title = 'Lotus Gaming Community') {
    global $GET, $user, $settings, $config;
	if(!file_exists('pages/homepage/'.$page.'/index.php')) {
        $_SESSION['error_title'] = 'Not existing';
        $_SESSION['error_message'] = 'The page you tried to access doesn\'t exist!';
        header("Location: /");
        exit;
    }
    ?>
	<!DOCTYPE html>
	<html lang="en-US">
		<?php
            include('components/head.php');
            ?><body class="text-white"><div class="container" style="min-height: 500px;"><?php
            include('components/navbar.php');
            Functions::ShowErrorMessage();
            Functions::ShowSuccessMessage();
            ?>
            <!--<div style="overflow-y: auto;">-->
            <?php include('pages/homepage/'.$page.'/index.php');?>
            <!--</div>-->
            <a href="" class="scroll_to_top"><i class="fa-solid fa-arrow-up"></i></a></div></body><?php
            include('components/footer.php');
            CheckCookies();
            include('components/javascript.php');
		?>
        </html>
    <?php
}

function LoadAdminView($page = '', $needed_permission = '', $page_title = 'Lotus Gaming Community') {
    global $GET, $user, $settings, $config;
	if(!$user->hasPermission($needed_permission)) {
        $_SESSION['error_title'] = 'No Permission';
        $_SESSION['error_message'] = 'You don\'t have the permissions to view that page!';
		header("Location: /");
		exit;
	}
    ?>
	<!DOCTYPE html>
	<html lang="en-US">
		<?php
            include('components/head.php');
            ?><body class="text-white"><div class="container" style="min-height: 500px;"><?php
            include('components/navbar.php');
            Functions::ShowSuccessMessage();
            Functions::ShowErrorMessage();
            include('pages/admin/'.$page.'/index.php');
            ?><div class="divider-50"></div><a href="" class="scroll_to_top"><i class="fa-solid fa-arrow-up"></i></a></div></body><?php
            include('components/footer.php');
            CheckCookies();
            include('components/javascript.php');
		?>
        </html>
    <?php
}

/*
    <script>
        $(".nav-check").removeClass("active");
        $("#<?= $view;?>").addClass("active");
    </script>
*/

function LoadHandler($handler = '') {
    $user = new User(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : (isset($_COOKIE['remember']) ? $_COOKIE['remember'] : 'guest'));
	global $GET, $settings, $config;
    include('handler/'.$handler.'.php');
}