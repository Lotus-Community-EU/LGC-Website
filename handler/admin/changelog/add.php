<?php

/*$webhook = new DiscordWebhook('https://discord.com/api/webhooks/1213603756151930970/4PZXn8KDmD_rQjcjDat0UtCm3xj6nwzvHPrRXCxAKfBMPOlkjpxURc5U4lAiqN0yXhIv');
$webhook->setUsername('Bla');
$webhook->setMessage('Test - <@535598273432649740> - '.gmdate('U'));
$webhook->create();

$webhook->setUsername('Blub');
$webhook->setMessage('Test2 - <@&1155573857130905650> - '.gmdate('U'));
$webhook->create();*/

//$webhook->delete('1213608751144112218');

if(!$user->hasPermission('admin_changelog_post')) {
    $_SESSION['error_title'] = 'Changelog - Edit Changelogs';
    $_SESSION['error_message'] = 'You don\'t have permissions to post Changelogs!';
    header("Location: /admin/changelog/list");
    exit;
}

?>

<?php

$webhook_url = 'https://discord.com/api/webhooks/1213603756151930970/4PZXn8KDmD_rQjcjDat0UtCm3xj6nwzvHPrRXCxAKfBMPOlkjpxURc5U4lAiqN0yXhIv';
$for = array(1 => 'Game', 2 => 'Bot', 3 => 'Web');

$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_changelog_add', $_POST['token'])) {

        $error = 0; $error_msg = '';
        $changed = 0;

        $v_old = ''.$_POST['v_old'];
        if(strlen($v_old) < 1 || strlen($v_old) > 15) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The Old-Version could not be changed - must be between 1 and 15 characters!';
        }

        $v_new = ''.$_POST['v_new'];
        if(strlen($v_new) < 1|| strlen($v_new) > 15) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The New-Version could not be changed - must be between 1 and 15 characters!';
        }

        $title = $_POST['title'];
        if(strlen($title) < 1 || strlen($title) > 63) {
            $error = 1;
            if(strlen($error_msg) > 0) { $error_msg .='<br>';}
            $error_msg .= 'The Title could not be changed - must be between 1 and 63 characters!';
        }

        $content_added = isset($_POST['content_added']) ? $_POST['content_added'] : null;
        if($content_added != null) {
            $content_added = json_encode($content_added);
        }
        else {
            $content_added = null;
        }

        $content_changed = isset($_POST['content_changed']) ? $_POST['content_changed'] : null;
        if($content_changed != null) {
            $content_changed = json_encode($content_changed);
        }
        else {
            $content_changed = null;
        }

        $content_removed = isset($_POST['content_removed']) ? $_POST['content_removed'] : null;
        if($content_removed != null) {
            $content_removed = json_encode($content_removed);
        }
        else {
            $content_removed = null;
        }

        $c_for = (int) $_POST['c_for'];

        $discord_message_id = 0;

        
        if(isset($_POST['post_discord'])) {
            $webhook = new DiscordWebhook($webhook_url);
            $webhook->setCreator($user->getID());

            $webhook->setUsername('Changelog');

            $added = '';
            if($_POST['content_added'] != null) {
                foreach($_POST['content_added'] as $content) {
                    $added .= '- '.$content.PHP_EOL;
                }
            }

            $changed = '';
            if($_POST['content_changed'] != null) {
                foreach($_POST['content_changed'] as $content) {
                    $changed .= '- '.$content.PHP_EOL;
                }
            }

            $removed = '';
            if($_POST['content_removed'] != null) {
                foreach($_POST['content_removed'] as $content) {
                    $removed .= '- '.$content.PHP_EOL;
                }
            }

            $embed = [
                'title' => '',
                'type' => 'rich',

                'description' => $message
                    .PHP_EOL
                    .((strlen($added) > 0) ? '### Added '.PHP_EOL.$added.PHP_EOL : '')
                    .((strlen($changed) > 0) ? '### Changed'.PHP_EOL.$changed.PHP_EOL : '')
                    .((strlen($removed) > 0) ? '### Removed'.PHP_EOL.$removed : ''),
                
                //'url' => 'https://google.at',

                'color' => hexdec('FF0000'),

                'footer' => [
                    'text' => 'Changelog by '.$user->getUsername().' | '.date('d.m.Y - H:i', gmdate('U'))
                ],

                'author' => [
                    'name' => $for[$c_for].'-Changelog (v'.$v_old.' => v'.$v_new.')',
                ]
            ];
            
            $webhook->setEmbed($embed);
            
            $webhook->setEmbed($embed);
            
            $res = $webhook->create();

            $message_id = (int) $res['id'];
            $changelog_id = $changelog['id'];

            $discord_message_id = $message_id;
        }

        $prepare = Functions::$mysqli->prepare("INSERT INTO web_changelogs (v_old,v_new,c_for,title,content_added,content_changed,content_removed,discord_message_id,posted_by,posted_at) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $prepare->bind_param('ssissssiii', $v_old, $v_new, $c_for, $title, $content_added, $content_changed, $content_removed, $discord_message_id, $user->getID(), gmdate('U'));
        $prepare->execute();

        $insert_id = $prepare->insert_id;

        if($error == 1) {
            $_SESSION['error_title'] = 'Add Changelog';
            $_SESSION['error_message'] = $error_msg;
        }

        if($changed > 0) {
            $_SESSION['success_message'] = 'Added the Changelog successfully!';
        }
        header("Location: /admin/changelog/".$insert_id);
        exit;
    }
    else {
        $_SESSION['error_title'] = 'Edit Changelog';
        $_SESSION['error_message'] = 'An error occured while adding the Changelog. Please try again! (2)';
        header("Location: /admin/changelog/list");
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Changelog';
    $_SESSION['error_message'] = 'An error occured while adding the Changelog. Please try again! (1)';
    header("Location: /admin/changelog/list");
    exit;
}