<?php
$all_changelogs = Functions::$mysqli->query("SELECT id,v_old,v_new,c_for,title FROM web_changelogs WHERE id > 0 ORDER BY posted_at DESC");
$all_ranks = Rank::getAllRanks();

foreach($all_ranks as $rank) {
    if($rank['is_staff'] == 0 && $rank['is_upperstaff'] == 0) {
        continue;
    }
    ?>
    <div class="container-fluid">
        <?php
        $get_users = Rank::getAllUsersByRank($rank['id']);
        if($get_users == 0) {
            //echo '<span class="text-center">Rank has no Members!</span>';
            continue;
        }
        else {
            ?>
            <div class="row mt-5 justify-content-center">
                <h3 class="text-center"><span style="color: <?= $rank['colour'];?>;"><?= $rank['name'];?></span></h3>
                <?php
                    foreach($get_users as $get_user) {
                        if($get_user['team_hidden'] == 1) { continue; }
                        ?>
                        <div class="col-12 col-sm-6 col-lg-4 mt-3" style="float: none !important;">
                            <div class="card text-white text-center mx-auto" style="width: 18rem; background: none; border: none;">
                                <img src="/assets/images/avatar/<?= $get_user['avatar'];?>"  class="card-img-top" height="250" width="250">
                                <div class="card-body">
                                    <h5 class="card-title"><a href="/profile/<?= $get_user['id'];?>" class="text-white text-decoration-none name_link" target="_blank"><?= $get_user['username'];?></a></h5>
                                    <p class="card-text text-decoration-underline"><?= $rank['name'];?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
    </div>
<?php } ?>

<style type="text/css">
    .name_link:hover {
        text-decoration: underline !important;
    }
</style>