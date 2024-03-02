<?php

/*$webhook = new DiscordWebhook('https://discord.com/api/webhooks/1213575243751231508/pegYyGIM109eBisqKvTFENfZ2z9tfIoicwLIkZ0PfVE7elFXu9lOj7TLa702pUcVo1K8?wait=true');

$webhook->setUsername('Bla');
$webhook->setMessage('Acer = Nub');

$webhook->addEmbed([
    'title' => 'bla',
    'description' => 'Description'
]);

$webhook->create();*/

$webhook = new DiscordWebhook('https://discord.com/api/webhooks/1213603756151930970/4PZXn8KDmD_rQjcjDat0UtCm3xj6nwzvHPrRXCxAKfBMPOlkjpxURc5U4lAiqN0yXhIv');
/*$webhook->setUsername('Bla');
$webhook->setMessage('Chris = Big Nub - '.gmdate('U'));
$webhook->create();*/


$webhook->delete('1213608751144112218');

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-md-8">
            <h4><?= $config['lgc_text'];?>...</h4>
            <p>but what is it? We're an upcoming Multi-Gaming and Multi-Lingual Community.<br>
            Our main-goal to achieve is fun! Finding new friends, increase our knowledge urge our skills and just improve us and others.</p>
            <p class="fst-italic" style="font-size: .75rem;">Yeah, don't wonder because we only use Minecraft-Picture yet. The picture will differ with more games coming!</p>
        </div>
        <div class="col-12 col-md-4">
            <a href="/assets/images/pages/index/chests_full.png" target="_blank"><img src="/assets/images/pages/index/chests.png" alt="Chests Picture" width="300" height="200"></a>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 col-md-8">
            <h4>Our first game...</h4>
            <p>Even though our goal is to become a Multi-Gaming Community, we need to start somewhere.<br>
            Our first developed game, is a Minecraft-Server. Our Team, consisting of 2 Developers, are developing our Minecraft-Server, which will contain much fun stuff!<br>
            We want to make everyone happy, playing on our Server, therefore we will support multiple mini-games. To begin with, we started with Creative, Survival, Skyblock!<br>
            Also planned are Gamemodes (Creative and Survial for now) which can be played with an own exclusive Mod-Pack (TBA)!</p>
            </p>
        </div>
        <div class="col-12 col-md-4">
            <a href="/assets/images/pages/index/player_full.png" target="_blank"><img src="/assets/images/pages/index/player.png" alt="Chests Picture" width="300" height="200"></a>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 col-md-8">
            <h4>Didn't catch you yet?</h4>
            <p>We didn't catch you yet? Totally no problem! We're open for feedback, opinions and any other kind of input you want to give us!<br>
            Get in touch with the ones who are already a part of <?= $config['lgc_text'];?> and also with our Staff-Team on <a href="https://discord.gg/5sWKNsPwtH" class="text-white fw-bold" target="_blank">Discord</a>!
            There you will be kept up to date about our Development progress and can give us your ideas - and talk to us!</p>
        </div>
        <div class="col-12 col-md-4">
            <a href="/assets/images/pages/index/player2_full.png" target="_blank"><img src="/assets/images/pages/index/player2.png" alt="Chests Picture" width="300" height="200"></a>
        </div>
    </div>
</div>