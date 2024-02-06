<?php
if(!$user->hasPermission('admin_user_management')) {
    $_SESSION['error_title'] = 'Permissions - Delete Log';
    $_SESSION['error_message'] = 'You don\'t have permissions to delete log entries!';
    header("Location: /admin/user/logs/".$_POST['user_id']);
    exit;
}

$ref = $_SERVER['HTTP_REFERER'];
$log_id = $_POST['log_id'];
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_user_management_logs', $_POST['token'])) {
        if(isset($_POST['delete'])) {
            $prepare = Functions::$mysqli->prepare("UPDATE web_logs SET deleted = '1',deleted_by = ?,deleted_time = ? WHERE id = ?");
            $time = gmdate('U');
            $user_id = $user->getID();
            $prepare->bind_param('iii', $user_id, $time, $log_id);
            $prepare->execute();

            $_SESSION['success_message'] = 'You successfully deleted the Log-Entry!';

            header("Location: /admin/user/logs/".$_POST['user_id']);
            exit;
        }
        if(isset($_POST['recover'])) {
            $prepare = Functions::$mysqli->prepare("UPDATE web_logs SET deleted = '0' WHERE id = ?");
            $time = gmdate('U');
            $prepare->bind_param('i', $log_id);
            $prepare->execute();

            $_SESSION['success_message'] = 'You successfully recovered the Log-Entry!';

            header("Location: /admin/user/logs/".$_POST['user_id']);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Delete Log';
        $_SESSION['error_message'] = 'An error occured while deleting the log entry. Please try again! (2)';
        header("Location: /admin/user/logs/".$_POST['user_id']);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Delete Log';
    $_SESSION['error_message'] = 'An error occured while deleting the log entry. Please try again! (1)';
    header("Location: /admin/user/logs/".$_POST['user_id']);
    exit;
}