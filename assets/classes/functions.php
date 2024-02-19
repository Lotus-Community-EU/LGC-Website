<?php

class Functions {

    public static $website_version = 'v0.0.5';

    public static $csrf = array('TFE$RW§5e342wREw','FT$§E%TR§$E3tzrterTrtgre');
    public static $csrf_token;

    public static $mysqli;

    public static $user;

    public static $translations;
    public static $language_resetted = 0;

    public static $settings;

    public static $user_permissions;
    
    public static $webdev_role = 3;
    public static $pl_role = 12;
    public static $vpl_role = 14;
    public static $bt_role = 33;

    static function GetWebsiteURL() {
        global $config;
        return $config['website_url'];
    }

    static function EncryptString($string = '') {
        return openssl_encrypt($string,"AES-256-CBC","fmiogfdjdtzkmiporzufngikugouhifd", 0,"kdpiotgrkedposlg");
    }
    
    static function DecryptString($string = '') {
        return openssl_decrypt($string,"AES-256-CBC","fmiogfdjdtzkmiporzufngikugouhifd", 0,"kdpiotgrkedposlg");
    }

    static function RemoveScriptFromString($string) {
        if(strpos($string,'<script') !== false) {
            $string = str_replace('<script','', $string);
        }
        if(strpos($string,'&#60;script') !== false) {
            $string = str_replace('&#60;script','', $string);
        }
        if(strpos($string,'&lt;script') !== false) {
            $string = str_replace('&lt;script','', $string);
        }
        return $string;
    }

    static function RemoveIFrameFromString($string) {
        if(strpos($string,'<iframe') !== false) {
            $string = str_replace('<iframe','', $string);
        }
        if(strpos($string,'&#60;iframe') !== false) {
            $string = str_replace('&#60;iframe','', $string);
        }
        if(strpos($string,'&lt;iframe') !== false) {
            $string = str_replace('&lt;iframe','', $string);
        }
        return $string;
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

    static function AddCSRFCheck($token_for, $token = '') {
        if(strlen($token) < 1) {
            $token = self::CreateCSRFToken($token_for);
        }
        self::CreateCSRFInput($token);
        return true;
    }

    static function CreateCSRFToken($token_for) {
        $token = uniqid();
        self::$csrf_token = $token;
        if(!is_array($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = array();
        }
        //array_push($_SESSION['csrf_token'], $token_for);
        $_SESSION['csrf_token'][$token_for]['token'] = self::$csrf[0].$token.self::$csrf[1];
        $_SESSION['csrf_token'][$token_for]['expire'] = time()+1800; // 30 Minutes = 1800 Seconds
        return $token;
    }

    static function CreateCSRFInput($token) {
        echo '<input type="hidden" name="token" value="'.$token.'">';
        return true;
    }

    static function CheckCSRF($token_for, $input) {
        if(self::$csrf[0].$input.self::$csrf[1] == $_SESSION['csrf_token'][$token_for]['token']) {
            if(time() <= $_SESSION['csrf_token'][$token_for]['expire']) {
                unset($_SESSION['csrf_token'][$token_for]);
                return true;
            }
            else {
                unset($_SESSION['csrf_token'][$token_for]);
                return false;
            }
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
            'host'      => '46.4.66.239',
            'user'      => 'root',
            'password'  => 'ChristopherWilhelm2856',
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
        $query = self::$mysqli->query("SELECT * FROM core_ranks WHERE id > 0");
        $all = $query->fetch_all(MYSQLI_ASSOC);

        $return = array();

        $return[0]['id'] = 0;
        $return[0]['name'] = 'None';
        $return[0]['is_staff'] = 0;
        $return[0]['is_upperstaff'] = 0;

        foreach($all as $role) {
            $return[$role['id']]['id'] = $role['id'];
            $return[$role['id']]['ingame_id'] = $role['ingame_id'];
            $return[$role['id']]['name'] = $role['name'];
            $return[$role['id']]['short'] = $role['short'];
            $return[$role['id']]['colour'] = $role['colour'];
            $return[$role['id']]['colour_ingame'] = $role['colour_ingame'];
            $return[$role['id']]['priority'] = $role['priority'];
            $return[$role['id']]['is_staff'] = $role['is_staff'];
            $return[$role['id']]['is_upperstaff'] = $role['is_upperstaff'];
        }

        return $return;
    }

    static function AddProfileEditLog($user_id, $changed_by, $visibility, $changed_what, $changed_old, $changed_new) {
        $time = gmdate('U');
        $prepare = self::$mysqli->prepare("INSERT INTO web_logs_profile_edit (user_id,changed_by,log_visibility,changed_what,changed_old,changed_new,changed_time) VALUES (?,?,?,?,?,?,?)");
        $prepare->bind_param("iiisssi", $user_id, $changed_by, $visibility, $changed_what, $changed_old, $changed_new, $time);
        $prepare->execute();
    }

    static function AddTranslationEditLog($language_path, $changed_by, $changed_what, $changed_old, $changed_new) {
        $time = gmdate('U');
        $prepare = self::$mysqli->prepare("INSERT INTO web_logs_translation_edit (language_path,changed_by,changed_what,changed_old,changed_new,changed_time) VALUES (?,?,?,?,?,?)");
        $prepare->bind_param("sisssi", $language_path, $changed_by, $changed_what, $changed_old, $changed_new, $time);
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

    static function LogoutUser() {
        unset($_SESSION['login_token']);
        ?><script>setcookie('remember','', 1,'/');</scrip><?php
        session_destroy();
        header("Location: /");
    }

    // can_see_tba
    static function AddAdminTabLink($id, $link, $icon, $permission, $text, $tba_new = '', $tba_see = []) {
        global $user;
        if($tba_new == 'TBA') {
            if(in_array($user->getMainRank(), $tba_see) || in_array($user->getSecondaryRank(), $tba_see)) {
                if(strlen($icon) > 0) {
                    echo '<a class="dropdown-item" id="'.$id.'" href="'.$link.'"><i class="'.$icon.'"></i> '.$text.' <span class="badge bg-danger">TBA</span></a>';
                }
                else {
                    echo '<a class="dropdown-item" id="'.$id.'" href="'.$link.'">'.$text.' <span class="badge bg-danger">TBA</span></a>';
                }
            }
            else {
                if(strlen($icon) > 0) {
                    echo '<a class="dropdown-item disabled" id="'.$id.'" href=""><i class="'.$icon.'"></i> '.$text.' <span class="badge badge-sm bg-danger">TBA</span></a>';
                }
                else {
                    echo '<a class="dropdown-item disabled" id="'.$id.'" href="">'.$text.' <span class="badge bg-danger">TBA</span></a>';
                }
            }
        }
        else {
            if($user->hasPermission($permission) == 1) {
                if($tba_new == 'NEW') {
                    $text = $text.' <span class="badge bg-danger">NEW</span>';
                }
                if(strlen($icon) > 0) {
                    echo '<a class="dropdown-item" id="'.$id.'" href="'.$link.'"><i class="'.$icon.'"></i> '.$text.'</a>';
                }
                else {
                    echo '<a class="dropdown-item" id="'.$id.'" href="'.$link.'">'.$text.'</a>';
                }
            }
        }
    }

    static function GetAllPermissions() {

        $return = array();

        $query = self::$mysqli->query("SELECT * FROM web_permissions WHERE id > 0");
        while($row = $query->fetch_all(MYSQLI_ASSOC)) {
            $return[$row['id']]['permission_code'] = $row['permission_code'];
            $return[$row['id']]['permission_description'] = $row['permission_description'];
            $return[$row['id']]['permission_category'] = $row['permission_category'];
        }
        return $row;
    }

    // Check if Language exists SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'translations' AND COLUMN_NAME = 'bla';

    static function GetLanguageName($language) {
        if(self::LanguageExists($language) == false) {
            return 'NOT_EXIST';
        }
        $prepare = self::$mysqli->prepare("SELECT COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'core_translations' AND COLUMN_NAME = ?");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            $result = $result->fetch_array();
            return $result['COLUMN_COMMENT'];
        }
        else {
            return 'NOT_EXIST';
        }
    }

    static function GetTranslations($language) {
        if(self::LanguageExists($language) == false) {
            $language = 'English';
            self::$language_resetted = 1;
        }
        $prepare = self::$mysqli->prepare("SELECT * FROM core_translations WHERE LENGTH(?) > 0 AND isWeb = 1");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            for($i = 0; $i < sizeof($result); $i++) {
                self::$translations[$result[$i]['path']] = $result[$i][$language];
            }
            //self::$translations = $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    static function GetLanguageTranslations($language) {
        if(self::LanguageExists($language) == false) {
            return null;
        }
        $prepare = self::$mysqli->prepare("SELECT * FROM core_translations WHERE LENGTH(?) > 0");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();

        $temp = array();
        if($result->num_rows > 0) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            for($i = 0; $i < sizeof($result); $i++) {
                $temp[$result[$i]['path']] = $result[$i][$language];
            }
            return $temp;
        }
    }

    static function LanguageExists($language) {
        $prepare = self::$mysqli->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'core_translations' AND COLUMN_NAME = ?");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    static function GetAllLanguages() {
        $query = self::$mysqli->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'core_translations' AND COLUMN_NAME NOT IN('id','path','isBot','isGame','isWeb')");
        
        $all = $query->fetch_all(MYSQLI_ASSOC);

        $return = array();

        foreach($all as $language) {
            $return[$language['COLUMN_NAME']]['language_code'] = $language['COLUMN_NAME'];
            $return[$language['COLUMN_NAME']]['language_name'] = $language['COLUMN_COMMENT'];
        }

        return $return;
    }
    
    static function GetNonesFromLanguages() {
        $all_languages = self::GetAllLanguages();

        $query_imp = array();
        foreach($all_languages as $language) {
            $query_imp[] = 'SUM(CASE WHEN ('.$language['language_code'].' = \'none\' OR '.$language['language_code'].' = \'\') AND path != \'dev.control\' THEN 1 ELSE 0 END) AS '.$language['language_code'];
        }
        $query = self::$mysqli->query("SELECT ".implode(',', $query_imp).' FROM core_translations');
        if($query->num_rows > 0) {
            $row = $query->fetch_array();
            return $row;
        }
    }

    static function GetTranslationRows() {
        $query = self::$mysqli->query("SELECT id FROM core_translations WHERE path != 'dev.control'");
        return $query->num_rows;
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

    static function GenerateLinkKey($id) {
        $alphabet = '123456789';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n]*$id;
            if($i == 2) {
                $pass[] = '-'.$id.'-';
            }
        }
        return implode($pass);
        /*$num = $numbers[rand(0, strlen($numbers)-1)]*$id;
        return $pass.$num;*/
    }

    static function GeneratePassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!\"§$%&/()=?';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}