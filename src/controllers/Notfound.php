<?php

function makeNotFound(): void {
    makePage('not_found', [
        'title' => '404 - Página não encontrada'
    ]);
}