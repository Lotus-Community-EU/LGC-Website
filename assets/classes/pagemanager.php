<?php

Functions::ConnectDB();
Functions::GetAllSettings();

$page = isset($_GET['url']) ? $_GET['url'] : 'index';

$GET = explode('/', $page);

$page = $GET[0];
$_GET['url'] = $page;
switch($page) {
    case 'admin':
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			LoadHandler('admin/'.$GET[1]);
			break;
		}
		else {
            Functions::LoadUserdData(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : 'guest');
			switch($GET[1]) {
                case 'user': 
                    LoadAdminView($GET['1'],'admin_user_list');
                    break;
                case 'roles':
                    LoadAdminView($GET['1'],'admin_role_management');
                    break;
				default:
					LoadAdminView($GET[1]);
					break;
			}
			break;
		}

    default:
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			LoadHandler($page);
			break;
		}
		else {
			switch($page) {
                case 'logout':
                    LoadHandler('logout');
                    break;
				default:
                    Functions::LoadUserdData(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : 'guest');
                    if($page == 'login' && Functions::$user['id'] != 0) {
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
    global $GET;
	if(!file_exists('pages/homepage/'.$page.'/index.php')) {
        $page = 'index';
    }
    ?>
	<!DOCTYPE html>
	<html lang="en-US">
		<?php
            include('components/head.php');
            ?><body class="text-white"><div class="container" style="min-height: 500px;"><?php
            include('components/navbar.php');
            Functions::ShowErrorMessage();
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
    global $GET;
	if(!Functions::UserHasPermission($needed_permission)) {
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
    Functions::LoadUserdData(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : 'guest');
	global $GET;
    include('handler/'.$handler.'.php');
}