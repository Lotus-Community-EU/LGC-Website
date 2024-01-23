<?php

class User {

    private $id = null;

    private $data = array();
    private $permissions = array();

    function __construct($search = null) {
        
        if($search != null) {
            if($search == 'guest') {
                $this->id = 0;
                $this->data = array(
                    'id' => 0,
                    'username' => 'Guest',
                    'language' => 'English',
                    'main_rank' => 1,
                    'secondary_rank' => 0
                );
            }
            else {
                if(is_numeric($search)) {
                    $prepare = Functions::$mysqli->prepare("SELECT * FROM web_users WHERE id = ? LIMIT 1");
                    $prepare->bind_param('i', $search);
                }
                else {
                    $prepare = Functions::$mysqli->prepare("SELECT * FROM web_users WHERE login_token = ? LIMIT 1");
                    $prepare->bind_param('s', $search);
                }
                $prepare->execute();
                $result = $prepare->get_result();
                $result = $result->fetch_array();
                
                $this->id = $result['id'];
                $this->data = $result;
            }

            Functions::GetTranslations($this->data['language']);

            $prepare_ranks = Functions::$mysqli->prepare("SELECT * FROM web_ranks_permissions WHERE rank_id = ? OR rank_id = ?");
            $prepare_ranks->bind_param('ii', $this->data['main_rank'], $this->data['secondary_rank']);
            $prepare_ranks->execute();
            $result_ranks = $prepare_ranks->get_result();
            $result_ranks = $result_ranks->fetch_all(MYSQLI_ASSOC);

            foreach($result_ranks as $rank) {
                $this->permissions[$rank['permission_name']] = 1;
            }
        }
    }

    function getID() { return $this->id; }

    function getUsername() { return $this->data['username']; }
    function setUsername($username) {
        $this->data['username'] = $username;
        return $this;
    }

    function getEmail() { return $this->data['email']; }
    function setEmail($email) {
        $this->data['email'] = $email;
        return $this;
    }

    function getCreatedAt() { return $this->data['created_at']; }
    function setCreatedAt($created_at) {
        $this->data['created_at'] = $created_at;
        return $this;
    }

    function getLoginToken() { return $this->data['login_token']; }
    function setLoginToken($login_token) {
        $this->data['login_token'] = $login_token;
        return $this;
    }

    function getMCUUID() { return $this->data['mc_uuid']; }
    function setMCUUID($mc_uuid) {
        $this->data['mc_uuid'] = $mc_uuid;
        return $this;
    }

    function getMCVerifyCode() { return $this->data['mc_verify_code']; }
    function setMCVerifyCode($mc_verify_code) {
        $this->data['mc_verify_code'] = $mc_verify_code;
        return $this;
    }

    function getShowMCName() { return $this->data['show_mc_name']; }
    function setShowMCName($show_mc_name) {
        $this->data['show_mc_name'] = $show_mc_name;
        return $this;
    }

    function getLanguage() { return $this->data['language']; }
    function setLanguage($language) {
        $this->data['language'] = $language;
        return $this;
    }

    function getMainRank() { return $this->data['main_rank']; }
    function setMainRank($main_rank) {
        $this->data['main_rank'] = $main_rank;
        return $this;
    }

    function getSecondaryRank() { return $this->data['secondary_rank']; }
    function setSecondaryRank($secondary_rank) {
        $this->data['secondary_rank'] = $secondary_rank;
        return $this;
    }

    function getBio() { return $this->data['bio']; }
    function setBio($bio) {
        $this->data['bio'] = $bio;
        return $this;
    }

    function getLastUsernameChange() { return $this->data['last_username_change']; }
    function setLastUsernameChange($last_username_change) {
        $this->data['last_username_change'] = $last_username_change;
        return $this;
    }

    function hasPermission($permission) {
        return (isset($this->permissions[$permission]) && $this->permissions[$permission] == 1) ? true : false;
    }

    function update() {
        if($this->id == null) {
            return null;
        }

        $vars = array(
            'username',
            'email',
            'created_at',
            'login_token',
            'mc_uuid',
            'mc_verify_code',
            'show_mc_name',
            'language',
            'main_rank',
            'secondary_rank',
            'bio',
            'last_username_change'
        );

        foreach($vars as $key => $var) {
            $vars[$key] = $var.' = ?';
        }

        $vars = implode(',', $vars);

        $prepare = Functions::$mysqli->prepare("UPDATE web_users SET ".$vars." WHERE id = ? LIMIT 1");
        $prepare->bind_param('ssssssisiisii',
            $this->data['username'],
            $this->data['email'],
            $this->data['created_at'],
            $this->data['login_token'],
            $this->data['mc_uuid'],
            $this->data['mc_verify_code'],
            $this->data['show_mc_name'],
            $this->data['language'],
            $this->data['main_rank'],
            $this->data['secondary_rank'],
            $this->data['bio'],
            $this->data['last_username_change'],
            $this->data['id']
        );
        $prepare->execute();
    }

    function logout() {
        if($this->id == null) {
            return null;
        }
        unset($_SESSION['login_token']);
        ?><script>setcookie('remember','', 1,'/');</scrip><?php
        session_destroy();
        header("Location: /");
    }
}