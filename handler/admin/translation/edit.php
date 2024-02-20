<?php
$ref = $_SERVER['HTTP_REFERER'];
$language_name = $_POST['language_name']; $s_language_name = $_SESSION['language_name']; //unset($_SESSION['language_name']);
if(strpos($ref, Functions::GetWebsiteURL()) == 0) {
    if(Functions::CheckCSRF('admin_translation_edit', $_POST['token'])) {
        if($language_name == $s_language_name) {
            if(isset($_POST['new_language_name'])) {
                if($user->hasPermission("admin_translation_add")) {
                    $now_name = Functions::GetLanguageName($language_name);
                    $new_name = Functions::$mysqli->real_escape_string($_POST['new_language_name']);
                    $error = 0; $error_msg = '';
                    if(strlen($new_name) < 1 || strlen($new_name) > 32) {
                        $error = 1;
                        $error_msg = '- The input language name is invalid (must be 1-32 characters)!';
                    }

                    $_SESSION['csrf_token']['admin_translation_edit']['token'] = Functions::$csrf[0].$_POST['token'].Functions::$csrf[1];
                    $_SESSION['csrf_token']['admin_translation_edit']['expire'] = time()+1800; // 30 Minutes = 1800 Seconds
                    $_SESSION['language_name'] = $language_name;

                    if($error == 1) {
                        $response = array(
                            'status' => 'error',
                            'message' => $error_msg
                        );
                        header("Location: /admin/translation/edit/".$language_name);
                    }
                    else {
                        if(strcmp($now_name, $new_name)) {
                            $new_name = Functions::RemoveScriptFromString($new_name);
                            $new_name = Functions::RemoveIFrameFromString($new_name);
                            
                            $log = new Log();
                            $log->setCategory('Translation');
                            $log->setUser($user->getID())->setTarget($language_name);
                            $log->setChangedWhat('Name')->setChangedOld($now_name)->setChangedNew($new_name);
                            $log->setTime(gmdate('U'));
                            $log->create();

                            $language_name = Functions::$mysqli->real_escape_string($language_name);
                            $prepare = Functions::$mysqli->prepare("ALTER TABLE core_translations CHANGE `".$language_name."` `".$language_name."` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none' COMMENT '".$new_name."'");
                            $prepare->execute();
                            $response = array(
                                'status' => 'success',
                                'message' => Functions::Translation('text.translation.language.name_edited'),
                                'id' => $_POST['id']
                            );
                        }
                    }

                    header('Content-Type: application/json');
                    echo json_encode($response);
                }
                else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'You don\'t have the permissions to edit the Language\'s name!'
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                }
            }
            else {
                $prepare = Functions::$mysqli->prepare("SELECT * FROM core_translations WHERE LENGTH(?) > 0");
                $prepare->bind_param('s', $language_name);
                $prepare->execute();
        
                $result = $prepare->get_result();
                if($result->num_rows > 0) {
                    $result = $result->fetch_all(MYSQLI_ASSOC);
                    for($i = 0; $i < sizeof($result); $i++) {
                        $current_translations[$result[$i]['path']] = $result[$i][$language_name];
                    }
                }

                $_SESSION['csrf_token']['admin_translation_edit']['token'] = Functions::$csrf[0].$_POST['token'].Functions::$csrf[1];
                $_SESSION['csrf_token']['admin_translation_edit']['expire'] = time()+1800; // 30 Minutes = 1800 Seconds
                $_SESSION['language_name'] = $language_name;

                $path = $_POST['path'];
                $new_bot = isset($_POST['isBot']) ? 1 : 0;
                $new_game = isset($_POST['isGame']) ? 1 : 0;
                $new_web = isset($_POST['isWeb']) ? 1 : 0;
                $new_language = $_POST['new_language'];
                $error = 0; $error_msg = '';

                if(strlen($new_language) > 2000) {
                    $error = 1;
                    $error_msg = 'The translation can not be longer than 2000 characters!';
                }

                if($error == 1) {
                    $response = array(
                        'status' => 'error',
                        'message' => $error_msg,
                        'id' => $_POST['id']
                    );
                }
                else {
                    
                    $prepare = Functions::$mysqli->prepare("SELECT * FROM core_translations WHERE path = ?");
                    $prepare->bind_param('s', $path);
                    $prepare->execute();
                    $result = $prepare->get_result();

                    $now_translation = $result->fetch_array();

                    $new_language = Functions::RemoveScriptFromString($new_language);
                    $new_language = Functions::RemoveIFrameFromString($new_language);

                    if($now_translation['isBot'] != $new_bot) {
                        $log = new Log();
                        $log->setCategory('Translation');
                        $log->setUser($user->getID())->setTarget($path);
                        $log->setChangedWhat('isBot')->setChangedOld($now_translation['isBot'])->setChangedNew($new_bot);
                        $log->setTime(gmdate('U'));
                        $log->create();
                    }
                    if($now_translation['isGame'] != $new_game) {
                        $log = new Log();
                        $log->setCategory('Translation');
                        $log->setUser($user->getID())->setTarget($path);
                        $log->setChangedWhat('isGame')->setChangedOld($now_translation['isGame'])->setChangedNew($new_game);
                        $log->setTime(gmdate('U'));
                        $log->create();
                    }
                    if($now_translation['isWeb'] != $new_web) {
                        $log = new Log();
                        $log->setCategory('Translation');
                        $log->setUser($user->getID())->setTarget($path);
                        $log->setChangedWhat('isWeb')->setChangedOld($now_translation['isWeb'])->setChangedNew($new_web);
                        $log->setTime(gmdate('U'));
                        $log->create();
                    }
                    if(strcmp($current_translations[$path], $new_language)) {
                        $log = new Log();
                        $log->setCategory('Translation');
                        $log->setUser($user->getID())->setTarget($path);
                        $log->setChangedWhat($language_name)->setChangedOld($current_translations[$path])->setChangedNew($new_language);
                        $log->setTime(gmdate('U'));
                        $log->create();
                    }

                    $prepare = Functions::$mysqli->prepare("UPDATE core_translations SET isBot = ?, isGame = ?, isWeb = ?, ".$language_name." = ? WHERE path = ?");
                    $prepare->bind_param('iiiss', $new_bot, $new_game, $new_web, $new_language, $path);
                    $prepare->execute();
                    $response = array(
                        'status' => 'success',
                        'message' => Functions::Translation('text.translation.language.language_edited', ['path','old_translation','new_translation'], [$path, $current_translations[$path], $new_language]),
                        'id' => $_POST['id']
                    );

                }
                header('Content-Type: application/json');
                echo json_encode($response);
            }
        }
        else {
            $response = array(
                'status' => 'error',
                'message' => 'An error occured while editing the Translation. Please try again! (3)',
                'id' => $_POST['id']
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
    else {
        $response = array(
            'status' => 'error',
            'message' => 'An error occured while editing the Translation. Please try again! (2)',
            'id' => $_POST['id']
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
else {
    $response = array(
        'status' => 'error',
        'message' => 'An error occured while editing the Translation. Please try again! (1)',
        'id' => $_POST['id']
    );
    header('Content-Type: application/json');
    echo json_encode($response);
}