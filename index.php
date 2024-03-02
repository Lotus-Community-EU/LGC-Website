<?php

session_start();

ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/*
    Lotus Gaming Community Website - Coded by SpoonyUK

    Genutzte Technologien:
        - PHP (https://www.php.net/)
            Version: 8.2.4
        - Bootstrap (https://getbootstrap.com/)
            Version: 5.3.2
        - JQuery (https://jquery.com/)
            Version: 3.7.1
        - JQuery Cookie (By Carl Hartl, https://github.com/carhartl/jquery-cookie)
            Version: 1.4.1
        - Cookie Consent (By Wruczek, https://github.com/Wruczek/Bootstrap-Cookie-Alert)
            Version: 1.2.2
        - WYSIWYG-Editor (https://summernote.org/)
            Version: 0.8.18
		- SCSS (https://sass-lang.com/)
			Version: 1.12

*/

require_once('config.php');
require_once('assets/classes/functions.php');
require_once('assets/classes/pagemanager.php');

?>