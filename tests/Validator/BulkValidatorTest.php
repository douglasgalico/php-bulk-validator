<?php
use DGalic\Validator\BulkValidator;
use DGalic\Validator\Validator;

class BulkValidatorTest extends TestCase
{

    public function test_validator_creation()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validators = [
          new Validator(),
          new Validator(),
          new Validator()
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);
        $this->assertEquals('success',$return['status']);
    }

    public function test_validator_simple_fails_error_status()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validators = [
            new Validator(['action' => function($data,&$message){
               if(!isset($data['foo'])){ $message = 'Key not found: foo'; return false; }
            }]),
            new Validator(['action' => function($data,&$message){
                if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
            }]),
            new Validator(['action' => function($data,&$message){
                if(!isset($data['lorem'])){ $message = 'Key not found: lorem'; return false; }
            }]),
            new Validator(['action' => function($data,&$message){
                if(!isset($data['hello'])){ $message = 'Key not found: hello'; return false; }
            }])
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);

        $this->assertEquals('error',$return['status']);
    }

    public function test_validator_simple_fails_error_count()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validators = [
            new Validator(['action' => function($data,&$message){
                if(!isset($data['foo'])){ $message = 'Key not found: foo'; return false; }
            }]),
            new Validator(['action' => function($data,&$message){
                if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
            }]),
            new Validator(['action' => function($data,&$message){
                if(!isset($data['lorem'])){ $message = 'Key not found: lorem'; return false; }
            }]),
            new Validator(['action' => function($data,&$message){
                if(!isset($data['hello'])){ $message = 'Key not found: hello'; return false; }
            }])
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);
        $this->assertEquals(3,count($return['errors']));
    }


    public function test_validator_fails_priority()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validators = [
            new Validator(['type' => 'info',
                'action' => function($data,&$message){
                    if(!isset($data['foo'])){ $message = 'Key not found: foo'; return false; }
                }]),
            new Validator(['type' => 'error',
                'action' => function($data,&$message){
                    if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }

                }]),
            new Validator(['type' => 'warning',
                'action' => function($data,&$message){
                    if(!isset($data['lorem'])){ $message = 'Key not found: lorem'; return false; }

                }])
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);
        $this->assertEquals('error',$return['status']);
    }

    public function test_validator_fails_priority_2()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validators = [
            new Validator(['type' => 'info',
                'action' => function($data,&$message){
                    if(!isset($data['foo'])){ $message = 'Key not found: foo'; return false; }
                }]),
            new Validator(['type' => 'warning',
                'action' => function($data,&$message){
                    if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
                }]),
            new Validator(['type' => 'info',
                'action' => function($data,&$message){
                    if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
                }]),
            new Validator(['type' => 'info',
                'action' => function($data,&$message){
                    if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
                }]),
            new Validator(['type' => 'warning',
                'action' => function($data,&$message){
                    if(!isset($data['hello'])){ $message = 'Key not found: hello'; return false; }
                }])
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);
        $this->assertEquals('warning',$return['status']);
    }

    public function test_validator_fails_with_custom_validator()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'foo' //force fails
        ];

        $validators = [
            new Validator([
                    'type' => 'info',
                    'action' => function($data,&$message){
                        if(!isset($data['foo'])){ $message = 'Key not found: foo'; return false; }
                        return true;
                    }]),
            new Validator([
                    'type' => 'info',
                    'action' => function($data,&$message){
                        if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
                        return true;
                    }]),
            new Validator([
                    'type' => 'info',
                    'action' => function($data,&$message){
                        if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
                        return true;
                    }]),
            new Validator([
                    'type' => 'info',
                    'action' => function($data,&$message){
                        if(!isset($data['bar'])){ $message = 'Key not found: bar'; return false; }
                        return true;
                    }]),
            new Validator([
                    'type' => 'warning',
                    'action' => function($data,&$message){
                        if(!isset($data['hello'])){ $message = 'Key not found: hello'; return false; }
                        return true;
                    }]),
            new \DGalic\Validator\ExampleValidator()
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);
        $this->assertEquals(5,count($return['errors']));
        $this->assertEquals('warning',$return['status']);
        $this->assertEquals('My Custom Message',$return['errors'][4]);
    }

    public function test_validator_false_error_message()
    {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'hello' => 'world'
        ];

        $validators = [
            new Validator(['type' => 'error',
                'action' => function($data,&$message){
                    $message = false; return false;
                }])
        ];

        $validator = new BulkValidator();
        $validator->append($validators);
        $return = $validator->execute($data);
        $this->assertEquals('error',$return['status']);
        $this->assertEquals(0,count($return['errors']));
    }

}