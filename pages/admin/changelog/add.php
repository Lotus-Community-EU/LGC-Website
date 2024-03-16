<?php
if(!$user->hasPermission('admin_changelog_post')) {
    $_SESSION['error_title'] = 'Changelog - Edit Changelogs';
    $_SESSION['error_message'] = 'You don\'t have permissions to post Changelogs!';
    header("Location: /admin/changelog/list");
    exit;
}
?>

<div class="container col-12 col-lg-6 mb-5">
    <form action="/admin/changelog/add" method="POST">
        <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="v_old">Old version</label>
                        <input type="text" name="v_old" id="v_old" class="form-control" placeholder="Old Version">
                    </div>
                    <div class="col">
                        <label for="v_new">New Version</label>
                        <input type="text" name="v_new" id="v_new" class="form-control" placeholder="New Version">
                    </div>
                </div>
        </div>

        <hr class="mt-3 mb-3">

        <div class="form-group">
            <label for="c_for">Changelog for?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="c_for_game" name="c_for" value="1">
                <label class="form-check-label" for="c_for_game">
                    Game
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="c_for_bot" name="c_for" value="2">
                <label class="form-check-label" for="c_for_bot">
                    Bot
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="c_for_web" name="c_for" value="3">
                <label class="form-check-label" for="c_for_web">
                    Web
                </label>
            </div>
        </div>

        <hr class="mt-3 mb-3">

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" id="title" placeholder="Title for the Changelog" value="">
        </div>

        <hr class="mt-3 mb-3">

        <div class="contents_add">
            <h3>Added <small>(leave empty if nothing has been added)</small></h3>
            
            <div class="row mt-2" id="0"></div>
        </div>
        <button type="button" class="btn btn-success w-100 mt-3" id="add_content_added">Add Line</button>

        <hr class="mt-3 mb-3">

        <div class="contents_changed">
            <h3>Changed <small>(leave empty if nothing has changed)</small></h3>
 
            <div class="row mt-2" id="0"></div>
        </div>
        <button type="button" class="btn btn-success w-100 mt-3" id="add_content_changed">Add Line</button>

        <hr class="mt-3 mb-3">

        <div class="contents_removed">
            <h3>Removed <small>(leave empty if nothing has been added)</small></h3>

            <div class="row mt-2" id="1"></div>
        </div>
        <button type="button" class="btn btn-success w-100 mt-3" id="add_content_removed">Add Line</button>

        <hr class="mt-3 mb-3">

        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="post" name="post_discord" id="post_discord">
            <label class="form-check-label" for="post_discord">
                Post in Discord
            </label>
        </div>

        <hr class="mt-3 mb-3">

        <?php Functions::AddCSRFCheck('admin_changelog_add'); ?>
        <input type="submit" value="<?= Functions::Translation('global.add');?>" name="submit" class="btn btn-success w-100">

    </form>
</div>

<script>
    $(document).ready(function() {
        $('#add_content_added').on('click', function(e) {
            var id = $(".contents_add .row:last-child").prop('id');
            id ++;
            $('.contents_add').append("<div class=\"row mt-2\" id=\"row_"+id+"\"><div class=\"col-11\"><div class=\"form-group\"><input type=\"text\" name=\"content_added[]\" class=\"form-control\" value=\"\" placeholder=\"Content that has been added\"></div></div><div class=\"col-1\"><button type=\"button\" class=\"btn btn-danger btn-sm fw-bold text-center delete_row\">&times;</button></div></div>");
        });
        $('.contents_add').delegate('.delete_row','click', function() {
            $(this).parent().parent().remove();
        });

        $('#add_content_changed').on('click', function(e) {
            var id = $(".contents_changed .row:last-child").prop('id');
            id ++;
            $('.contents_changed').append("<div class=\"row mt-2\" id=\""+id+"\"><div class=\"col-11\"><div class=\"form-group\"><input type=\"text\" name=\"content_changed[]\" class=\"form-control\" value=\"\" placeholder=\"Content that has been changed\"></div></div><div class=\"col-1\"><button type=\"button\" class=\"btn btn-danger btn-sm fw-bold text-center delete_row\">&times;</button></div></div>");
        });
        $('.contents_changed').delegate('.delete_row','click', function() {
            $(this).parent().parent().remove();
        });

        $('#add_content_removed').on('click', function(e) {
            var id = $(".contents_removed .row:last-child").prop('id');
            id ++;
            $('.contents_removed').append("<div class=\"row mt-2\" id=\"row_"+id+"\"><div class=\"col-11\"><div class=\"form-group\"><input type=\"text\" name=\"content_removed[]\" class=\"form-control\" value=\"\" placeholder=\"Content that has been changed\"></div></div><div class=\"col-1\"><button type=\"button\" class=\"btn btn-danger btn-sm fw-bold text-center delete_row\">&times;</button></div></div>");
        });
        $('.contents_removed').delegate('.delete_row','click', function() {
            $(this).parent().parent().remove();
        });
    });
</script>