<?php

function makePage(string $page, array $args): void {
    extract($args);

    require_once COMPONENTS . 'header.php';
    
    require_once VIEWS . $page . '.php';

    require_once COMPONENTS . 'footer.php';
}