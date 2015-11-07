<?php

    /**
     * Retorna o IP do usuário
     * @return string
     */
    function getUserIP() {
        
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            return $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            return $forward;
        } else if (filter_var($remote, FILTER_VALIDATE_IP)) {
            return $remote;
        } else {
            return null;
        }
        
    }
    