<?php
$f = 'app/Views/users/index.php';
$c = file_get_contents($f);
$c = str_replace(
    'background:linear-gradient(135deg,#1a1a1a,#444)',
    'background:linear-gradient(135deg,#0f4c81,#0ea5e9)',
    $c
);
file_put_contents($f, $c);
echo "Fixed users/index.php avatar gradient\n";

// Also clean neutral tab labels in login/register — #1a1a1a as tab text is fine (dark on white bg)
// Those are actually correct dark text colors for active tab labels, leave them.
echo "All remaining refs are neutral text colors — OK to leave.\n";
