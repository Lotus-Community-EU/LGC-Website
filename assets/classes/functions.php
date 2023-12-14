<?php

class Functions {

    public static $website_url = 'https://localhost';
    public static $website_version = 'v0.0.1-R2';

    public static $csrf = array('TFE$RW§5e342wREw','FT$§E%TR§$E3tzrterTrtgre');
    public static $csrf_token;

    public static $mysqli;

    public static $user;

    public static $translations;
    public static $language_resetted = 0;

    public static $user_permissions;

    static function EncryptString($string = '') {
        return openssl_encrypt($string,"AES-256-CBC","fmiogfdjdtzkmiporzufngikugouhifd", 0,"kdpiotgrkedposlg");
    }
    
    static function DecryptString($string = '') {
        return openssl_decrypt($string,"AES-256-CBC","fmiogfdjdtzkmiporzufngikugouhifd", 0,"kdpiotgrkedposlg");
    }
    
    static function GetIP() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }   
        else {  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }

    static function GetLastLogin($user_id) {
        $prepare = self::$mysqli->prepare("SELECT login_time FROM web_users_logins WHERE user_id = ? ORDER BY login_time DESC LIMIT 1");
        $prepare->bind_param('i', $user_id);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_array()['login_time'];
        }
        else {
            return '0';
        }
    }

    static function AddCSRFCheck($token = '') {
        if(strlen($token) < 1) {
            $token = self::CreateCSRFToken();
        }
        self::CreateCSRFInput($token);
        return true;
    }

    static function CreateCSRFToken() {
        $token = uniqid();
        self::$csrf_token = $token;
        $_SESSION['csrf_token'] = self::$csrf[0].$token.self::$csrf[1];
        return $token;
    }

    static function CreateCSRFInput($token) {
        echo '<input type="hidden" name="token" value="'.$token.'">';
        return true;
    }

    static function CheckCSRF($input) {
        if(self::$csrf[0].$input.self::$csrf[1] == $_SESSION['csrf_token']) {
            unset($_SESSION['csrf_token']);
            return true;
        }
        return false;
    }

    static function CreateUniqueToken($id) {
        $unique_id = uniqid();
        $bytes = array(bin2hex($id.random_bytes(6)), bin2hex($id.random_bytes(6)));
        $rand = random_int(0, 1);
        if($rand == 0) {
            return $bytes[0].$unique_id.$bytes[1];
        }
        else {
            return $bytes[1].$unique_id.$bytes[0];
        }
    }

    static function CheckEmail($email = '') {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) !== false) return true;
        return false;
    }

    static function IsUsernameRegistered($username) {
        $prepare = self::$mysqli->prepare("SELECT * FROM web_users WHERE username = ?");
        $prepare->bind_param('s', $username);
        $prepare->execute();
        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return 1;
        }
        else {
            return 0;
        }
    }

    static function ShowErrorMessage() {
        if(isset($_SESSION['error_message']) && strlen($_SESSION['error_message']) > 0) {
			echo '
				<div class="alert alert-danger alert-dismissible fade show text-dark">
					<b>An error occured - '.$_SESSION['error_title'].'</b><br>
					<span>'.$_SESSION['error_message'].'</span>
                    
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			';
			unset($_SESSION['error_message'], $_SESSION['error_title']);
		}
    }

    static function ShowSuccessMessage() {
        if(isset($_SESSION['success_message']) && strlen($_SESSION['success_message']) > 0) {
			echo '
				<div class="alert alert-success alert-dismissible fade show text-dark">
					<span>'.$_SESSION['success_message'].'</span>
                    
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			';
			unset($_SESSION['success_message']);
		}
    }

    static function ConnectDB() {
        $data = array(
            'host'      => '45.81.233.154',
            'user'      => 'web_lokal',
            'password'  => 'gfREDTE%6t34&§T$EWRTF',
            'database'  => 'LotusGaming'
        );

        self::$mysqli = new mysqli($data['host'], $data['user'], $data['password'], $data['database']);

        if(self::$mysqli->connect_error) {
            die('Could not connect to the Database - '.$mysqli->connect_error);
        }
    }

    static function HashPassword($pw = '') {
        return hash('Whirlpool', strrev($pw).'fgnu=drfgE%TeERTe456te4tr4RTTZ5r(dgfgR');
    }

    static function GetUserData($user_id) {
        $prepare = self::$mysqli->prepare("SELECT * FROM web_users WHERE id = ?");
        $prepare->bind_param('i', $user_id);
        $prepare->execute();
        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_array();
        }
        else {
            return '0';
        }
    }

    static function GetAllRanks() {
        $query = self::$mysqli->query("SELECT id,rank_name,rank_short,rank_colour,is_staff,is_upperstaff FROM core_ranks WHERE id > 0");
        $all = $query->fetch_all(MYSQLI_ASSOC);

        $return = array();

        $return[0]['id'] = 0;
        $return[0]['rank_name'] = 'None';
        $return[0]['is_staff'] = 0;
        $return[0]['is_upperstaff'] = 0;

        foreach($all as $role) {
            $return[$role['id']]['id'] = $role['id'];
            $return[$role['id']]['rank_name'] = $role['rank_name'];
            $return[$role['id']]['rank_short'] = $role['rank_short'];
            $return[$role['id']]['rank_colour'] = $role['rank_colour'];
            $return[$role['id']]['is_staff'] = $role['is_staff'];
            $return[$role['id']]['is_upperstaff'] = $role['is_upperstaff'];
        }

        return $return;
    }

    static function AddLog($staff_id, $user_id, $category, $visibility, $text) {
        $prepare = self::$mysqli->prepare("INSERT INTO web_logs (staff_id,user_id,log_category,log_visibility,log_entry) VALUES (?,?,?,?,?)");
        $prepare->bind_param("iisis", $staff_id, $user_id, $category, $visibility, $text);
        $prepare->execute();
    }

    static function GetUserRanks($user_id) {
        $prepare = self::$mysqli->prepare("SELECT main_rank,secondary_rank FROM web_users WHERE id = ?");
        $prepare->bind_param('i', $user_id);
        $prepare->execute();
        $result = $prepare->get_result();
        $result = $result->fetch_array();
        return [$result[0], $result[1]];
    }

    static function LoadUserdData($login_token) {
        if($login_token == 'guest') {
            self::$user = array(
                'id' => 0,
                'username' => 'Guest',
                'language' => 'en',
                'main_rank' => 1,
                'secondary_rank' => 0
            );
        }
        else {
            $prepare = self::$mysqli->prepare("SELECT * FROM web_users WHERE login_token = ?");
            $prepare->bind_param('s', $login_token);
            $prepare->execute();

            $result = $prepare->get_result();
            if($result->num_rows > 0) {
                self::$user = $result->fetch_array();
            }
            else {
                self::LogoutUser();
                exit;
            }
        }
        self::GetTranslations(self::$user['language']);
        self::GetUserPermissions();
    }

    static function LogoutUser() {
        unset($_SESSION['login_token']);
        ?><script>setcookie('remember','', 1,'/');</script><?php
        session_destroy();
        header("Location: /");
    }

    static function UserHasPermission($permission) {
        if(isset(self::$user_permissions[$permission]) && self::$user_permissions[$permission] == 1) {
            return true;
        }
        return false;
    }

    static function AddAdminTabLink($link, $icon, $permission, $text) {
        if(self::UserHasPermission($permission) == 1) {
            if(strlen($icon) > 0) {
                echo '<a class="dropdown-item" href="'.$link.'"><i class="'.$icon.'"></i> '.$text.'</a>';
            }
            else {
                echo '<a class="dropdown-item" href="'.$link.'">'.$text.'</a>';
            }
        }
    }

    static function RankHasPermission($role, $permission) {
        if(is_numeric($role)) {
            $prepare = self::$mysqli->prepare("SELECT id FROM web_ranks_permissions WHERE ? = 1 AND rank_id = ?");
            $prepare->bind_param('si', $language, $role);
        }
        
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    static function RankExists($rank_id) {
        $prepare = self::$mysqli->prepare("SELECT * FROM core_ranks WHERE id = ?");
        $prepare->bind_param('i', $rank_id);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return 1;
        }
        else {
            return 0;
        }
    }

    static function GetUserPermissions() {
        $query = self::$mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'web_ranks_permissions' AND COLUMN_NAME NOT IN ('id','rank_id')");

		$columns = array();
		$values = array();

		if($query->num_rows > 0) {
			while($row = $query->fetch_assoc()) {
				$columns[] = $row["COLUMN_NAME"];
			}
		}

		$query2 = self::$mysqli->query("SELECT " . implode(",", $columns) . " FROM web_ranks_permissions WHERE rank_id = ".self::$user['main_rank']." OR rank_id = ".self::$user['secondary_rank']);

		if($query2->num_rows > 0) {
			while($row = $query2->fetch_assoc()) {
				$rowValues = array();
				foreach($columns as $column) {
                    if($row[$column] == 1) {
					    $rowValues[$column] = (int)$row[$column];
                    }
				}
				self::$user_permissions = $rowValues;
			}
		}
    }

    // Check if Language exists SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'translations' AND COLUMN_NAME = 'bla';

    static function GetTranslations($language) {
        if(self::LanguageExists($language) == false) {
            $language = 'en';
            self::$language_resetted = 1;
        }
        $prepare = self::$mysqli->prepare("SELECT * FROM web_translations WHERE LENGTH(?) > 0");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            for($i = 0; $i < sizeof($result); $i++) {
                self::$translations[$result[$i]['code']] = $result[$i][$language];
            }
            //self::$translations = $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    static function LanguageExists($language) {
        $prepare = self::$mysqli->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'web_translations' AND COLUMN_NAME = ?");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    static function GetAllLanguages() {
        $query = self::$mysqli->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'web_translations' AND COLUMN_NAME NOT IN('id','code')");
        
        $all = $query->fetch_all(MYSQLI_ASSOC);

        $return = array();

        foreach($all as $language) {
            $return[$language['COLUMN_NAME']]['language_code'] = $language['COLUMN_NAME'];
            $return[$language['COLUMN_NAME']]['language_name'] = $language['COLUMN_COMMENT'];
        }

        return $return;
    }  
    
    static function Translation($code, $search = [], $replace = []) {
        if(isset(self::$translations[$code])) {
            $text = self::$translations[$code];
            if(sizeof($search) == sizeof($replace)) {
                for($i = 0; $i < sizeof($search); $i ++) {
                    $text = str_replace("%".$search[$i]."%", $replace[$i], $text);
                }
            }
            return $text;
        }
        else return $code;
    }

    static function GetSetting($setting) {
        $prepare = self::$mysqli->prepare("SELECT text FROM web_settings WHERE code = ? LIMIT 1");
        $prepare->bind_param('s', $setting);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_array()['text'];
        }
        else {
            return '0';
        }
    }

    static function GeneratePassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!\"§$%&/()=?';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}