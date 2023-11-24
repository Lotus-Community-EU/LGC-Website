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

    static function AddCSRFCheck() {
        $token = self::CreateCSRFToken();
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
        if(self::$csrf[0].$input.self::$csrf[1] == $_SESSION['csrf_token']) return true;
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

    public static function ConnectDB() {
        $data = array(
            'host'      => 'localhost',
            'user'      => 'root',
            'password'  => '',
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

    public static function LoadUserdData($login_token) {
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
            $prepare = self::$mysqli->prepare("SELECT * FROM users WHERE login_token = ?");
            $prepare->bind_param('s', $login_token);
            $prepare->execute();

            $result = $prepare->get_result();
            if($result->num_rows > 0) {
                self::$user = $result->fetch_array();
            }
            else {
                header("Location: /logout");
                exit;
            }
        }
        self::GetTranslations(self::$user['language']);
        self::GetUserPermissions();
    }

    public static function UserHasPermission($permission) {
        if(isset(self::$user_permissions[$permission]) && self::$user_permissions[$permission] == 1) {
            return 1;
        }
        return 0;
    }

    public static function AddAdminTabLink($link, $icon, $permission, $text) {
        if(self::UserHasPermission($permission) == 1) {
            if(strlen($icon) > 0) {
                echo '<a class="dropdown-item" href="'.$link.'"><i class="'.$icon.'"></i> '.$text.'</a>';
            }
            else {
                echo '<a class="dropdown-item" href="'.$link.'">'.$text.'</a>';
            }
        }
    }

    public static function RankHasPermission($role, $permission) {
        if(is_numeric($role)) {
            $prepare = self::$mysqli->prepare("SELECT id FROM ranks WHERE ? = 1 AND id = ?");
            $prepare->bind_param('si', $language, $role);
        }
        else {
            $prepare = self::$mysqli->prepare("SELECT id FROM ranks WHERE ? = 1 AND rank_name = ?");
            $prepare->bind_param('ss', $language, $role);
        }
        
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public static function GetUserPermissions() {
        $query = self::$mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ranks' AND COLUMN_NAME NOT IN ('id','rank_name','rank_short','rank_colour','is_staff','is_upperstaff')");

		$columns = array();
		$values = array();

		if($query->num_rows > 0) {
			while($row = $query->fetch_assoc()) {
				$columns[] = $row["COLUMN_NAME"];
			}
		}

		$query2 = self::$mysqli->query("SELECT " . implode(",", $columns) . " FROM ranks WHERE id = ".self::$user['main_rank']." OR id = ".self::$user['secondary_rank']);

		if($query2->num_rows > 0) {
			while($row = $query2->fetch_assoc()) {
				$rowValues = array();
				foreach($columns as $column) {
                    if($row[$column] == 1) {
					    $rowValues[$column] = $row[$column];
                    }
				}
				self::$user_permissions = $rowValues;
			}
		}
    }

    // Check if Language exists SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'translations' AND COLUMN_NAME = 'bla';

    public static function GetTranslations($language) {
        if(self::LanguageExists($language) == false) {
            $language = 'en';
            self::$language_resetted = 1;
        }
        $prepare = self::$mysqli->prepare("SELECT * FROM translations WHERE LENGTH(?) > 0");
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

    private static function LanguageExists($language) {
        $prepare = self::$mysqli->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'translations' AND COLUMN_NAME = ?");
        $prepare->bind_param('s', $language);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }
}