<?php

namespace App;

class Request
{
    public function getParam($code, $defaut = null)
    {
        if (isset($_POST[$code])) {
            return $_POST[$code];
        }

        if (isset($_GET[$code])) {
            return $_GET[$code];
        }

        return $defaut;
    }
}