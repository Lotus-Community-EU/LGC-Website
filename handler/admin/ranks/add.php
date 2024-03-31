<?php
if(!$user->hasPermission('admin_rank_management')) {
    $_SESSION['error_title'] = 'Permissions - Edit Ranks';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_ranks_add', $_POST['token'])) {

        $rank = new Rank();

        $name = $_POST['name'];
        $short = $_POST['short'];
        $description = $_POST['description'];
        $colour = $_POST['colour'];
        $colour_ingame = $_POST['colour_ingame'];
        $ingame_id = $_POST['ingame_id'];
        $priority = $_POST['priority'];
        $is_staff = isset($_POST['is_staff']) ? 1 : 0;
        $is_upperstaff = isset($_POST['is_upper_staff']) ? 1 : 0;
        $error = 0; $error_msg = '';

        if(strlen($name) < $rank->lengths['name']['min'] || strlen($name) > $rank->lengths['name']['max']) {
            $error = 1;
            $error_msg = 'The Rank\'s name must be between '.$rank->lengths['name']['min'].' and '.$rank->lengths['name']['min'].' letters!';
        }

        if($rank->nameExists($name)) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The selected Rank name is already taken by another rank!';
        }

        if(strlen($short) < $rank->lengths['short']['min'] || strlen($short) > $rank->lengths['short']['max']) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Rank\'s short-form must be between '.$rank->lengths['short']['min'].' and '.$rank->lengths['short']['max'].' letters!';
        }

        if(strlen($description) < $rank->lengths['description']['min'] || strlen($description) > $rank->lengths['description']['max']) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Description\'s length must be between '.$rank->lengths['description']['min'].' and '.$rank->lengths['description']['max'].' letters!';
        }

        if(strlen($colour) != $rank->lengths['colour']) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'Something\'s wrong with the Rank\'s colour. Try it again!';
        }

        if(strlen($colour_ingame) > $rank->lengths['colour_ingame']) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The in-game colour cannot be longer than '.$rank->lengths['colour_ingame'].' characters!';
        }

        if(strlen($ingame_id) < $rank->lengths['ingame_id']['min'] || strlen($ingame_id) > $rank->lengths['ingame_id']['max']) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The in-game ID has to be between '.$rank->lengths['ingame_id']['min'].' and '.$rank->lengths['ingame_id']['max'].' characters!';
        }

        if($rank->inGameIDTaken($ingame_id)) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The selected in-game ID is already taken by another Rank!';
        }

        if(!is_numeric($priority)) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The priority must be numeric!';
        }

        if($error == 1) {
            $_SESSION['error_title'] = 'Add Rank';
            $_SESSION['error_message'] = $error_msg;
            header("Location: /admin/ranks/add");
            exit;
        }

        $error_msg = '';

        $rank->setName($name);
        $rank->setShort($short);
        $rank->setDescription($description);
        $rank->setColour($colour);
        $rank->setColourIngame($colour_ingame);
        $rank->setIngameID($ingame_id);
        $rank->setPriority($priority);
        $rank->setIsStaff($is_staff);
        $rank->setIsUpperStaff($is_upperstaff);
        
        $rank->create();

        $permissions = $_POST;
        unset($permissions['name'], $permissions['short'], $permissions['colour'], $permissions['colour_ingame'], $permissions['ingame_id'], $permissions['priority'], $permissions['is_staff'], $permissions['is_upperstaff'], $permissions['token']);

        $all_permissions = $rank->getAllPermissions();

        foreach($all_permissions as $new_perm) {
            if(isset($permissions[$new_perm['permission_code']])) {
                if($user->hasPermission($new_perm['permission_code'])) {
                    $rank->addPermission($new_perm['permission_code']);
                }
                else {
                    $rank->removePermission($new_perm['permission_code']);
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg .= 'You tried to set at least 1 permission you don\'t have access to!';
                }
            }
        }

        if(strlen($error_msg) > 0) {
            $_SESSION['error_title'] = 'Add Rank';
            $_SESSION['error_message'] = $error_msg;
        }
        
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