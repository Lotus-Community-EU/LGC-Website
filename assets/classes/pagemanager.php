<?php

Functions::ConnectDB();

Functions::LoadUserdData(isset($_SESSION['user_token']) ? $_SESSION['user_token'] : 'guest');

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
			switch($GET[1]) {
				case 'home':
					//LoadAdminView('home','Home','p_view_home');
                    echo 'Admin_Home';
					break;
				case 'logout':
                    echo 'Admin_Logout';
					//Functions::LogoutUser();
					break;
				default:
					header("Location: /admin/home");
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
				case 'index':
					LoadView('index','Home-Page');
					break;
                case 'login':
                    if(Functions::$user['id'] != 0) {
                        header("Location: /");
                        exit;
                    }
                    LoadView('login','Login');
                    break;
                case 'logout':
                    LoadHandler('logout');
                    break;
				default:
					header("Location: /");
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

function LoadView($page = '', $page_title = '') {
	global $GET;
    ?>
	<!DOCTYPE html>
	<html lang="en-US">
		<?php
		include('components/head.php');
		?><body class="text-white"><div class="container"><?php
		include('components/navbar.php');
        Functions::ShowErrorMessage();
		include('pages/'.$page.'.php');
		?><div class="divider-50"></div><a href="" class="scroll_to_top"><i class="fa-solid fa-arrow-up"></i></a></div></body><?php
		include('components/footer.php');
        CheckCookies();
        include('components/javascript.php');
		?>
        </html>
    <?php
}

function LoadAdminView($view = '', $page_title = '', $needed_permission = '') {
	include('assets/connectdatabase.php');
    include('assets/functions.php');
	Functions::InitDB($mysqli);
	Functions::CheckUserLogin($mysqli);
	if(!Functions::RoleHasPermission(Functions::$user['role'], $needed_permission)) {
		header("Location: /login");
		exit;
	}
	
    $view = $view;
	global $GET;
    ?>
	<!DOCTYPE html>
	<html lang="en-US">
		<?php
		include('view/admin/modules/head.php');
		?>
		<body class="dark-mode sidebar-mini layout-fixed layout-footer-fixed control-sidebar-slide-open sidebar-mini-xs layout-navbar-fixed">
			<div class="wrapper"><?php
				include('view/admin/modules/navbar_top.php');
				include('view/admin/modules/navbar_side.php');
				?>
				<div class="content-wrapper">
					<?php
					include('view/admin/modules/head_title.php');
					?>
					<section class="content">
						<div class="container-fluid">
							<?php
							include('view/admin/modules/head_boxes.php');
							?>
							<div class="row">
								<?php
								include('view/admin/'.$view.'.php');
								?>
							</div>
						</div>
					</section>
					<?php include('view/admin/modules/footer.php'); ?>
				</div>
				<?php include('view/admin/modules/scripts.php'); ?>
                <script>
                    $(".nav-check").removeClass("active");
                    $("#<?= $view;?>").addClass("active");
                </script>
			</div>
		</body>
	</html>
	<!-- Used Template was provided by AdminLTE (https://adminlte.io) and edited by SpoonyUK for LotusGamingCommunity -->
    <?php
}

function LoadHandler($handler = '') {
	global $GET;
    include('handler/'.$handler.'.php');
}