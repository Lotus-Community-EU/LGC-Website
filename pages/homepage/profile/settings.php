<?php
if($user->getID() == 0) {
    header("Location: /");
    exit;
}

$all_languages = Functions::GetAllLanguages();

$csrf_token = Functions::CreateCSRFToken('profile_settings');

?>

<style>
    ul.list-group > .active {
        border: var(--bs-list-group-border-width) solid var(--bs-list-group-border-color);
        font-weight: bold;
        text-decoration: underline !important;
    }
</style>

<div class="container mb-5">
    <div class="row">
        <div class="container-fluid">
            <p><?= Functions::Translation('text.edit_profile');?> - <span class="text-capitalize"><?= $GET[2];?></span></p>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 col-md-4">
            <ul class="list-group">
                <li class="list-group-item bg-transparent" id="settings_profile">
                    <a href="/profile/settings/profile" class="text-white text-decoration-none">Profile</a>
                </li>
                <li class="list-group-item bg-transparent" id="settings_avatar">
                    <a href="/profile/settings/avatar" class="text-white text-decoration-none">Avatar</a>
                </li>
                <li class="list-group-item bg-transparent" id="settings_minecraft">
                    <a href="/profile/settings/minecraft" class="text-white text-decoration-none">Minecraft</a>
                </li>
                <li class="list-group-item bg-transparent" id="settings_password">
                    <a href="/profile/settings/password" class="text-white text-decoration-none">Password</a>
                </li>
            </ul>
        </div>
        <div class="col-12 col-md-8 mt-5 mt-md-0">
            <form action="/profile/<?= $GET['2'] == 'password' ? 'password' : 'settings';?>" method="POST" class="">
                <?php
                    switch($GET['2']) {
                        case 'profile':
                            include('categories/profile.php');
                            break;
                        case 'avatar':
                            include('categories/avatar.php');
                            break;
                        case 'minecraft':
                            include('categories/minecraft.php');
                            break;
                        case 'password':
                            include('categories/password.php');
                            break;
                        default:
                            header("Location: /profile/settings/profile");
                            break;
                    }
                    ?><input type="hidden" name="ref" value="<?= $GET['2'];?>"><?php
                ?>
            </form>
        </div>
    </div>
</div>

<script>
$(".list-group-item").removeClass("active");
$("#settings_<?= $GET[2];?>").addClass("active");
</script>