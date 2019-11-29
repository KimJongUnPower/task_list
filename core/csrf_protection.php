<?php

    require_once 'create_token.php';

    function check_csrf() {
        if (!array_key_exists('csrf', $_POST) or $_POST['csrf'] !== $_SESSION['csrf']) {
            return 0;
        }
        return 1;
    }

    function csrf_html() {
        echo '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'" />';
    }

    function gen_csrf($replace = false) {
        if ($replace or !array_key_exists('csrf', $_SESSION)) {
            $_SESSION['csrf'] = touch_token(128);
        }
    }