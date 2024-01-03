<?php
if(!Functions::UserHasPermission('admin_rank_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit Ranks';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        $all_ranks = Functions::GetAllRanks();
        $rank_names = Functions::GetRankComments();
        $all_permissions = Functions::GetAllPermissions();

        $name = $_POST['name'];
        $short = $_POST['short'];
        $colour = $_POST['colour'];
        $colour_ingame = $_POST['colour_ingame'];
        $ingame_id = $_POST['ingame_id'];
        $priority = $_POST['priority'];
        $is_staff = isset($_POST['is_staff']) ? 1 : 0;
        $is_upperstaff = isset($_POST['is_upper_staff']) ? 1 : 0;
        $error = 0; $error_msg = '';

        $permissions = $_POST;

        unset($permissions['name'], $permissions['short'], $permissions['colour'], $permissions['is_staff'], $permissions['is_upperstaff'], $permissions['token']);

        if(strlen($name) < 4 || strlen($name) > 64) {
            $error = 1;
            $error_msg = 'The Rank\'s name must be between 4 and 64 letters!';
        }

        $prep = Functions::$mysqli->prepare("SELECT id FROM core_ranks WHERE name = ? LIMIT 1");
        $prep->bind_param('s', $name);
        $prep->execute();

        $result = $prep->get_result();
        if($result->num_rows > 0) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The selected Rank name is already taken by another rank!';
        }

        if(strlen($short) < 1 || strlen($short) > 6) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Rank\'s short-form must be between 1 and 6 letters!';
        }

        if(strlen($colour) != 7) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Something\'s wrong with the Rank\'s colour. Try it again!';
        }

        if(strlen($colour_ingame) > 5) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The in-game colour cannot be longer than 5 characters!';
        }

        if(strlen($ingame_id) > 64) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The in-game ID cannot be longer than 64 characters!';
        }

        $prep = Functions::$mysqli->prepare("SELECT id FROM core_ranks WHERE ingame_id = ? LIMIT 1");
        $prep->bind_param('s', $ingame_id);
        $prep->execute();

        $result = $prep->get_result();
        if($result->num_rows > 0) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The selected in-game ID is already taken by another Rank!';
        }

        if(!is_numeric($priority)) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The priority must be numeric!';
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
            $_SESSION['error_title'] = 'Add Rank';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /admin/ranks/add");
            exit;
        }

        $prepare_core = Functions::$mysqli->prepare("INSERT INTO core_ranks (ingame_id,name,short,colour,colour_ingame,priority,is_staff,is_upperstaff) VALUES (?,?,?,?,?,?,?,?)");
        $prepare_core->bind_param("sssssiii", $ingame_id, $name, $short, $colour, $colour_ingame, $priority, $is_staff, $is_upperstaff);
        $prepare_core->execute();

        $insert_id = $prepare_core->insert_id;

        $prepare_perms = Functions::$mysqli->prepare("INSERT INTO web_ranks_permissions (rank_id) VALUES (?)");
        $prepare_perms->bind_param('i', $insert_id);
        $prepare_perms->execute();

        $prepare_perms = Functions::$mysqli->prepare("UPDATE web_ranks_permissions SET ".$query_perms." WHERE rank_id = ?");
        $prepare_perms->bind_param('i', $insert_id);
        $prepare_perms->execute();
        
        $_SESSION['success_message'] = 'Rank added successfully!';
        header("Location: /admin/ranks/list");
        exit;

    }
    else {
        $_SESSION['error_title'] = 'Add Rank';
        $_SESSION['error_message'] = 'An error occured while adding a rank. Please try again! (2)';
        header("Location: /admin/ranks/add");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Add Rank';
    $_SESSION['error_message'] = 'An error occured while adding a rank. Please try again! (1)';
    header("Location: /admin/ranks/add");
    exit;
}