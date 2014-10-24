<?php

class SflmCli {

    protected $frontend;

    //function __construct($frontend) {
    //    $this->frontend = $frontend;
    //    Sflm::setFrontendName($this->frontend);
    //}

    function paths() {
        die2(Sflm::frontend('js')->getPaths());
    }

}