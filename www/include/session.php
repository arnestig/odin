<?php

# (almost) always include this file first
session_start();
if ( ! isset( $_SESSION[ 'user_data' ] ) ) {
    header('Location: index.php');
    exit;
}

?>
