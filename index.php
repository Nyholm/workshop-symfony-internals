<?php

if (isset($_GET['page']) && $_GET['page'] === 'foo') {
    echo "Foo page <br>";
} else {
    echo "Welcome to index! <br>";
}

if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
    echo "(admin stuff)";
}
