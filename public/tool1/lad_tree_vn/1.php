<?php

require_once __DIR__.'/../../index.php';

echo \App\Models\User::checkGuestPermissionRoute('api.member-tree-mng.tree-index');
echo "<br/>\n";
echo \App\Models\User::checkGuestPermissionRoute('api.member-tree-mng.tree-index1');
echo "<br/>\n";

echo \App\Models\User::checkGuestPermissionRoute('api.member-tree-mng.index');
echo "<br/>\n";
echo \App\Models\User::checkGuestPermissionRoute('admin.categories.create');
