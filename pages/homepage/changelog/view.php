<?php
$changelog_id = $GET['1'];
$changelog = Functions::$mysqli->prepare("SELECT * FROM web_changelogs WHERE id = ? LIMIT 1");
$changelog->bind_param('i', $changelog_id);
$changelog->execute();
$changelog = $changelog->get_result();
$changelog = $changelog->fetch_array();

$added = $changelog['content_added'] == null ? null : json_decode($changelog['content_added']);
$changed = $changelog['content_changed'] == null ? null : json_decode($changelog['content_changed']);
$deleted = $changelog['content_deleted'] == null ? null : json_decode($changelog['content_deleted']);

if($changelog['edit_by'] != 0) {
$edit_user = new User($changelog['edit_by']);
}
?>

<h3>Changelog - <?= $for[$changelog['c_for']];?> (<?= date('d.m.Y - H:i', $changelog['posted_at']);?>)</h3>
<?php if($changelog['edit_by'] != 0) { ?>
<small>Last edited by <a href="/profile/<?= $edit_user->getID();?>" class="text-decoration-underline text-white" target="_blank"><?= $edit_user->getUsername();?></a> at <?= date('d.m.Y - H:i', $changelog['edit_time']);?> UTC</small>
<?php } ?>

<hr class="mt-3 mb-3">

<h4>Added</h4>

<?php
if($added == null) {
    echo 'There has nothing been added with this Changelog!';
}
else {
    echo '<ul>';
    foreach($added as $content) {
        echo '<li>'.$content.'</li>';
    }
    echo '</ul>';
}
?>

<hr class="mt-3 mb-3">

<h4>Changed</h4>

<?php
if($changed == null) {
    echo 'There has nothing been changed with this Changelog!';
}
else {
    echo '<ul>';
    foreach($changed as $content) {
        echo '<li>'.$content.'</li>';
    }
    echo '</ul>';
}
?>

<hr class="mt-3 mb-3">

<h4>Deleted</h4>

<?php
if($deleted == null) {
    echo 'There was nothing deleted with this Changelog!';
}
else {
    echo '<ul>';
    foreach($deleted as $content) {
        echo '<li>'.$content.'</li>';
    }
    echo '</ul>';
}
?>