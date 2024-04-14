<?php
if(!$user->hasPermission('admin_changelog_log_view')) {
    $_SESSION['error_title'] = 'Permissions - Changelog Logs';
    $_SESSION['error_message'] = 'You don\'t have permissions to view Changelog Logs!';
    header("Location: /admin/changelog/list");
    exit;
}
$csrf_token = Functions::CreateCSRFToken('admin_changelog_log_delete');
?>

<a href="/admin/changelog/list" class="btn btn-primary btn-sm mb-2"><?= Functions::Translation('text.back_to_overview');?></a>

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
                <?php if($user->hasPermission('admin_changelog_log_delete')) {?>
                <th><?= Functions::Translation('global.delete');?>?</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
                <?php
                    if($user->hasPermission('admin_changelog_log_delete')) {
                        $all_logs = Functions::$mysqli->query("SELECT * FROM web_logs WHERE category = 'Changelog' AND id > 0 ORDER BY time DESC");
                    }
                    else {
                        $all_logs = Functions::$mysqli->query("SELECT * FROM web_logs WHERE category = 'Changelog' AND id > 0 AND deleted = '0' ORDER BY time DESC");
                    }
                    while($log = $all_logs->fetch_array()) {
                        ?>
                        <tr <?= ($user->hasPermission('admin_changelog_log_delete') && $log['deleted'] == 1) ? 'class="table-danger"' : '';?>>
                            <td>
                                <span <?= ($user->hasPermission('admin_changelog_log_delete') && $log['deleted'] == 1) ? 'style="cursor: help;" title="Deleted at: '.date('d.m.Y - H:i', $log['deleted_time']).'"' : '';?>>
                                    <?= date('d.m.Y - H:i', $log['time']);?>
                                </span>
                            </td>
                            <td>
                                <a href="/changelog/<?= $log['target'];?>" <?= ($user->hasPermission('admin_changelog_log_delete') && $log['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Hyperlink (<?= $log['target'];?>)</a>
                            </td>
                            <td>
                                <a href="/profile/<?= $log['user'];?>" <?= ($user->hasPermission('admin_changelog_log_delete') && $log['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Hyperlink (<?= $log['user'];?>)</a>
                                <?php if($log['deleted'] == 1) {?>
                                    | <a href="/profile/<?= $log['deleted_by'];?>" <?= ($user->hasPermission('admin_changelog_log_delete') && $log['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Deleted by (<?= $log['deleted_by'];?>)</a>
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
                            <?php if($user->hasPermission('admin_changelog_log_delete')) {?>
                            <td>
                                <?php if($log['deleted'] == 1) {?>
                                    <form action="/admin/changelog/logs" method="POST">
                                        <?php Functions::AddCSRFCheck('admin_changelog_edit', $csrf_token);?>
                                        <input type="hidden" name="log_id" value="<?= $log['id'];?>">
                                        <input type="submit" name="recover" value="Recover" class="btn btn-primary btn-sm w-100 fw-bold">
                                    </form>
                                <?php }
                                else {?>
                                    <form action="/admin/changelog/logs" method="POST">
                                        <?php Functions::AddCSRFCheck('admin_changelog_edit', $csrf_token);?>
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