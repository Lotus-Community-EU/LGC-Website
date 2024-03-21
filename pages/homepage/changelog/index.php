<?php
$for = array(1 => 'Game', 2 => 'Bot', 3 => 'Web');
if(isset($GET['1']) && is_numeric($GET['1'])) {
    include('view.php');
}
else {
    $all_changelogs = Functions::$mysqli->query("SELECT id,v_old,v_new,c_for,title FROM web_changelogs WHERE id > 0 ORDER BY posted_at DESC");
    if($all_changelogs->num_rows > 0) {
        ?>
        <div class="containter">
            <div class="table-response">
                <table class="table table-dark table-striped text-center text-white" style="background-color: none;">
                    <tr>
                        <th>Version</th>
                        <th>For</th>
                        <th>Title</th>
                        <th>View</th>
                    </tr>
                    <?php
                    while($row = $all_changelogs->fetch_array()) {
                        ?>
                        <tr>
                            <td>
                                <?= $row['v_old'].' => '.$row['v_new'];?>
                            </td>
                            <td>
                                <?= $for[$row['c_for']];?>
                            </td>
                            <td>
                                <?= $row['title'];?>
                            </td>
                            <td>
                                <a href="/changelog/<?= $row['id'];?>" class="btn btn-primary">View</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div>
        <?php
    }
    else {
        echo 'No changelogs have been posted yet!';
    }
}