<?php if(!Functions::UserHasPermission('admin_user_management')) { // User has no permission to edit Users
    $_SESSION['error_title'] = 'Permissions - View Logs';
    $_SESSION['error_message'] = 'You don\'t have permissions to edit Users!';
    header("Location: /user");
    exit;
}
$user_id = $GET[3];

$user_data = Functions::GetUserData($user_id);

if(Functions::IsStaff(Functions::$user)) {
    $query = "SELECT * FROM web_logs_profile_edit WHERE log_visibility = '1' AND user_id = ?";
}
if(Functions::IsUpperStaff(Functions::$user)) {
    $query = "SELECT * FROM web_logs_profile_edit WHERE (log_visibility = '1' OR log_visibility = '2') AND user_id = ?";
}

?>

<div class="row justify-content-end mb-3">
    <div class="col-12">
        <div class="row">
            <div class="col-12 mt-3 mt-lg-0">
                <div class="w-100 d-flex justify-content-end">
                    <input type="text" name="filter" id="filter" placeholder="Filter" class="form-control w-50">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="table" class="table table-dark table-striped text-center text-white" style="background-color: none;">
        <thead>
            <tr>
                <th>Changed By</th>
                <th>Visible for</th>
                <th>Changed What</th>
                <th>Changed Old</th>
                <th>Changed New</th>
                <th>Changed Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $prepare = Functions::$mysqli->prepare($query);
                $prepare->bind_param('i', $user_data['id']);
                $prepare->execute();
                $result = $prepare->get_result();
                while($row = $result->fetch_array()) {
                    ?>
                    <tr>
                        <td>
                            <a href="/user/<?= $row['changed_by'];?>" class="text-white" target="_blank">Changed by (<?= $row['changed_by'];?>)</a>
                        </td>
                        <td><?= $row['log_visibility'] == 1 ? 'Staff' : 'Upper-Staff';?></td>
                        <td><?= $row['changed_what'];?></td>
                        <td><?= $row['changed_old'];?></td>
                        <td><?= $row['changed_new'];?></td>
                        <td><?= date('d.m.Y - H:i', $row['changed_time']);?></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#filter').on('input', function() {
            var input = $(this).val().toLowerCase();
            var table = $('#table');
            
            $('tbody tr', table).each(function() {
                var row = $(this);
                var ShouldHide = true;
                
                $('td', row).each(function() {
                    if ($(this).text().toLowerCase().indexOf(input) > -1) {
                        ShouldHide = false;
                        return false;
                    }
                });

                row.toggle(!ShouldHide);
            });
        });
    });
</script>