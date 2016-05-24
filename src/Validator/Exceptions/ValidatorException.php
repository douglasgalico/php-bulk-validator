<?php

namespace DGalic\Validator\Exceptions;

class ValidatorException extends \Exception
{
    protected $type;

    public function __construct($message, $type, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->type = $type;
    }

    public function getType(){
        return $this->type;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}