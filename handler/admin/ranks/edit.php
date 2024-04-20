<?php
if(!$user->hasPermission('admin_rank_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Edit Ranks';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit ranks!';
    header("Location: /admin/ranks/list");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
$id = $_POST['rank_id']; $s_id = $_SESSION['rank_id']; unset($_SESSION['rank_id']);
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_ranks_edit', $_POST['token'])) {
        if($id == $s_id) {

            $rank = new Rank($id);

            $name = $_POST['name'];
            $short = $_POST['short'];
            $description = $_POST['description'];
            $colour = $_POST['colour'];
            $colour_ingame = $_POST['colour_ingame'];
            $ingame_id = $_POST['ingame_id'];
            $discord_role_id = $_POST['discord_role_id'];
            $priority = $_POST['priority'];
            $is_staff = isset($_POST['is_staff']) ? 1 : 0;
            $is_upperstaff = isset($_POST['is_upper_staff']) ? 1 : 0;
            $error = 0; $error_msg = '';

            if($is_upperstaff == 1 && $is_staff == 0) {
                $is_staff = 1;
            }


            $permissions = $_POST;

            unset($permissions['name'], $permissions['short'], $permissions['colour'], $permissions['is_staff'], $permissions['is_upperstaff'], $permissions['token']);

            if(strlen($name) < $rank->lengths['name']['min'] || strlen($name) > $rank->lengths['name']['max']) {
                $error = 1;
                $error_msg = 'The Rank\'s name must be between '.$rank->lengths['name']['min'].' and '.$rank->lengths['name']['min'].' letters!';
            }

            if($rank->nameExists($name)) {
                if(strcmp($name, $rank->getName())) {
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg .= 'The selected Rank name is already taken by another rank!';
                }
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
                if(strcmp($ingame_id, $rank->getIngameID())) {
                    $error = 1;
                    if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                    $error_msg .= 'The selected in-game ID is already taken by another Rank!';
                }
            }

            if(!is_numeric($discord_role_id)) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .= '<br>'; }
                $error_msg .= 'The Discord Role ID has to be numeric!';
            }

            if(!is_numeric($priority)) {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg .= 'The priority must be numeric!';
            }

            if($error == 1) {
                $_SESSION['error_title'] = 'Edit Rank';
                $_SESSION['error_message'] = $error_msg;
                header("Location: /admin/ranks/edit/".$rank->getID());
                exit;
            }

            $rank->setIngameID($ingame_id);
            $rank->setName($name);
            $rank->setShort($short);
            $rank->setDescription($description);
            $rank->setColour($colour);
            $rank->setColourIngame($colour_ingame);
            $rank->setPriority($priority);
            $rank->setDiscordRoleID($discord_role_id);
            $rank->setIsStaff($is_staff);
            $rank->setIsUpperStaff($is_upperstaff);
            $rank->update();

            $permissions = $_POST;
            unset($permissions['name'], $permissions['short'], $permissions['colour'], $permissions['colour_ingame'], $permissions['ingame_id'], $permissions['priority'], $permissions['is_staff'], $permissions['is_upperstaff'], $permissions['token'], $permissions['rank_id']);

            foreach($permissions as $key => $permission) {
                if($permission == 1) {
                    $rank->addPermission($key);
                }
                else {
                    $rank->removePermission($key);
                }
            }
            
            $_SESSION['success_message'] = 'Rank edited successfully!';
            header("Location: /admin/ranks/edit/".$rank->getID());
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit Rank';
            $_SESSION['error_message'] = 'An error occured while editing the rank. Please try again! (3)';
            header("Location: /admin/ranks/edit/".$id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit Rank';
        $_SESSION['error_message'] = 'An error occured while editing the rank. Please try again! (2)';
        header("Location: /admin/ranks/edit/".$id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Rank';
    $_SESSION['error_message'] = 'An error occured while editing the rank. Please try again! (1)';
    header("Location: /admin/ranks/edit/".$id);
    exit;
}