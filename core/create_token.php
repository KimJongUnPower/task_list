<?php

        function touch_token($length=32){
        $token = '';
        for ($i=0; $i<$length; $i++){
                $token .= chr(mt_rand(35,126)); //символ из ASCII-table
        }

        return $token;
    }

    function touch_letter_token($length=32){
        $token = '';
        $array_sim = array('q', 'Q', 'w', 'W', 'e', 'E', 'r', 'R', 't', 'T', 'y', 'Y', 'u', 'U', 'i', 'I', 'o', 'O', 'p', 'P', 'a', 'A', 's', 'S', 'd', 'D', 'f', 'F', 'g', 'G', 'h', 'H', 'j', 'J', 'k', 'K', 'l', 'L', 'z', 'Z', 'x', 'X', 'c', 'C', 'v', 'V', 'b', 'B', 'n', 'N', 'm', 'M');
        for ($i=0; $i<$length; $i++){
            $token .= $array_sim[array_rand($array_sim, 1)];
        }

        return $token;
    }