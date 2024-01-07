<div class="container">
    <footer class="py-3 my-4">
        <ul class="nav justify-content-center border-bottom border-top pb-3 mb-3 pt-3">
            <li class="nav-item">
                <a href="/" class="nav-link text-white px-2"><?= Functions::Translation('nav.home');?></a>
            </li>
            <li class="nav-item">
                <a href="/imprint" class="nav-link text-white px-2"><?= Functions::Translation('nav.imprint');?></a>
            </li>
        </ul>

        <?php if(Functions::GetSetting('copyright_show') == 1) { ?>
        <p class="text-center mb-0"><?= Functions::GetSetting('copyright_text');?></p>
        <?php } ?>

        <?php if(Functions::GetSetting('show_mcheads') == 1) { ?>
        <p class="text-center mb-0">Thanks to <a href="https://mc-heads.net">MCHeads</a> for providing Minecraft Avatars.</p>
        <?php } ?>
    </footer>
</div>