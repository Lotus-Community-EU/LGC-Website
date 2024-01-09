<?php
if(!Functions::UserHasPermission('admin_translation_log_view')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Translation Logs';
    $_SESSION['error_message'] = 'You don\'t have permissions to view Translation Logs!';
    header("Location: /admin/translation/list");
    exit;
}
?>

<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>Timestamp (UTC)</th>
                <th>Path</th>
                <th>User</th>
                <th>What</th>
                <td>Old</td>
                <td>New</td>
                <?php if(Functions::UserHasPermission('admin_translation_log_delete')) {?>
                <th><?= Functions::Translation('global.delete');?>?</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
                $all_logs = Functions::$mysqli->query("SELECT * FROM web_translation_edit_logs WHERE id > 0 ORDER BY changed_time DESC");
                while($log = $all_logs->fetch_array()) {


                    ?>
                    <tr>
                        <td><?= date('d.m.Y - H:i', $log['changed_time']);?></td>
                        <td><?= $log['language_path'];?></td>
                        <td><a href="/user/<?= $log['changed_by'];?>" class="text-white" target="_blank">Hyperlink (<?= $log['changed_by'];?>)</a></td>
                        <td><?= $log['changed_what'];?></td>
                        <td><?= $log['changed_old'];?></td>
                        <td><?= $log['changed_new'];?></td>
                        <?php if(Functions::UserHasPermission('admin_translation_log_delete')) {?>
                        <td><a href="/admin/translation/delete" class="btn btn-danger btn-sm w-100 fw-bold">&times;</a></td>
                        <?php } ?>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>