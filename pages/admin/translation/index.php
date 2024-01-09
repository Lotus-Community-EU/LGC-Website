<?php if(isset($GET[2]) && $GET[2] == 'edit') {
    include('edit.php');
}
elseif(isset($GET[2]) && $GET[2] == 'add') {
    include('add.php');
}
elseif(isset($GET[2]) && $GET[2] == 'logs') {
    include('logs.php');
}
else {
$languages = Functions::GetAllLanguages();
?>

<div class="row justify-content-end mb-3">
    <div class="col-12 col-md-2">
        <div class="text-end">
        <?php if(Functions::UserHasPermission('admin_translation_add')) {?>
            <a class="btn btn-success" href="/admin/translation/add"><?= Functions::Translation('text.translation.language.add');?></a>
        <?php } ?>
        <?php if(Functions::UserHasPermission('admin_translation_log_view')) {?>
            <a class="btn btn-primary mt-2" href="/admin/translation/logs">Logs</a>
        <?php } ?>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th><?= Functions::Translation('global.language');?></th>
                <th><?= Functions::Translation('global.edit');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($languages as $language) {
                    ?>
                    <tr>
                        <td><?= $language['language_name'];?></td>
                        <td><a href="/admin/translation/edit/<?= $language['language_code'];?>" class="btn btn-primary btn-sm w-100"><?= Functions::Translation('text.translation.language.edit');?></a></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>