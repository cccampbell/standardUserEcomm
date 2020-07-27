<?php

namespace App\TwigExtension;

use Slim\Csrf\Guard;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class CsrfExtension extends AbstractExtension {

    protected $csrf;

    public function __construct(Guard $csrf) {

        $this->csrf = $csrf;
        
    }

    public function getFunctions() {

        // return array of Functions you want to implement
        return [
            // give it a name 'csrf' and a callable [this instance and the method name]
            new TwigFunction('csrf', [$this, 'csrf'])
        ];

    }

    public function csrf() {

        return '
        <input type="hidden" name="'. $this->csrf->getTokenNameKey() .'" value="'. $this->csrf->getTokenName() .'">
        <input type="hidden" name="'. $this->csrf->getTokenValueKey() .'" value="'. $this->csrf->getTokenValue() .'">
        ';

    }

}