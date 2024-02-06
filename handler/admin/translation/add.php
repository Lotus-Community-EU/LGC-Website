<?php
//ALTER TABLE `core_translations` ADD `test` VARCHAR(2000) NOT NULL DEFAULT 'none';
if(!$user->hasPermission('admin_translation_add')) {
    $_SESSION['error_title'] = 'Permissions - Add Language';
    $_SESSION['error_message'] = 'You don\'t have permissions to add languages!';
    header("Location: /admin/translation/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_translation_add', $_POST['token'])) {
        $database = $_POST['database'];
        $language_name = $_POST['language_name'];
        $error = 0; $error_msg = '';

        if(Functions::LanguageExists($database)) {
            $error = 1;
            $error_msg = Functions::Translation('text.translation.add.database.already_exists', ['database_column'], [$database]);
        }

        if(strlen($database) < 1 || strlen($database) > 16) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= Functions::Translation('text.translation.add.database.length');
        }

        if(strlen($language_name) < 1 || strlen($language_name) > 32) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= Functions::Translation('text.translation.add.language_name.length');
        }

        if($error == 1) {
            $_SESSION['error_title'] = 'Add Language';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /admin/translation/add");
            exit;
        }

        $database = Functions::$mysqli->real_escape_string($database);
        $language_name = Functions::$mysqli->real_escape_string($language_name);

        $log = new Log();
        $log->setCategory('Translation');
        $log->setUser($user->getID())->setTarget($database);
        $log->setChangedWhat('Added')->setChangedOld('')->setChangedNew('Added');
        $log->setTime(gmdate('U'));
        $log->create();
        
        Functions::$mysqli->query("ALTER TABLE `core_translations` ADD `".$database."` VARCHAR(2000) NOT NULL COMMENT '".$language_name."'");

        $_SESSION['success_message'] = 'Successfully added the language!';
        header("Location: /admin/translation/edit/".$database);
        exit;
    }
    else {
        $_SESSION['error_title'] = 'Add Language';
        $_SESSION['error_message'] = 'An error occured while adding a language. Please try again! (2)';
        header("Location: /admin/translation/add");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Add Language';
    $_SESSION['error_message'] = 'An error occured while adding a language. Please try again! (1)';
    header("Location: /admin/translation/add");
    exit;
}