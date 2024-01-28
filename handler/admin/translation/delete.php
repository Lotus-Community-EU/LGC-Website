<?php
if(!$user->hasPermission('admin_translation_delete')) {
    $_SESSION['error_title'] = 'Permissions - Delete Language';
    $_SESSION['error_message'] = 'You don\'t have permissions to delete languages!';
    header("Location: /admin/translation/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
$language_name = $_POST['language_name']; $s_language_name = $_SESSION['language_name']; unset($_SESSION['language_name']);
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        if($language_name == $s_language_name) {
            if($language_name != 'English') {

                $language_name = Functions::$mysqli->real_escape_string($language_name);

                $log = new Log();
                $log->setCategory('Translation');
                $log->setUser($user->getID())->setTarget($language_name);
                $log->setChangedWhat('Deleted')->setChangedOld('')->setChangedNew('Deleted');
                $log->setTime(gmdate('U'));
                $log->create();
                
                Functions::$mysqli->query("ALTER TABLE `core_translations` DROP `".$language_name."`");

                Functions::$mysqli->query("UPDATE web_users SET language = 'English' WHERE language = '".$language_name."'");

                $_SESSION['success_message'] = 'Language was deleted successfully, everyone who had that language was set back to English';
                header("Location: /admin/translation/list");
                exit;
            }
            else {
                $_SESSION['error_title'] = 'Delete Language';
                $_SESSION['error_message'] = 'You can not delete that language!';
                header("Location: /admin/translation/edit/".$language_name);
                exit;
            }
        }
        else {
            $_SESSION['error_title'] = 'Delete Language';
            $_SESSION['error_message'] = 'An error occured while deleting the language. Please try again! (3)';
            header("Location: /admin/translation/edit/".$language_name);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Delete Language';
        $_SESSION['error_message'] = 'An error occured while deleting the language. Please try again! (2)';
        header("Location: /admin/translation/edit/".$language_name);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Delete Language';
    $_SESSION['error_message'] = 'An error occured while deleting the language. Please try again! (1)';
    header("Location: /admin/translation/edit/".$language_name);
    exit;
}