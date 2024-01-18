<?php

class Log {

    private $id = null;

    private $log_data = array();

    function __construct($log_id = null) {

        $this->log_data['deleted'] = 0;
        $this->log_data['deleted_by'] = 0;
        $this->log_data['deleted_time'] = 0;

        if($log_id != null) {
            $prepare = Functions::$mysqli->prepare("SELECT * FROM web_logs WHERE id = ?");
            $prepare->bind_param('i', $log_id);
            $prepare->execute();
            $result = $prepare->get_result();
            $result = $result->fetch_object();

            $this->id = $result->id;
            $log_data = $result;
        }
    }

    function getCategory() { return $this->log_data['category']; }
    function setCategory($category) {
        $this->log_data['category'] = $category;
        return $this;
    }

    function getUser() { return $this->log_data['user']; }
    function setUser($user) {
        $this->log_data['user'] = $user;
        return $this;
    }

    function getTarget() { return $this->log_data['target']; }
    function setTarget($target) {
        $this->log_data['target'] = $target;
        return $this;
    }

    function getChangedWhat() { return $this->log_data['changed_what']; }
    function setChangedWhat($changed_what) {
        $this->log_data['changed_what'] = $changed_what;
        return $this;
    }

    function getChangedOld() { return $this->log_data['changed_old']; }
    function setChangedOld($changed_old) {
        $this->log_data['changed_old'] = $changed_old;
        return $this;
    }

    function getChangedNew() { return $this->log_data['changed_new']; }
    function setChangedNew($changed_new) {
        $this->log_data['changed_new'] = $changed_new;
        return $this;
    }

    function getTime() { return $this->log_data['time']; }
    function setTime($time) {
        $this->log_data['time'] = $time;
        return $this;
    }

    function getDeleted() { return $this->log_data['deleted']; }
    function setDeleted($deleted) {
        $this->log_data['deleted'] = $deleted;
        return $this;
    }

    function getDeletedBy() { return $this->log_data['deleted_by']; }
    function setDeletedBy($deleted_by) {
        $this->log_data['deleted_by'] = $deleted_by;
        return $this;
    }

    function getDeletedTime() { return $this->log_data['deleted_time']; }
    function setDeletedTime($deleted_time) {
        $this->log_data['deleted_time'] = $deleted_time;
        return $this;
    }

    function update() {
        if($this->id == null) {
            return null;
        }

        $prepare = Functions::$mysqli->prepare("UPDATE web_logs SET category = ?, user = ?, target = ?, changed_what = ?, changed_old = ?, changed_new = ?, time = ?, deleted = ?, deleted_by = ?, deleted_time = ? WHERE id = ?");
        $prepare->bind_param('sissssiiiii', $this->log_data['category'], $this->log_data['user'], $this->log_data['target'], $this->log_data['changed_what'], $this->log_data['changed_old'], $this->log_data['changed_new'], $this->log_data['time'], $this->log_data['deleted'], $this->log_data['deleted_by'], $this->log_data['deleted_time'], $this->log_data['id']);
        $prepare->execute();
    }

    function create() {
        if($this->id != null) {
            return null;
        }

        $prepare = Functions::$mysqli->prepare("INSERT INTO web_logs (category,user,target,changed_what,changed_old,changed_new,time,deleted,deleted_by,deleted_time) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $prepare->bind_param('sissssiiii', $this->log_data['category'], $this->log_data['user'], $this->log_data['target'], $this->log_data['changed_what'], $this->log_data['changed_old'], $this->log_data['changed_new'], $this->log_data['time'], $this->log_data['deleted'], $this->log_data['deleted_by'], $this->log_data['deleted_time']);
        $prepare->execute();
        $insert_id = $prepare->insert_id;
        $this->id = $insert_id;
        $this->log_data['id'] = $insert_id;
    }
}