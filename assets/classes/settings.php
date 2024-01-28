<?php

class Settings {

    private $data;

    function __construct() {
        $prepare = Functions::$mysqli->prepare("SELECT * FROM web_settings");
        $prepare->execute();
        $results = $prepare->get_result();
        $results = $results->fetch_all(MYSQLI_ASSOC);

        foreach($results as $result) {
            $this->data[$result['code']] = $result['value'];
        }

    }

    function getPasswordResetSubject() { return $this->data['password_reset_email_subject']; }
    function setPasswordResetSubject($subject) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET password_reset_email_subject = ?");
        $prepare->bind_param('s', $subject);
        $prepare->execute();
        return $this;
    }

    function getPasswordResetText() { return $this->data['password_reset_email_text']; }
    function setPasswordResetText($text) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET password_reset_email_text = ?");
        $prepare->bind_param('s', $text);
        $prepare->execute();
        return $this;
    }

    function getCopyrightShow() { return $this->data['copyright_show']; }
    function setCopyrightShow($show) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET copyright_show = ?");
        $prepare->bind_param('i', $show);
        $prepare->execute();
        return $this;
    }

    function getCopyrightText() { return $this->data['copyright_text']; }
    function setSCopyrightText($text) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET copyright_text = ?");
        $prepare->bind_param('s', $text);
        $prepare->execute();
        return $this;
    }

    function getShowMCHeads() { return $this->data['show_mcheads']; }
    function setShowMCHeads($show) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET show_mcheads = ?");
        $prepare->bind_param('i', $show);
        $prepare->execute();
        return $this;
    }

    function getUsernameChangeUnit() { return $this->data['username_change_unit']; }
    function setUsernameChangeUnit($unit) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET username_change_unit = ?");
        $prepare->bind_param('s', $unit);
        $prepare->execute();
        return $this;
    }

    function getUsernameChangeValue() { return $this->data['username_change_value']; }
    function setUsernameChangeValue($value) {
        $prepare = Functions::$mysqli->prepare("UPDATE web_settings SET username_change_value = ?");
        $prepare->bind_param('i', $value);
        $prepare->execute();
        return $this;
    }

}