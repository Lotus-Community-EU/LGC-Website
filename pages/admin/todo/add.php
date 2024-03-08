<?php if(!$user->hasPermission('admin_todo_list_add')) {
    $_SESSION['error_title'] = 'To-Do System - Add List';
    $_SESSION['error_message'] = 'You don\'t have permissions to add To-Do lists!';
    header("Location: /admin/todo/list");
    exit;
}

$all_ranks = Rank::getAllRanks();
$all_lists = ToDo::getAllLists();
?>

<div class="container col-12 col-lg-6 mb-5">
    <form action="/admin/todo/add" method="POST">

        <hr>
        <h5>Main</h5>
        <hr>

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Name" value="Name" maxlength="64">
        </div>

        <div class="form-group mt-3">
            <label for="name">Description</label>
            <input type="text" name="description" class="form-control" id="name" placeholder="Description" value="Description" maxlength="64">
        </div>

        <div class="form-group mt-3">
            <label for="sort">Sort</label>
            <input type="text" name="sort" class="form-control" id="sort" placeholder="1" value="1">
        </div>

        <div class="form-group mt-3 mb-3">
            <label for="sub_from">Sub from? (Select "None" to create a List)</label>
            <select class="form-select">
                <option value="0">None</option>
                <?php
                foreach($all_lists as $list) {
                    ?><option value="<?= $list['id'];?>"><?= $list['todo_name'];?></option><?php
                }
                ?>
            </select>
        </div>

        <hr>
        <h5>Access</h5>
        <hr>

        <div class="form-group mt-3">
            <label for="hidden">Is To-Do List hidden?</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">
                    Yes
                </label>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="access_for">Access for (CTRL for multiple | Your main rank is automatically selected)</label>
            <select class="form-select" id="access_for">
                <?php
                foreach($all_ranks as $rank) {
                    ?><option value="<?= $rank['id'];?>" <?= $rank['id'] == $user->getMainRank() ? 'selected' : '';?>><?= $rank['name'];?></option><?php
                }
                ?>
            </select>
        </div>

        <hr>
        <h5>Notification Settings</h5>
        <hr>

        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Notify Roles on changes
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Notify Roles for creation
            </label>
        </div>

        <?php Functions::AddCSRFCheck('admin_ranks_edit');?>
        <input type="submit" class="btn btn-success w-100 mt-3" value="<?= Functions::Translation('global.edit');?>">
    </form>
</div>