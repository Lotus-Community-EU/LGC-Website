<?php if(!$user->hasPermission('admin_user_management_log_view')) {
    $_SESSION['error_title'] = 'Permissions - View Logs';
    $_SESSION['error_message'] = 'You don\'t have permissions to view User Logs!';
    header("Location: /user");
    exit;
}
$user_id = $GET[3];

$user_data = new User($user_id);
$user_id = $user_data->getID();

$csrf_token = Functions::CreateCSRFToken('admin_user_management_logs');

?>

<div class="row justify-content-end mb-3">
    <div class="col-6">
        <h4>Logs from <?= $user_data->getUsername();?></h4>
    </div>
    <div class="col-6">
        <div class="row">
            <div class="col-12 mt-3 mt-lg-0">
                <div class="w-100 d-flex justify-content-end">
                    <input type="text" name="filter" id="filter" placeholder="Filter" class="form-control w-50">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="table" class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>Timestamp (UTC)</th>
                <th>Admin</th>
                <th>Changed What</th>
                <th>Changed Old</th>
                <th>Changed New</th>
                <?php if($user->hasPermission('admin_user_management_log_delete')) {?>
                <th><?= Functions::Translation('global.delete');?>?</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
                if($user->hasPermission('admin_user_management_log_delete')) {
                    $prepare = Functions::$mysqli->prepare("SELECT * FROM web_logs WHERE category = 'Profile_Edit' AND target = ? ORDER BY time DESC");
                }
                else {
                    $prepare = Functions::$mysqli->prepare("SELECT * FROM web_logs WHERE category = 'Profile_Edit' AND target = ? AND deleted = '0' ORDER BY time DESC");
                }
                $prepare->bind_param('i', $user_id);
                $prepare->execute();
                $result = $prepare->get_result();
                while($row = $result->fetch_array()) {
                    ?>
                    <tr <?= ($user->hasPermission('admin_user_management_log_delete') && $row['deleted'] == 1) ? 'class="table-danger"' : '';?>>
                        <td>
                            <span <?= ($user->hasPermission('admin_user_management_log_delete') && $row['deleted'] == 1) ? 'style="cursor: help;" title="Deleted at: '.date('d.m.Y - H:i', $row['deleted_time']).'"' : '';?>>
                                <?= date('d.m.Y - H:i', $row['time']);?>
                            </span>
                        </td>
                        <td>
                            <a href="/user/<?= $row['user'];?>" <?= ($user->hasPermission('admin_user_management_log_delete') && $row['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Hyperlink (<?= $row['user'];?>)</a>
                            <?php if($row['deleted'] == 1) {?>
                                | <a href="/user/<?= $row['deleted_by'];?>" <?= ($user->hasPermission('admin_user_management_log_delete') && $row['deleted'] == 1) ? 'class="text-dark"' : 'class="text-white"';?> target="_blank">Deleted by (<?= $row['deleted_by'];?>)</a>
                            <?php }?>

                        </td>
                        <td>
                            <?= $row['changed_what'];?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['changed_old']);?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['changed_new']);?>
                        </td>

                        <?php if($user->hasPermission('admin_user_management_log_delete')) {?>
                        <td>
                            <?php if($row['deleted'] == 1) {?>
                                <form action="/admin/user/logs" method="POST">
                                    <?php Functions::AddCSRFCheck('admin_user_management_logs', $csrf_token);?>
                                    <input type="hidden" name="log_id" value="<?= $row['id'];?>">
                                    <input type="hidden" name="user_id" value="<?= $user_id;?>">
                                    <input type="submit" name="recover" value="Recover" class="btn btn-primary btn-sm w-100 fw-bold">
                                </form>
                            <?php }
                            else {?>
                                <form action="/admin/user/logs" method="POST">
                                    <?php Functions::AddCSRFCheck('admin_user_management_logs', $csrf_token);?>
                                    <input type="hidden" name="log_id" value="<?= $row['id'];?>">
                                    <input type="hidden" name="user_id" value="<?= $user_id;?>">
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

<script>
    $(document).ready(function() {
        $('#filter').on('input', function() {
            var input = $(this).val().toLowerCase();
            var table = $('#table');
            
            $('tbody tr', table).each(function() {
                var row = $(this);
                var ShouldHide = true;
                
                $('td', row).each(function() {
                    if ($(this).text().toLowerCase().indexOf(input) > -1) {
                        ShouldHide = false;
                        return false;
                    }
                });

                row.toggle(!ShouldHide);
            });
        });
    });
</script>