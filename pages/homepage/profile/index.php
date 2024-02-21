<?php
if(!isset($GET[1])) {
    header("Location: /profile/".$user->getID());
    exit;
}
if(strtolower($GET[1]) == 'settings') {
    include('settings.php');
}
elseif(strtolower($GET[1]) == 'password') {
    include('password.php');
}
else {
    $user_id = $GET[1];

    $user_data = new User($user_id);
    $user_id = $user_data->getID();
    $main_rank = new Rank($user_data->getMainRank());
    $secondary_rank = new Rank($user_data->getSecondaryRank());

    $all_languages = Functions::GetAllLanguages();

    if(strlen($user_data->getMCUUID()) > 1) {
        $mc_username = $user->getMCName();
    }
    else {
        $mc_username = 'Unset';
    }
    ?>

    <?php if($user->hasPermission('admin_user_view_admintab') || $user_id == $user->getID()) { ?>
        <div class="row">
            <div class="col-12 offset-md-4 col-md-6 p-0 d-flex justify-content-end">
                <?php if($user->hasPermission('admin_user_view_admintab')) {?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Admin-Menu">
                            Admin
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <?php if($user->hasPermission('admin_user_management')) {?>
                            <li><a class="dropdown-item" href="/admin/user/edit/<?= $user_data->getID();?>"><?= Functions::Translation('text.edit_user');?></a></li>
                            <?php } ?>
                            <?php if($user->hasPermission('admin_user_management_log_view')) {?>
                            <li><a class="dropdown-item" href="/admin/user/logs/<?= $user_data->getID();?>"><?= Functions::Translation('text.view_logs');?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <?php if($user_id == $user->getID()) {?>
                    <div class="dropdown ms-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="User-Menu">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile/settings"><?= Functions::Translation('text.account_settings');?></a></li>
                        </ul>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div class="row mt-2">
        <div class="col-12 col-md-4 mt-3 d-flex justify-content-center">

            <img src="/assets/images/avatar/<?= $user_data->getAvatar();?>" height="250" width="250">

        </div>
        <div class="col-12 col-md-6 mt-3 p-2" style="background-color: #494B4D; border-radius: 10px;">
            <h5 class="text-decoration-underline text-center mb-2">User Info</h5>

            <b><?= Functions::Translation('global.username');?>:</b> <?= $user_data->getUsername();?> <?= strlen($user_data->getPronouns()) > 0 ? '<small>('.$user_data->getPronouns().')</small>' : '';?><br>

            <b>Main Rank:</b> <?= '<font color="'.$main_rank->getColour().'">'.$main_rank->getName().'</font>';?><br>

            <?= $user_data->getSecondaryRank() != 0 ? '<b>Secondary Rank:</b> <font color="'.$secondary_rank->getColour().'">'.$secondary_rank->getName().'</font><br>' : '';?>

            <b><?= Functions::Translation('text.member_since');?>:</b> <?= date('d.m.Y - H:i', $user_data->getCreatedAt());?><br>

            <b><?= Functions::Translation('global.language');?>:</b> <?= $all_languages[$user_data->getLanguage()]['language_name'];?><br>

            <?php if($user->getIsStaff() || $user->getIsUpperStaff() || $user_data->getShowMCName() == 1 || $user_data->getID() == $user->getID()) {?>
            <b>Minecraft-Account:</b> <?= $user_data->getMCUUID() > 1 ? 'Linked ('.$mc_username.($user_data->getShowMCName() == 0 ? ' <span class="text-danger">(Hidden)</span>)' : ')') : 'Not Linked!';?>
            <?php } else {
                ?><b>Minecraft-Account:</b> <?= $user_data->getMCUUID() > 1 ? 'Linked<br>' : 'Not Linked!';?><?php
            } ?>

            <div class="mt-3">
                <b>About me:</b><br>
                <?= $user_data->getBio();?>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 col-md-6 offset-md-4 p-2" style="background-color: #494B4D; border-radius: 10px;">
            <h5 class="text-decoration-underline text-center mb-2">Position History</h5>
            <?php
                $prepare = Functions::$mysqli->prepare("SELECT * FROM web_users_position_history WHERE user_id = ? AND main_secondary = '1' ORDER BY timestamp DESC");
                $prepare->bind_param('i', $user_id);
                $prepare->execute();

                $result = $prepare->get_result();
                if($result->num_rows > 0) {
                    ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Old Rank</th>
                                    <th>New Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = $result->fetch_array()) {
                                    $old_rank = (array)json_decode($row['old_rank']);
                                    $new_rank = (array)json_decode($row['new_rank']);
                                    $time = $row['timestamp'] == -1 ? '-/-' : date('d.m.Y - H:i', $row['timestamp']);
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $time;?>
                                        </td>
                                        <td>
                                            <span style="color: <?= $old_rank['colour'];?>;">
                                                <b><?= $old_rank['name'];?></b>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: <?= $new_rank['colour'];?>;">
                                                <b><?= $new_rank['name'];?></b>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }
                else {
                    echo 'User has no position history!';
                }
            ?>
        </div>
    </div>
<?php } ?>