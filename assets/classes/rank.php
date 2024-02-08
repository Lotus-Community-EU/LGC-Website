<?php

class Rank {

    private $id;
    private $data = array();
    private $permissions = array();

    public static $permission_categories = array(
        1 => 'Admin - General',
        2 => 'Admin - User Management',
        3 => 'Admin - Rank Management',
        4 => 'Admin - Translation-System',
        5 => 'Admin - To-Do System'
    );

    public $lengths = array(
        'name' => array(
            'min' => 4,
            'max' => 64
        ),
        'short' => array(
            'min' => 1,
            'max' => 6
        ),
        'colour' => 7,
        'colour_ingame' => 5,
        'ingame_id' => array(
            'min' => 1,
            'max' => 64
        )
    );

    function __construct($rank_id = null) {

        if($rank_id != null || $rank_id == 0) {
            $prepare = Functions::$mysqli->prepare("SELECT * FROM core_ranks WHERE id = ? LIMIT 1");
            $prepare->bind_param('i', $rank_id);
            $prepare->execute();

            $result = $prepare->get_result();
            if($result->num_rows > 0) {
                $result = $result->fetch_array();

                $this->id = $result['id'];
                $this->data = $result;

                $prepare_ranks = Functions::$mysqli->prepare("SELECT * FROM web_ranks_permissions WHERE rank_id = ?");
                $prepare_ranks->bind_param('i', $this->data['id']);
                $prepare_ranks->execute();
                $result_ranks = $prepare_ranks->get_result();
                $result_ranks = $result_ranks->fetch_all(MYSQLI_ASSOC);

                foreach($result_ranks as $rank) {
                    $this->permissions[$rank['permission_name']] = 1;
                }
            }
            else {
                return null;
            }
        }

    }

    function getID() { return $this->id; }

    function getIngameID() { return $this->data['ingame_id']; }
    function setIngameID($ingame_id) {
        $this->data['ingame_id'] = $ingame_id;
        return $this;
    }

    function getName() { return $this->data['name']; }
    function setName($name) {
        $this->data['name'] = $name;
        return $this;
    }

    function getShort() { return $this->data['short']; }
    function setShort($short) {
        $this->data['short'] = $short;
        return $this;
    }

    function getColour() { return $this->data['colour']; }
    function setColour($colour) {
        $this->data['colour'] = $colour;
        return $this;
    }

    function getColourIngame() { return $this->data['colour_ingame']; }
    function setColourIngame($colour_ingame) {
        $this->data['colour_ingame'] = $colour_ingame;
        return $this;
    }

    function getPriority() { return $this->data['priority']; }
    function setPriority($priority) {
        $this->data['priority'] = $priority;
        return $this;
    }

    function getIsStaff() { return $this->data['is_staff']; }
    function setIsStaff($is_staff) {
        $this->data['is_staff'] = $is_staff;
        return $this;
    }

    function getIsUpperStaff() { return $this->data['is_upperstaff']; }
    function setIsUpperStaff($is_upperstaff) {
        $this->data['is_upperstaff'] = $is_upperstaff;
        return $this;
    }

    function hasPermission($permission) {
        return (isset($this->permissions[$permission]) && $this->permissions[$permission] == 1) ? true : false;
    }

    static function getAllRanks() {
        $query = Functions::$mysqli->query("SELECT * FROM core_ranks WHERE id > 0");
        if($query->num_rows > 0) {
            return $query->fetch_all(MYSQLI_ASSOC);
        }
    }

    function nameExists($name) {
        $prepare = Functions::$mysqli->prepare("SELECT id FROM core_ranks WHERE name = ? LIMIT 1");
        $prepare->bind_param('s', $name);
        $prepare->execute();
        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    function inGameIDTaken($ingame_id) {
        $prepare = Functions::$mysqli->prepare("SELECT id FROM core_ranks WHERE ingame_id = ? LIMIT 1");
        $prepare->bind_param('s', $ingame_id);
        $prepare->execute();
        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    function getPermissions() { return $this->permissions; }

    function getAllPermissions() {
        $query = Functions::$mysqli->query("SELECT * FROM web_permissions WHERE id > 0");
        if($query->num_rows > 0) {
            return $query->fetch_all(MYSQLI_ASSOC);
        }
        else {
            return null;
        }
    }

    function addPermission($permission) {
        if($this->id == null) {
            return $null;
        }
        if(!$this->hasPermission($permission)) {
            $this->permissions[$permission] = 1;
            $prepare = Functions::$mysqli->prepare("INSERT INTO web_ranks_permissions (rank_id,permission_name) VALUES (?,?)");
            $prepare->bind_param('is', $this->id, $permission);
            $prepare->execute();
            return $this;
        }
    }

    function removePermission($permission) {
        if($this->id == null) {
            return null;
        }
        if($this->hasPermission($permission)) {
            unset($this->permissions[$permission]);
            $prepare = Functions::$mysqli->prepare("DELETE FROM web_ranks_permissions WHERE rank_id = ? AND permission_name = ?");
            $prepare->bind_param('is', $this->id, $permission);
            $prepare->execute();
            return $this;
        }
    }

    function create() {
        if($this->id != null) {
            return null;
        }

        $vars = array(
            'ingame_id',
            'name',
            'short',
            'colour',
            'colour_ingame',
            'priority',
            'is_staff',
            'is_upperstaff'
        );

        $questionmarks;

        foreach($vars as $key => $var) {
            $vars[$key] = $var;
            $questionmarks[$key] = '?';
        }

        $vars = implode(',', $vars);
        $questionmarks = implode(',', $questionmarks);
        
        $prepare = Functions::$mysqli->prepare("INSERT INTO core_ranks (".$vars.") VALUES (".$questionmarks.")");
        $prepare->bind_param('sssssiii',
            $this->data['ingame_id'],
            $this->data['name'],
            $this->data['short'],
            $this->data['colour'],
            $this->data['colour_ingame'],
            $this->data['priority'],
            $this->data['is_staff'],
            $this->data['is_upperstaff']
        );

        $prepare->execute();
        $insert_id = $prepare->insert_id;
        $this->id = $insert_id;
        $this->data['id'] = $insert_id;
    }

    function update() {
        if($this->id == null) {
            return null;
        }

        $vars = array(
            'ingame_id',
            'name',
            'short',
            'colour',
            'colour_ingame',
            'priority',
            'is_staff',
            'is_upperstaff',
        );

        foreach($vars as $key => $var) {
            $vars[$key] = $var.' = ?';
        }

        $vars = implode(',', $vars);

        $prepare = Functions::$mysqli->prepare("UPDATE core_ranks SET ".$vars." WHERE id = ? LIMIT 1");
        $prepare->bind_param('sssssiiii',
            $this->data['ingame_id'],
            $this->data['name'],
            $this->data['short'],
            $this->data['colour'],
            $this->data['colour_ingame'],
            $this->data['priority'],
            $this->data['is_staff'],
            $this->data['is_upperstaff'],
            $this->data['id']
        );
        $prepare->execute();
    }

    function delete() {
        if($this->id == null) {
            return null;
        }

        $prepare = Functions::$mysqli->prepare("DELETE FROM core_ranks WHERE id = ?");
        $prepare->bind_param('i', $this->id);
        $prepare->execute();

        $prepare_perm = Functions::$mysqli->prepare("DELETE FROM web_ranks_permissions WHERE rank_id = ?");
        $prepare_perm->bind_param('i', $this->id);
        $prepare_perm->execute();
    }
}