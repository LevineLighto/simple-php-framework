<?php

namespace App\Controller;

class Welcome {
    public function index() {
        return layout('sample', ['view' => 'sample.index', 'viewData' => []]);
    }
}