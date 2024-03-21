<?php
$for = array(1 => 'Game', 2 => 'Bot', 3 => 'Web');

if(isset($GET['2']) && $GET['2'] == 'add') {
    include('add.php');
}
elseif(isset($GET['2']) && is_numeric($GET['2'])) {
    include('edit.php');
}
else {
?>

<?php if($user->hasPermission('admin_changelog_post')) {?>
<div class="row justify-content-end mb-3">
    <div class="col-12 col-md-2">
        <div class="text-end">
            <a class="btn btn-success" href="/admin/changelog/add">Post Changelog</a>
        </div>
    </div>
</div>
<?php } ?>
<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>For?</th>
                <th>Old-Version</th>
                <th>New-Version</th>
                <th>By?</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $query = Functions::$mysqli->query("SELECT id,title,c_for,v_old,v_new,posted_by FROM web_changelogs WHERE id > 0");
                if($query->num_rows > 0) {
                    while($row = $query->fetch_array()) {
                        ?>
                        <tr>
                            <td><?= $row['id'];?></td>
                            <td><?= $row['title'];?></td>
                            <td><?= $for[$row['c_for']];?></td>
                            <td><?= $row['v_old'];?></td>
                            <td><?= $row['v_new'];?></td>
                            <td><a href="/profile/<?= $row['posted_by'];?>" class="td-none fw-bold text-white" target="_blank">Hyperlink (<?= $row['posted_by'];?>)</td>
                            <td>
                                <a href="/admin/changelog/<?= $row['id'];?>" class="btn btn-primary <?= ($row['posted_by'] == $user->getID() || $user->hasPermission('admin_changelog_edit_other')) ? '' : 'disabled';?>">Edit</a>
                            </td>
                        </tr>
                        <?php
                    }
                }
            ?>
        </tbody>
    </table>
</div>