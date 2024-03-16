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

            $new_content_added = isset($_POST['content_added']) ? $_POST['content_added'] : null;
            if($new_content_added != null) {
                $json_new_content_added = json_encode($new_content_added);
                if($json_new_content_added != $changelog['content_added']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('Content_Added')->setChangedOld($changelog['content_added'] == null ? 'Null' : $changelog['content_added'])->setChangedNew($json_new_content_added == null ? 'Null' : $json_new_content_added);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;
                }
                $new_content_added = json_encode($new_content_added);
            }
            else {
                $new_content_added = null;
            }

            $new_content_changed = isset($_POST['content_changed']) ? $_POST['content_changed'] : null;
            if($new_content_changed != null) {
                $json_new_content_changed = json_encode($new_content_changed);
                if($json_new_content_changed != $changelog['content_changed']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('Content_Changed')->setChangedOld($changelog['content_changed'] == null ? 'Null' : $changelog['content_changed'])->setChangedNew($json_new_content_changed == null ? 'Null' : $json_new_content_changed);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;
                }
                $new_content_changed = json_encode($new_content_changed);
            }
            else {
                $new_content_changed = null;
            }

            $new_content_removed = isset($_POST['content_removed']) ? $_POST['content_removed'] : null;
            if($new_content_removed != null) {
                $json_new_content_removed = json_encode($new_content_removed);
                if($json_new_content_removed != $changelog['content_removed']) {
                    $log = new Log();
                    $log->setCategory('Changelog');
                    $log->setUser($user->getID())->setTarget($changelog['id']);
                    $log->setChangedWhat('Content_Removed')->setChangedOld($changelog['content_removed'] == null ? 'Null' : $changelog['content_removed'])->setChangedNew($json_new_content_removed == null ? 'Null' : $json_new_content_removed);
                    $log->setTime(gmdate('U'));
                    $log->create();
                    $changed++;
                }
                $new_content_removed = json_encode($new_content_removed);
            }
            else {
                $new_content_removed = null;
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
            $new_c_for = (int) $new_c_for;

            $new_discord_message_id = $changelog['discord_message_id'];

            if($changelog['discord_message_id'] != 0) {
                $update_discord = isset($_POST['update_discord']) ? 1 : 0;
                $delete_discord = isset($_POST['delete_discord']) ? 1 : 0;

                if($update_discord == 1) {
                    $webhook = new DiscordWebhook($webhook_url);

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
                            'name' => $for[$new_c_for].'-Changelog (v'.$new_v_old.' => v'.$new_v_new.')',
                        ]
                    ];
                   
                    $webhook->setEmbed($embed);
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
                            'name' => $for[$new_c_for].'-Changelog (v'.$new_v_old.' => v'.$new_v_new.')',
                        ]
                    ];
                   
                    $webhook->setEmbed($embed);
                   
                    $webhook->setEmbed($embed);
                    
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

            $prepare = Functions::$mysqli->prepare("UPDATE web_changelogs SET v_old = ?,v_new = ?,c_for = ?,title = ?,content_added = ?,content_changed = ?,content_removed = ?,discord_message_id = ? WHERE id = ?");
            $prepare->bind_param('ssissssii', $new_v_old, $new_v_new, $new_c_for, $new_title, $new_content_added, $new_content_changed, $new_content_removed, $new_discord_message_id, $changelog['id']);
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