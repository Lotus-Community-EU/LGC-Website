<?php
$ref = $_SERVER['HTTP_REFERER'];
$rank_id = $GET[2];
if(strpos($ref, Functions::$website_url) == 0) {
    if(Functions::CheckCSRF($_POST['token'])) {
        $all_ranks = Functions::GetAllRanks();
        $rank = $all_ranks[$rank_id];
        $rank_names = Functions::GetRankComments();
        $all_permissions = Functions::GetAllPermissions();

        $rank_name = $_POST['rank_name'];
        $rank_short = $_POST['rank_name'];
        $rank_colour = $_POST['rank_name'];
        $rank_is_staff = $_POST['rank_name'];
        $rank_is_upperstaff = $_POST['rank_name'];

        $permissions = $_POST;

        unset($permissions['rank_name'], $permissions['rank_short'], $permissions['rank_colour'], $permissions['is_staff'], $permissions['is_upperstaff'], $permissions['token']);
    
        die(var_dump($permissions));
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