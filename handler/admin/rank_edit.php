<?php
if(!Functions::UserHasPermission('admin_rank_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit Ranks';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
$rank_id = $GET[2];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        $all_ranks = Functions::GetAllRanks();
        $rank = $all_ranks[$rank_id];
        $rank_names = Functions::GetRankComments();
        $all_permissions = Functions::GetAllPermissions();

        $rank_name = $_POST['rank_name'];
        $rank_short = $_POST['rank_short'];
        $rank_colour = $_POST['rank_colour'];
        $rank_is_staff = isset($_POST['rank_is_staff']) ? 1 : 0;
        $rank_is_upperstaff = isset($_POST['rank_is_upper_staff']) ? 1 : 0;
        $error = 0; $error_msg = '';


        $permissions = $_POST;

        unset($permissions['rank_name'], $permissions['rank_short'], $permissions['rank_colour'], $permissions['is_staff'], $permissions['is_upperstaff'], $permissions['token']);

        if(strlen($rank_name) < 4 || strlen($rank_name) > 64) {
            $error = 1;
            $error_msg = 'The Rank\'s name must be between 4 and 64 letters!';
        }

        $prep = Functions::$mysqli->prepare("SELECT id FROM core_ranks WHERE rank_name = ? LIMIT 1");
        $prep->bind_param('s', $rank_name);
        $prep->execute();

        $result = $prep->get_result();
        if($result->num_rows > 0) {
            if(strcmp($rank_name, $rank['rank_name'])) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg .= 'The selected Rank name is already taken by another rank!';
            }
        }

        if(strlen($rank_short) < 1 || strlen($rank_short) > 6) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Rank\'s short-form must be between 1 and 6 letters!';
        }

        if(strlen($rank_colour) != 7) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Something\'s wrong with the Rank\'s colour. Try it again!';
        }

        foreach($all_permissions as $new_perm) {
            $new_permissions[$new_perm] = 0;
            if(isset($permissions[$new_perm])) {
                if(Functions::UserHasPermission($new_perm)) {
                    $new_permissions[$new_perm] = 1;
                }
                else {
                    $new_permissions[$new_perm] = 0;
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg .= 'You tried to set at least 1 permission you don\'t have access to!';
                }
            }
        }

        $query_perms = '';
        foreach($all_permissions as $key => $perm) {
            $query_perms .= $perm.'=\''.$new_permissions[$perm].'\'';
            if($key !== array_key_last($all_permissions)) {
                $query_perms .= ',';
            }
        }

        if($error == 1) {
            $_SESSION['error_title'] = 'Edit Rank';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /admin/ranks/edit/".$rank_id);
            exit;
        }

        $prepare_core = Functions::$mysqli->prepare("UPDATE core_ranks SET rank_name = ?,rank_short = ?,rank_colour = ?,is_staff = ?,is_upperstaff = ? WHERE id = ?");
        $prepare_core->bind_param("sssiii", $rank_name, $rank_short, $rank_colour, $rank_is_staff, $rank_is_upperstaff, $rank_id);
        $prepare_core->execute();

        $prepare_perms = Functions::$mysqli->prepare("UPDATE web_ranks_permissions SET ".$query_perms." WHERE rank_id = ?");
        $prepare_perms->bind_param('i', $rank_id);
        $prepare_perms->execute();
        
        $_SESSION['success_message'] = 'Rank edited successfully!';
        header("Location: /admin/ranks/edit/".$rank_id);
        exit;
    }
    else {
        $_SESSION['error_title'] = 'Edit Rank';
        $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (2)';
        header("Location: /admin/ranks/edit/".$rank_id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Rank';
    $_SESSION['error_message'] = 'An error occured while logging in. Please try again! (1)';
    header("Location: /admin/ranks/edit/".$rank_id);
    exit;
}