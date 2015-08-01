<?php

class TtTpl {
    use Options;

    protected $tt, $d;

    function __construct(Tt $tt, $d, array $options) {
        $this->tt = $tt;
        $this->d = $d;
        $this->setOptions($options);
    }

    function __toString() {
        if (isset($this->options['html'])) {
            return $this->options['html'];
        } elseif (isset($this->options['path'])) {
            return $this->tt->getTpl($this->options['path'], $this->d);
        } else {
            throw new Exception('no path or html option');
        }
    }

}