<?php
$changelog_id = $GET['2'];
$prepare = Functions::$mysqli->prepare("SELECT * FROM web_changelogs WHERE id = ?");
$prepare->bind_param('i', $changelog_id);
$prepare->execute();
$result = $prepare->get_result();
if($result->num_rows == 0) {
    $_SESSION['error_title'] = 'Changelog - Not exististing';
    $_SESSION['error_message'] = 'The Changelog you try to edit does not exist!';
    header("Location: /admin/changelog/list");
    exit;
}
$changelog = $result->fetch_array();
if($changelog['posted_by'] != $user->getID() && !$user->hasPermission('admin_changelog_edit_other')) {
    $_SESSION['error_title'] = 'Changelog - Edit Changelogs';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit Changelogs!';
    header("Location: /admin/changelog/list");
    exit;
}

?>

<div class="container col-12 col-lg-6 mb-5">
    <form action="/admin/changelog/edit" method="POST">
        <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="v_old">Old version</label>
                        <input type="text" name="v_old" id="v_old" class="form-control" placeholder="v_old" value="<?= $changelog['v_old'];?>">
                    </div>
                    <div class="col">
                        <label for="v_new">New Version</label>
                        <input type="text" name="v_new" id="v_new" class="form-control" placeholder="v_new" value="<?= $changelog['v_new'];?>">
                    </div>
                </div>
        </div>

        <hr class="mt-3 mb-3">

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" id="title" placeholder="title" value="<?= $changelog['title'];?>">
        </div>

        <hr class="mt-3 mb-3">

        <div class="contents">
        <?php
            $contents = json_decode($changelog['content']);

            foreach($contents as $key => $content) {
                ?>
                <div class="row mt-2" id="row_<?= $key+1;?>">
                    <div class="col-11">
                        <div class="form-group">
                            <input type="text" name="content[]" class="form-control" value="<?= $content;?>">
                        </div>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger btn-sm fw-bold text-center delete_row" id="<?= $key+1;?>">&times;</button>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <button type="button" class="btn btn-success w-100 mt-3" id="add_content">Add Content</button>

        <hr class="mt-3 mb-3">

        <div class="form-group">
            <label for="c_for">Changelog for?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="c_for_game" name="c_for" value="1" <?= $changelog['c_for'] == 1 ? 'checked' : '';?>>
                <label class="form-check-label" for="c_for_game">
                    Game
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="c_for_bot" name="c_for" value="2" <?= $changelog['c_for'] == 2 ? 'checked' : '';?>>
                <label class="form-check-label" for="c_for_bot">
                    Bot
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="c_for_web" name="c_for" value="3" <?= $changelog['c_for'] == 3 ? 'checked' : '';?>>
                <label class="form-check-label" for="c_for_web">
                    Web
                </label>
            </div>
        </div>

        <?php if($changelog['discord_message_id'] != 0) {?>
        
            <hr class="mt-3 mb-3">
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" name="update_discord" id="update_discord">
                <label class="form-check-label" for="update_discord">
                    Update Discord Message
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="delete" name="delete_discord" id="delete_discord">
                <label class="form-check-label" for="delete_discord">
                    Delete Discord Message
                </label>
            </div>

        <?php } else {?>

            <hr class="mt-3 mb-3">

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="post" name="post_discord" id="post_discord">
                <label class="form-check-label" for="post_discord">
                    Post in Discord
                </label>
            </div>
        <?php } ?>

        <hr class="mt-3 mb-3">

        <div class="form-group">
            <label for="delete_changelog">Delete Changelog</label>
            <select class="form-select" id="delete_changelog" name="delete_changelog">
                <option value="0" selected><?= Functions::Translation('global.no');?></option>
                <option value="1"><?= Functions::Translation('global.yes');?></option>
            </select>
        </div>

        <hr class="mt-3 mb-3">

        <?php Functions::AddCSRFCheck('admin_changelog_edit'); $_SESSION['changelog_id'] = $changelog['id'];?>
        <input type="hidden" name="changelog_id" value="<?= $changelog['id'];?>">
        <input type="submit" value="<?= Functions::Translation('global.edit');?>" name="submit" class="btn btn-success w-100">

    </form>
</div>

<script>
    $(document).ready(function() {
        $('#add_content').on('click', function(e) {
            var id = $(".contents .row:last-child .col-1 .btn").prop('id');
            id ++;
            $('.contents').append("<div class=\"row mt-2\" id=\"row_"+id+"\"><div class=\"col-11\"><div class=\"form-group\"><input type=\"text\" name=\"content[]\" class=\"form-control\" value=\"\" placeholder=\"Content\"></div></div><div class=\"col-1\"><button type=\"button\" class=\"btn btn-danger btn-sm fw-bold text-center delete_row\" id=\""+id+"\">&times;</button></div></div>");
        });
        $('.contents').delegate('.delete_row','click', function() {
            $(this).parent().parent().remove();
        });
    });
</script>