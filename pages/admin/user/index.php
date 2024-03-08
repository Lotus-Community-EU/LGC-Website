<?php if(isset($GET[2]) && $GET[2] == 'edit') {
    include('edit.php');
}
elseif(isset($GET[2]) && $GET[2] == 'logs') {
    include('logs.php');
}
else { ?>

<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Functions::Translation('global.username');?></th>
                <th title="Format: DD.MM.YYYY"><?= Functions::Translation('global.last_login');?></th>
                <th><?= Functions::Translation('text.edit_user');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $users = Functions::$mysqli->query("SELECT id,username FROM web_users WHERE id > 0");
                while($luser = $users->fetch_array()) {
                    $last_login = Functions::GetLastLogin($luser['id']);
                    ?>
                    <tr>
                        <td><?= $luser['id'];?></td>
                        <td><a href="/profile/<?= $luser['id'];?>" class="text-white" target="_blank"><?= $luser['username'];?></a></td>
                        <td><?= $last_login == 0 ? 'Never' : date('d.m.Y', $last_login);?></td>
                        <td><a href="/admin/user/edit/<?= $luser['id'];?>" class="btn btn-primary btn-sm w-100"><?= Functions::Translation('text.edit_user');?></a></td>
                    </tr>
                    <?php
                }

            ?>
        </tbody>
    </table>
</div>
<?php } ?>