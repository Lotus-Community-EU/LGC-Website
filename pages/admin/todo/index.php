<?php

//$todo->setName('test')->setDescription('Desc')->setSort(1)->setHidden(0)->setSubFrom(-1)->setCreatedBy($user->getID())->setCreatedAt(gmdate('U'));
//$todo->create();

$all_todo = ToDo::getAllLists();

?>

<div class="row justify-content-end mb-3">
    <div class="col-12 col-md-2">
        <div class="text-end">
            <a class="btn btn-success" href="/admin/todo/add"><?= Functions::Translation('text.todo.add_list');?></a>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Functions::Translation('text.todo.list_name');?></th>
                <?php if($user->hasPermission('admin_todo_view_hidden')) {?>
                <th>Hidden?</th>
                <?php } ?>
                <th><?= Functions::Translation('global.view');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($all_todo as $todo) {
                    ?>
                    <tr>
                        <td><?= $todo['id'];?></td>
                        <td><?= $todo['todo_name'];?></td>
                        <?php if($user->hasPermission('admin_todo_view_hidden')) {?>
                        <th><?= $todo['todo_hidden'] == 1 ? 'True' : 'False';?></th>
                        <?php } ?>
                        <td><a href="/admin/todo/view/<?= $todo['id'];?>" class="btn btn-primary btn-sm w-100"><?= Functions::Translation('global.view');?></a></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>