<?php

namespace controllers;

class HomepageController {
    static public function makeHome() {
        makePage('homepage', [
            'tile' => 'Luming - Homepage',
            'erros' => []
        ]);
    }
}