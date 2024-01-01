<?php
if(!Functions::UserHasPermission('admin_rank_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Delete Rank';
    $_SESSION['error_message'] = 'You don\'t have permissions to delete ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
$rank_id = $GET[2];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        $rank = Functions::GetAllRanks($rank_id);
        if($rank['id'] != 1 && $rank['id'] != 2) {
            $prep = Functions::$mysqli->prepare("DELETE FROM core_ranks WHERE id = ?");
            $prep->bind_param('i', $rank_id);
            $prep->execute();

            $prep = Functions::$mysqli->prepare("DELETE FROM web_ranks_permissions WHERE rank_id = ?");
            $prep->bind_param('i', $rank_id);
            $prep->execute();

            $prep = Functions::$mysqli->prepare("UPDATE web_users SET main_rank = '3' WHERE main_rank = ?");
            $prep->bind_param('i', $rank_id);
            $prep->execute();

            $prep = Functions::$mysqli->prepare("UPDATE web_users SET secondary_rank = '0' WHERE secondary_rank = ?");
            $prep->bind_param('i', $rank_id);
            $prep->execute();

            $_SESSION['success_message'] = 'Rank was deleted successfully, everyone who had the Rank was set back to \'Verified\'.';
            header("Location: /admin/ranks/list");
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Delete Rank';
            $_SESSION['error_message'] = 'You can not delete that rank!';
            header("Location: /admin/ranks/edit/".$rank_id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Delete Rank';
        $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (2)';
        header("Location: /admin/ranks/edit/".$rank_id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Delete Rank';
    $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (1)';
    header("Location: /admin/ranks/edit/".$rank_id);
    exit;
}