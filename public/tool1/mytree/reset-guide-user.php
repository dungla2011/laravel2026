<?php

use App\Models\GiaPha;

require_once '../../index.php';

if (! isSupperAdmin__()) {
    exit('Not admin');
}

$user = \App\Models\User::where('email', 'mytreevn2015@gmail.com')->first();
if (! $user) {
    echo "<br/>\n Deleted? ";

    return;
}

echo "<br/>\n Delete All uid $user->id: ";

GiaPha::withTrashed()->where('user_id', 0)->forceDelete();
GiaPha::where('user_id', $user->id)->forceDelete();

//$user->forceDelete();

echo "<br/>\n DONE!";
