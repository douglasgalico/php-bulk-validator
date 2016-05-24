<?php

use DGalic\Validator\Validator;
use DGalic\Validator\ExampleValidator;


class CustomValidatorTest extends TestCase
{


    public function test_custom_validator_success()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new ExampleValidator();

        $return = $validator->execute($data);

        $this->assertEquals('success',$return['status']);
    }


    public function test_custom_validator_custom_error()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'foo' //fail!
        ];

        $validator = new ExampleValidator();

        $return = $validator->execute($data);

        $this->assertEquals('warning',$return['status']);
        $this->assertEquals('My Custom Message',$return['result']);
    }

}