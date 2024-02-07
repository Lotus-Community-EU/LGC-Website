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
                while($user = $users->fetch_array()) {
                    $last_login = Functions::GetLastLogin($user['id']);
                    ?>
                    <tr>
                        <td><?= $user['id'];?></td>
                        <td><a href="/user/<?= $user['id'];?>" class="text-white" target="_blank"><?= $user['username'];?></a></td>
                        <td><?= date('d.m.Y', $last_login);?></td>
                        <td><a href="/admin/user/edit/<?= $user['id'];?>" class="btn btn-primary btn-sm w-100"><?= Functions::Translation('text.edit_user');?></a></td>
                    </tr>
                    <?php
                }

            ?>
        </tbody>
    </table>
</div>
<?php } ?>