<?php
if(!$user->hasPermission('admin_translation_log_view')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - Translation Logs';
    $_SESSION['error_message'] = 'You don\'t have permissions to view Translation Logs!';
    header("Location: /admin/translation/list");
    exit;
}
$csrf_token = Functions::CreateCSRFToken('admin_translation_log_delete');
?>

<a href="/admin/translation/list" class="btn btn-primary btn-sm mb-2"><?= Functions::Translation('text.back_to_overview');?></a>

<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>Timestamp (UTC)</th>
                <th>Target</th>
                <th>Admin</th>
                <th>What</th>
                <td>Old</td>
                <td>New</td>
                <?php if($user->hasPermission('admin_translation_log_delete')) {?>
                <th><?= Functions::Translation('global.delete');?>?</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
                <?php
                    if($user->hasPermission('admin_translation_log_delete')) {
                        $all_logs = Functions::$mysqli->query("SELECT * FROM web_logs WHERE category = 'Translation' AND id > 0 ORDER BY time DESC");
                    }
                    else {
                        $all_logs = Functions::$mysqli->query("SELECT * FROM web_logs WHERE category = 'Translation' AND id > 0 AND deleted = '0' ORDER BY time DESC");
                    }
                    while($log = $all_logs->fetch_array()) {
                        ?>
                        <tr <?= ($user->hasPermission('admin_translation_log_delete') && $log['deleted'] == 1) ? 'class="table-danger"' : '';?>>
                            <td>
                                <span <?= ($user->hasPermission('admin_translation_log_delete') && $log['deleted'] == 1) ? 'style="cursor: help;" title="Deleted at: '.date('d.m.Y - H:i', $log['deleted_time']).'"' : '';?>>
                                    <?= date('d.m.Y - H:i', $log['time']);?>
                                </span>
                            </td>
                            <td>
                                <?= $log['target'];?>
                            </td>
                            <td>
                                <a href="/user/<?= $log['user'];?>" <?= ($user->hasPermission('admin_translation_log_delete') && $log['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Hyperlink (<?= $log['user'];?>)</a>
                                <?php if($log['deleted'] == 1) {?>
                                    | <a href="/user/<?= $log['deleted_by'];?>" <?= ($user->hasPermission('admin_translation_log_delete') && $log['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Deleted by (<?= $log['deleted_by'];?>)</a>
                                <?php }?>
                            </td>
                            <td>
                                <?= $log['changed_what'];?>
                            </td>
                            <td>
                                <?= $log['changed_old'];?>
                            </td>
                            <td>
                                <?= $log['changed_new'];?>
                            </td>
                            <?php if($user->hasPermission('admin_translation_log_delete')) {?>
                            <td>
                                <?php if($log['deleted'] == 1) {?>
                                    <form action="/admin/translation/logs" method="POST">
                                        <?php Functions::AddCSRFCheck('admin_translation_edit', $csrf_token);?>
                                        <input type="hidden" name="log_id" value="<?= $log['id'];?>">
                                        <input type="submit" name="recover" value="Recover" class="btn btn-primary btn-sm w-100 fw-bold">
                                    </form>
                                <?php }
                                else {?>
                                    <form action="/admin/translation/logs" method="POST">
                                        <?php Functions::AddCSRFCheck('admin_translation_edit', $csrf_token);?>
                                        <input type="hidden" name="log_id" value="<?= $log['id'];?>">
                                        <input type="submit" name="delete" value="&times;" class="btn btn-danger btn-sm w-100 fw-bold">
                                    </form>
                                <?php }?>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                ?>
        </tbody>
    </table>
</div>