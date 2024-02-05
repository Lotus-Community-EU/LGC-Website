<?php

class User {

    private $id = null;

    public $lengths = array(
        'username' => array(
            'min' => 1,
            'max' => 64
        ),
        'bio' => array(
            'min' => 0,
            'max' => '4096'
        )
    );

    private $data = array();
    private $permissions = array();
    private $is_staff = 0;
    private $is_upperstaff = 0;

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
                if($result->num_rows > 0) {
                    //$result = $result->fetch_array();
                    $result = $result->fetch_all(MYSQLI_ASSOC)[0];
                    //die(var_dump($result));

                    $this->id = $result['id'];
                    $this->data = $result;
                }
                else {
                    header("Location: /logout");
                    exit;
                }
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

            $prepare_staff = Functions::$mysqli->prepare("SELECT is_staff,is_upperstaff FROM core_ranks WHERE id = ? OR id = ?");
            $prepare_staff->bind_param('ii', $this->data['main_rank'], $this->data['secondary_rank']);
            $prepare_staff->execute();
            $result_staff = $prepare_staff->get_result();
            $result_staff = $result_staff->fetch_all(MYSQLI_ASSOC);

            if(sizeof($result_staff) == 1) {
                $this->is_staff = $result_staff[0]['is_staff'];
                $this->is_upperstaff = $result_staff[0]['is_upperstaff'];
            }
            else {
                if($result_staff[0]['is_staff'] == 1 || $result_staff[1]['is_staff'] == 1) {
                    $this->is_staff = 1;
                }
                if($result_staff[0]['is_upperstaff'] == 1 || $result_staff[1]['is_upperstaff'] == 1) {
                    $this->is_upperstaff = 1;
                }
            }
        }
    }

    function getID() { return $this->id; }

    function getIsStaff() { return $this->is_staff; }
    function getIsUpperStaff() { return $this->is_upperstaff; }

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

    function getCanChangeAvatar() { return $this->data['change_avatar']; }
    function setCanChangeAvatar($new_avatar) {
        $this->data['change_avatar'] = $new_avatar;
        return $this;
    }

    function getAvatar() { return $this->data['avatar']; }
    function setAvatar($new_avatar) {
        $this->data['avatar'] = $new_avatar;
        return $this;
    }

    function deleteAvatarFile() {
        if($this->data['avatar'] != 'none.png') {
            unlink('assets/images/avatar/'.$this->data['avatar']);
            return $this;
        }
        else return null;
    }

    function canChangeUsername() {
        global $settings;
        if($this->data['last_username_change'] == 0) {
            return true;
        }
        else {
            $change_value = $settings->getUsernameChangeValue();
            $change_unit = $settings->getUsernameChangeUnit();
            $change_final = 0;
            switch($change_unit) {
                case 'hours':
                    $change_final = $change_value*3600; // 1 Hour = 3600 Seconds
                case 'days':
                    $change_final = $change_value*86400; // 1 Day = 86400 Seconds
                    break;
                case 'month':
                    $change_final = $change_value*2628000; // 1 Month = 2628000 Seconds
                    break;
            }

            if(time() > ($this->data['last_username_change']+$change_final)) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    function getMCName() {
        if(strlen($this->data['mc_uuid']) < 1) {
            return 'No Account linked';
        }

        $prepare = Functions::$mysqli->prepare("SELECT name FROM mc_users WHERE mcuuid = ? LIMIT 1");
        $prepare->bind_param('s', $this->data['mc_uuid']);
        $prepare->execute();
        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            $result = $result->fetch_array();
            return $result['name'];
        }
        else {
            return 'Never connected to MC-Server!';
        }
    }

    function hasPermission($permission) {
        return (isset($this->permissions[$permission]) && $this->permissions[$permission] == 1) ? true : false;
    }

    function update() {
        if($this->id == null) {
            return null;
        }

        $keys = array_keys($this->data);
        $tmp = $keys[0];
        unset($keys[0]);

        $vars = array();
        foreach($keys as $key => $var) {
            $vars[$key] = $var.' = ?'; 
        }

        $vars = implode(',', $vars);

        array_push($keys, $tmp);

        $types = ''; $bind_keys = array();
        foreach($keys as $id => $key) {
            $bind_keys[$id] = $this->data[$key];
            if(is_numeric($this->data[$key])) {
                $types .= 'i';
            }
            else {
                $types .= 's';
            }
        }
        $prepare = Functions::$mysqli->prepare("UPDATE web_users SET ".$vars." WHERE id = ? LIMIT 1");
        $prepare->bind_param($types, ...$bind_keys);
        $prepare->execute();
    }

    function logout() {
        if($this->id == null) {
            return null;
        }
        unset($_SESSION['login_token']);
        ?><script>setcookie('remember','', 1,'/');</script><?php
        session_destroy();
        header("Location: /");
    }

    function isUsernameRegistered($name) {
        $prepare = Functions::$mysqli->prepare("SELECT id FROM web_users WHERE username = ? LIMIT 1");
        $prepare->bind_param('s', $name);
        $prepare->execute();
        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_array()['id'];
        }
        else {
            return false;
        }
    }
}