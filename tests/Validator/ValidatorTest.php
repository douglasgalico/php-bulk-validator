<?php

use DGalic\Validator\Validator;


class ValidatorTest extends TestCase
{

    /*
     * Test Validatior Creation - Blank Action, default type and message.
     */
    public function test_validator_creation()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator();
        //Check basic methods and attributes
        $this->assertClassHasAttribute('overrideAction', get_class($validator));
        $this->assertClassHasAttribute('result', get_class($validator));
        $this->assertClassHasAttribute('status', get_class($validator));
        $this->assertTrue(method_exists($validator, 'execute'));
        $this->assertTrue(method_exists($validator, 'action'));
        //Assert class type
        $this->assertInstanceOf('DGalic\Validator\Validator', $validator);
    }

      /*
      * Test Blank Validatior Succes Return
      */
    public function test_blank_validator_success_return()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator();
        $return = $validator->execute($data);
        $this->assertArrayHasKey('status',$return);
        $this->assertArrayHasKey('result',$return);
        $this->assertEquals('success',$return['status']);

    }

    /*
     * Test Fails Injected Action Return
     */
    public function test_injected_action_validator_fail_return(){

        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator([
            'action' => function($inner_data, &$validator_message){
                //force fails!
                return false;
            }
        ]);

        $return = $validator->execute($data);
        $this->assertEquals('error',$return['status']);
        $this->assertEquals('Undefined Validation Error',$return['result']);
    }


    /*
     * Test Custom Action Validator Success
     */
    public function test_custom_action_validation()
    {

        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator([
            'action' => function($inner_data, &$validator_message)
            {
                if($inner_data['hello'] != 'world'){
                    return false;
                }
                return true;
            }
        ]);

        $return = $validator->execute($data);
        $this->assertEquals('success',$return['status']);

    }

    /*
     * Test Custom Message Argument
     */
    public function test_validation_fails_custom_message_argument()
    {

        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator([
            'action' => function($inner_data, &$validator_message)
            {
                return false;
            },
            'message' => 'Validation Custom Error'
        ]);

        $return = $validator->execute($data);
        $this->assertEquals('error',$return['status']);
        $this->assertEquals('Validation Custom Error',$return['result']);

    }

    /*
      * Test Custom Message Injected
      */
    public function test_validation_fails_custom_message_inside_action()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator([
            'action' => function($inner_data, &$validator_message)
            {
                $validator_message = 'Validation Custom Error';
                return false;
            }
        ]);

        $return = $validator->execute($data);
        $this->assertEquals('error',$return['status']);
        $this->assertEquals('Validation Custom Error',$return['result']);

    }

    /*
    * Test Custom Type
    */
    public function test_validation_custom_type()
    {

        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validator = new Validator([
            'action' => function($inner_data, &$validator_message)
            {
                return false;
            },
            'type' => 'warning'
        ]);

        $return = $validator->execute($data);
        $this->assertEquals('warning',$return['status']);
    }


}