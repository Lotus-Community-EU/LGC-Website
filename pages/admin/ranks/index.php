<?php if(isset($GET[2]) && $GET[2] == 'edit') {
    include('edit.php');
}
elseif(isset($GET[2]) && $GET[2] == 'add') {
    include('add.php');
}
else { ?>

<div class="row justify-content-end mb-3">
    <div class="col-12 col-md-2">
        <div class="text-end">
            <a class="btn btn-success" href="/admin/ranks/add"><?= Functions::Translation('text.rank.add');?></a>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Functions::Translation('text.rank.rank_name');?></th>
                <th><?= Functions::Translation('global.edit');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $ranks = Functions::$mysqli->query("SELECT id,name,short,colour FROM core_ranks WHERE id > 0 ORDER BY priority ASC");
                while($rank = $ranks->fetch_array()) {
                    ?>
                    <tr>
                        <td><?= $rank['id'];?></td>
                        <td><span style="color: <?= $rank['colour'];?>;"><?= $rank['name'].(strlen($rank['short']) > 0 ? ' ('.$rank['short'].')' : '');?></span></td>
                        <td><a href="/admin/ranks/edit/<?= $rank['id'];?>" class="btn btn-primary btn-sm w-100"><?= Functions::Translation('text.rank.edit.button');?></a></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>