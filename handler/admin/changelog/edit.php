<?php

/*$webhook = new DiscordWebhook('https://discord.com/api/webhooks/1213603756151930970/4PZXn8KDmD_rQjcjDat0UtCm3xj6nwzvHPrRXCxAKfBMPOlkjpxURc5U4lAiqN0yXhIv');
$webhook->setUsername('Bla');
$webhook->setMessage('Test - <@535598273432649740> - '.gmdate('U'));
$webhook->create();

$webhook->setUsername('Blub');
$webhook->setMessage('Test2 - <@&1155573857130905650> - '.gmdate('U'));
$webhook->create();*/

//$webhook->delete('1213608751144112218');

?>

<?php

$webhook_url = 'https://discord.com/api/webhooks/1213603756151930970/4PZXn8KDmD_rQjcjDat0UtCm3xj6nwzvHPrRXCxAKfBMPOlkjpxURc5U4lAiqN0yXhIv';
$for = array(1 => 'Game', 2 => 'Bot', 3 => 'Web');

$ref = $_SERVER['HTTP_REFERER'];
$id = $_POST['changelog_id']; $s_id = $_SESSION['changelog_id']; unset($_SESSION['changelog_id']);
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_changelog_edit', $_POST['token'])) {
        if($id == $s_id) {

            $changelog_id = $GET['2'];
            $prepare = Functions::$mysqli->prepare("SELECT * FROM web_changelogs WHERE id = ?");
            $prepare->bind_param('i', $id);
            $prepare->execute();
            $result = $prepare->get_result();
            if($result->num_rows == 0) {
                $_SESSION['error_title'] = 'Changelog - Not exististing';
                $_SESSION['error_message'] = 'The Changelog you try to edit does not exist!';
                header("Location: /admin/changelog/list");
                exit;
            }
            $changelog = $result->fetch_array();
            if($changelog['posted_by'] != $user->getID() && !$user->hasPermission('admin_changelog_edit_other')) {
                $_SESSION['error_title'] = 'Changelog - Edit Changelogs';
                $_SESSION['error_message'] = 'You don\'t have permissions to edit Changelogs!';
                header("Location: /admin/changelog/list");
                exit;
            }

            $error = 0; $error_msg = '';
            $changed = 0;

            $new_v_old = ''.$_POST['v_old'];
            if(strlen($new_v_old) > 0 && strlen($new_v_old) < 16) {
                if($new_v_old != $changelog['v_old']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('V_Old')->setChangedOld($changelog['v_old'])->setChangedNew($new_v_old);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;
                }
            }
            else {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg .= 'The Old-Version could not be changed - must be between 1 and 15 characters!';
                $new_v_old = $changelog['v_old'];
            }

            $new_v_new = ''.$_POST['v_new'];
            if(strlen($new_v_new) > 0 && strlen($new_v_new) < 16) {
                if($new_v_new != $changelog['v_new']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('V_New')->setChangedOld($changelog['v_new'])->setChangedNew($new_v_new);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;
                }
            }
            else {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg .= 'The New-Version could not be changed - must be between 1 and 15 characters!';
                $new_v_new = $changelog['v_new'];
            }

            $new_title = $_POST['title'];
            if(strlen($new_title) > 0 && strlen($new_title) < 64) {
                if($new_title != $changelog['title']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('Title')->setChangedOld($changelog['title'])->setChangedNew($new_title);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;
                }
            }
            else {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg .= 'The Title could not be changed - must be between 1 and 63 characters!';
                $new_title = $changelog['title'];
            }

            $new_content = $_POST['content'];
            if($_POST['content'] != null) {
                $json_new_content = json_encode($new_content);
                if($json_new_content != $changelog['content']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('Content')->setChangedOld($changelog['content'])->setChangedNew($json_new_content);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;

                    $new_content = json_encode($new_content);
                }
            }
            else {
                $error = 1;
                if(strlen($error_msg) > 0) { $error_msg .='<br>';}
                $error_msg .= 'You must add at least 1 content line - to delete the Changelog, set "delete" to "yes"';
                $new_content = $changelog['content'];
            }

            $new_c_for = $_POST['c_for'];
            if($new_c_for != $changelog['c_for']) {
                $log = new Log();
                $log->setCategory('Changelog');
                $log->setUser($user->getID())->setTarget($changelog['id']);
                $log->setChangedWhat('C_For')->setChangedOld($changelog['c_for'])->setChangedNew($new_c_for);
                $log->setTime(gmdate('U'));
                $log->create();
                $changed++;
            }

            $new_discord_message_id = $changelog['discord_message_id'];

            if($changelog['discord_message_id'] != 0) {
                $update_discord = isset($_POST['update_discord']) ? 1 : 0;
                $delete_discord = isset($_POST['delete_discord']) ? 1 : 0;

                if($update_discord == 1) {
                    $webhook = new DiscordWebhook($webhook_url);

                    $message = '```Old Version: '.$new_v_old.PHP_EOL.'New Version: '.$new_v_new.PHP_EOL.'For: '.$for[$new_c_for].PHP_EOL.PHP_EOL.'```'.PHP_EOL.'**Changes:**'.PHP_EOL;

                    foreach($_POST['content'] as $content) {
                        $message .= '- '.$content.PHP_EOL;
                    }

                    $webhook->setMessage($message);
                    $webhook->update($changelog['discord_message_id']);
                }

                if(isset($_POST['delete_discord'])) {
                    $webhook = new DiscordWebhook($webhook_url);
                    $webhook->delete($changelog['discord_message_id']);

                    $new_discord_message_id = 0;
                }
            }
            else {
                if(isset($_POST['post_discord'])) {
                    $webhook = new DiscordWebhook($webhook_url);
                    $webhook->setCreator($user->getID());

                    $webhook->setUsername('Changelog');

                    $message = '```Old Version: '.$new_v_old.PHP_EOL.'New Version: '.$new_v_new.PHP_EOL.'For: '.$for[$new_c_for].PHP_EOL.PHP_EOL.'```'.PHP_EOL.'**Changes:**'.PHP_EOL;

                    foreach($_POST['content'] as $content) {
                        $message .= '- '.$content.PHP_EOL;
                    }

                    $webhook->setMessage($message);
                    $res = $webhook->create();

                    $message_id = (int) $res['id'];
                    $changelog_id = $changelog['id'];

                    $new_discord_message_id = $message_id;
                }
            }

            $delete_changelog = $_POST['delete_changelog'];
            if($delete_changelog == 1) {
                $prepare = Functions::$mysqli->prepare("DELETE FROM web_changelogs WHERE id = ?");
                $prepare->bind_param('i', $changelog['id']);
                $prepare->execute();

                if($changelog['discord_message_id'] != 0) {
                    $webhook = new DiscordWebhook($webhook_url);
                    $webhook->delete($changelog['discord_message_id']);
                }

                $_SESSION['success_message'] = 'Deleted Changelog successfully!';
                header("Location: /admin/changelog/list");
                exit;
            }

            $prepare = Functions::$mysqli->prepare("UPDATE web_changelogs SET v_old = ?,v_new = ?,c_for = ?,title = ?,content = ?,discord_message_id = ? WHERE id = ?");
            $prepare->bind_param('ssissii', $new_v_old, $new_v_new, $new_c_for, $new_title, $new_content, $new_discord_message_id, $changelog['id']);
            $prepare->execute();

            if($error == 1) {
                $_SESSION['error_title'] = 'Edit Changelog';
                $_SESSION['error_message'] = $error_msg;
            }

            if($changed > 0) {
                $_SESSION['success_message'] = 'Updated the Changelog successfully!';
            }
            header("Location: /admin/changelog/".$changelog['id']);
            exit;
        }
        else {
            $_SESSION['error_title'] = 'Edit Changelog';
            $_SESSION['error_message'] = 'An error occured while editing the Changelog. Please try again! (3)';
            header("Location: /admin/changelog/edit/".$id);
            exit;
        }
    }
    else {
        $_SESSION['error_title'] = 'Edit Changelog';
        $_SESSION['error_message'] = 'An error occured while editing the Changelog. Please try again! (2)';
        header("Location: /admin/ranks/changelog/".$id);
        exit;
    }
}
else {
    $_SESSION['error_title'] = 'Edit Changelog';
    $_SESSION['error_message'] = 'An error occured while editing the Changelog. Please try again! (1)';
    header("Location: /admin/ranks/changelog/".$id);
    exit;
}