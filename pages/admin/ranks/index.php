<?php if(isset($GET[2]) && $GET[2] == 'edit') {
    include('edit.php');
}
else { ?>

<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Functions::Translation('username');?></th>
                <th><?= Functions::Translation('edit');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $ranks = Functions::$mysqli->query("SELECT id,rank_name,rank_short,rank_colour FROM core_ranks WHERE id > 0");
                while($rank = $ranks->fetch_array()) {
                    ?>
                    <tr>
                        <td><?= $rank['id'];?></td>
                        <td><span style="color: <?= $rank['rank_colour'];?>;"><?= $rank['rank_name'].(strlen($rank['rank_short']) > 1 ? '('.$rank['rank_short'].')' : '');?></style></td>
                        <td><a href="/admin/ranks/edit/<?= $rank['id'];?>" class="btn btn-primary btn-sm w-100"><?= Functions::$translations['edit_rank'];?></a></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>