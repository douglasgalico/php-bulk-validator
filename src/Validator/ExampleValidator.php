<?php
namespace DGalic\Validator;
use DGalic\Validator\Contracts\Validator as ValidatorContract;
use DGalic\Validator\Validator as BaseValidator;

class ExampleValidator extends BaseValidator implements ValidatorContract
{
    protected $type = 'warning';

    public function action($data, &$message)
    {
        if($data['hello'] != 'world') {
            $message = 'My Custom Message';
            return false;
        }
        return true;
    }

}