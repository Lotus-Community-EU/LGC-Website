<?php

class DiscordWebhook {

    private $webhook = '';

    private $creator = 0;

    private $username = null;
    private $avatar = 'https://lotuscommunity.eu/assets/images/discord_webhook_default.jpg';

    private $message = null;

    private $embeds = null;

    private $type = 'rich';

    function __construct($webhook) {
        if(strlen($webhook) > 1) {
            if(strpos($webhook,'?wait=true') === false) {
                $webhook .= '?wait=true';
            }
            $this->webhook = $webhook;
        }
    }

    function setWebhook($webhook) {
        if(strpos($webhook,'?wait=true') === false) {
            $webhook .= '?wait=true';
        }
        $this->webhook = $webhook;
        return $this;
    }

    function setCreator($id) {
        $this->creator = $id;
        return $this;
    }

    function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    function setAvatar($url) {
        $this->avatar = $url;
        return $this;
    }

    static function replaceNewlines($string) {
        str_replace('\n', PHP_EOL, $string);
        return $string;
    }

    function update($id) {
        $webhook_url = str_replace('?wait=true', '', $this->webhook).'/messages/'.$id;

        $content = [
            'username' => $this->username,
            'avatar_url' => $this->avatar,

            'content' => $this->message == null ? '' : $this->message
        ];

        $headers = array('Content-Type: application/json'); 

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'PATCH');
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($content));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = (array) json_decode($response);
        var_dump($response);

        $time = gmdate('U');
        $message_id = $response['id'];
        $message = $this->message;

        $prepare = Functions::$mysqli->prepare("UPDATE core_webhook_messages SET message_content = ? WHERE message_id = ?");
        $prepare->bind_param('si', $message, $message_id);
        $prepare->execute();

        return $response;
    }

    function create() {
        global $user;

        $webhook_url = $this->webhook;

        $content = [
            'username' => $this->username,
            'avatar_url' => $this->avatar,

            'content' => $this->message == null ? '' : $this->message
        ];

        $headers = array('Content-Type: application/json'); 

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $webhook_url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($content));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = (array) json_decode($response);
        
        $time = gmdate('U');
        $message_id = $response['id'];
        $webhook = $this->webhook;
        $message = $this->message;
        $user_id = $this->creator;

        $prepare = Functions::$mysqli->prepare("INSERT INTO core_webhook_messages (message_id,message_content,webhook,timestamp,send_by) VALUES (?,?,?,?,?)");
        $prepare->bind_param('sssii', $message_id, $message, $webhook, $time, $user_id);
        $prepare->execute();

        return $response;

    }

    function delete($id) {
        $webhook_url = str_replace('?wait=true', '', $this->webhook).'/messages/'.$id;

        $headers = array('Content-Type: application/json'); 

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'DELETE');
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = (array) json_decode($response);

        $prepare = Functions::$mysqli->prepare("DELETE FROM core_webhook_messages WHERE message_id = ?");
        $prepare->bind_param('s', $id);
        $prepare->execute();

        return $response;
    }
}