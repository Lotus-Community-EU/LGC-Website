<?php
if(!Functions::UserHasPermission('admin_rank_management')) {
    $_SESSION['error_title'] = 'Permissions - Delete Rank';
    $_SESSION['error_message'] = 'You don\'t have permissions to delete ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
$id = $_POST['rank_id']; $s_id = $_SESSION['rank_id']; unset($_SESSION['rank_id']);
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        if($id == $s_id) {
            $rank = Functions::GetAllRanks($id);
            if($rank['id'] != 1 && $rank['id'] != 2) {
                $prep = Functions::$mysqli->prepare("DELETE FROM core_ranks WHERE id = ?");
                $prep->bind_param('i', $id);
                $prep->execute();

                $prep = Functions::$mysqli->prepare("DELETE FROM web_ranks_permissions WHERE rank_id = ?");
                $prep->bind_param('i', $id);
                $prep->execute();

                $prep = Functions::$mysqli->prepare("UPDATE web_users SET main_rank = '2' WHERE main_rank = ?");
                $prep->bind_param('i', $id);
                $prep->execute();

                $prep = Functions::$mysqli->prepare("UPDATE web_users SET secondary_rank = '0' WHERE secondary_rank = ?");
                $prep->bind_param('i', $id);
                $prep->execute();

                $_SESSION['success_message'] = 'Rank was deleted successfully, everyone who had the Rank was set back to \''.Functions::GetAllRanks()[2]['name'].'\'';
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
            $_SESSION['error_message'] = 'An error occured while deleting the rank. Please try again! (3)';
            header("Location: /admin/ranks/edit/".$rank_id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Delete Rank';
        $_SESSION['error_message'] = 'An error occured while deleting the rank. Please try again! (2)';
        header("Location: /admin/ranks/edit/".$rank_id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Delete Rank';
    $_SESSION['error_message'] = 'An error occured while deleting the rank. Please try again! (1)';
    header("Location: /admin/ranks/edit/".$rank_id);
    exit;
}