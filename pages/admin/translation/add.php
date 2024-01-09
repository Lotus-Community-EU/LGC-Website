<div class="container w-50 mb-5">
    <div class="d-flex justify-content-between">
        <div>
            <p><?= Functions::Translation('text.translation.language.add');?></p>
        </div>
    </div>
    <form action="/admin/translation/add" method="POST">

        <div class="form-group">
            <?php $database = Functions::Translation('text.translation.add.database');?>
            <label for="database"><?= $database;?></label>
            <input type="text" name="database" class="form-control" id="database" placeholder="<?= $database;?>">
        </div>
        <div class="form-group mt-3">
            <?php $language_name = Functions::Translation('text.translation.add.language_name');?>
            <label for="language_name"><?= $language_name;?></label>
            <input type="text" name="language_name" class="form-control" id="language_name" placeholder="<?= $language_name;?>">
        </div>

        <?php Functions::AddCSRFCheck();?>
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.add');?>">
    </form>
</div>