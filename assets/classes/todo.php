<?php

class ToDo {

    private $id = null;
    private $data = null;
    private $content = null;

    function __construct($id = null) {
        if($id != null) {

            $prepare = Functions::$mysqli->prepare("SELECT * FROM web_todo_lists WHERE id = ?");
            $prepare->bind_param('i', $id);
            $prepare->execute();

            $result = $prepare->get_result();
            if($result->num_rows > 0) {
                $result = $result->fetch_array();
                
                $this->id = $result['id'];
                $this->data = $result;

                $prepare_content = Functions::$mysqli->prepare("SELECT * FROM web_todo_content WHERE list_id = ?");
                $prepare_content->bind_param('i', $this->id);
                $prepare_content->execute();

                $result_content = $prepare_content->get_result();
                if($result_content->num_rows > 0) {
                    $this->content = $result_content->fetch_all(MYSQLI_ASSOC);
                }
                else {
                    $this->content = null;
                }
            }
            else {
                return null;
            }
        }
    }

    function getName() { return $this->data['todo_name']; }
    function setName($name) {
        $this->data['todo_name'] = $name;
        return $this;
    }

    function getDescription() { return $this->data['todo_description']; }
    function setDescription($description) {
        $this->data['todo_description'] = $description;
        return $this;
    }

    function getSort() { return $this->data['todo_sort']; }
    function setSort($sort) {
        $this->data['todo_sort'] = $sort;
        return $this;
    }

    function getHidden() { return $this->data['todo_hidden']; }
    function setHidden($hidden) {
        $this->data['todo_hidden'] = $hidden;
        return $this;
    }

    function getSubFrom() { return $this->data['sub_from']; }
    function setSubFrom($sub_from) {
        $this->data['sub_from'] = $sub_from;
        return $this;
    }

    function getCreatedBy() { return $this->data['created_by']; }
    function setCreatedBy($created_by) {
        $this->data['created_by'] = $created_by;
        return $this;
    }

    function getCreatedAt() { return $this->data['created_at']; }
    function setCreatedAt($created_at) {
        $this->data['created_at'] = $created_at;
        return $this;
    }

    static function getAllLists() {
        $prepare = Functions::$mysqli->prepare("SELECT * FROM web_todo_lists WHERE id > 0 AND sub_from = '-1' ORDER BY todo_sort DESC");
        $prepare->execute();

        $result = $prepare->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getSubLists() {
        if($this->id == null) {
            return null;
        }

        $prepare = Functions::$mysqli->prepare("SELECT * FROM web_todo_lists WHERE sub_from = ?");
        $prepare->bind_param('i', $this->id);
        $prepare->execute();

        $result = $prepare->get_result();
        if($result->num_rows > 0) {
            $result = $result->fetch_all();

            return $result;
        }
        else {
            return null;
        }
    }

    function getAllComments() {
        if($this->id == null) {
            return null;
        }

        return $this->content;
    }

    function getComment($comment) {
        if($this->id == null) {
            return null;
        }

        return $this->content[$comment];
    }

    function create() {
        if($this->id != null) {
            return null;
        }

        $prepare = Functions::$mysqli->prepare("INSERT INTO web_todo_lists (todo_name,todo_description,todo_sort,todo_hidden,sub_from,created_by,created_at) VALUES (?,?,?,?,?,?,?)");
        $prepare->bind_param('ssiiiii', $this->data['todo_name'], $this->data['todo_description'], $this->data['todo_sort'], $this->data['todo_hidden'], $this->data['sub_from'], $this->data['created_by'], $this->data['created_at']);
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
            'todo_name',
            'todo_description',
            'todo_sort',
            'todo_hidden',
            'sub_from',
            'created_by',
            'created_by'
        );

        foreach($vars as $key => $var) {
            $vars[$key] = $var.' = ?';
        }

        $vars = implode(',', $vars);

        $prepare = Functions::$mysqli->prepare("UPDATE web_todo_lists SET ".$vars." WHERE id = ? LIMIT 1");

        $prepare->bind_param('ssiiiiii',
            $this->data['todo_name'],
            $this->data['todo_description'],
            $this->data['todo_sort'],
            $this->data['todo_hidden'],
            $this->data['sub_from'],
            $this->data['created_by'],
            $this->data['created_at'],
            $this->data['id']
        );
        $prepare->execute();
    }
}