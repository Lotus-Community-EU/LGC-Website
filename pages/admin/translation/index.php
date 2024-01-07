<?php if(isset($GET[2]) && $GET[2] == 'edit') {
    include('edit.php');
}
elseif(isset($GET[2]) && $GET[2] == 'add') {
    include('add.php');
}
else {
$languages = Functions::GetAllLanguages();
?>

<?php if(Functions::UserHasPermission('admin_translation_add')) {?>
<div class="row justify-content-end mb-3">
    <div class="col-12 col-md-2">
        <div class="text-end">
            <a class="btn btn-success" href="/admin/translation/add"><?= Functions::Translation('text.translation.language.add');?></a>
        </div>
    </div>
</div>
<?php } ?>
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