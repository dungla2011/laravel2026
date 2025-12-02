<?php

function getUserFromHeader($headers)
{
    $tkStr = '_tglx863516839=';
    $tk = '';
    foreach ($headers as $header) {
        if (strpos($header, 'GET /') === 0) {
            $tk = explode('tkx=', $header)[1];
            $tk = explode(" ", $tk)[0];
            echo "\n FOUND TK from GET..." .' - '. nowyh();;
            break;
        }
        if (strpos($header, 'Cookie: ') === 0) {
            $cookie = trim(substr($header, 8));
            // Extract the specific cookie _tglx863516839
            $cookie = explode(';', $cookie);
            foreach ($cookie as $c) {
                $c = trim($c);  // Remove leading/trailing spaces
                if (str_starts_with($c, $tkStr)) {
                    $tk = str_replace($tkStr, '', $c);
                    echo "\n FOUND TK from Cookie..." .' - '. nowyh();;
                    break;
                }
            }
        }
        if ($tk)
            break;
    }
    echo "\n\n Get user from Token: \n";
    $user = \App\Models\User::where("token_user", $tk)->first();
    return $user;
}
