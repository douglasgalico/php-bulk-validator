<?php
namespace DGalic\Validator;

use DGalic\Validator\Contracts\Validator as ValidatorContract;


class BulkValidator
{
    protected $validators;
    protected $noErrorLabel;
    protected $errorPriority;
    protected $unknowPriority = 100;

    /**
     * BulkValidator constructor.
     * @param string $noErrorLabel
     * @param array $errorPriority
     */
    public function __construct($noErrorLabel = 'success', $errorPriority = [])
    {
        $this->noErrorLabel = $noErrorLabel;
        if(!empty($errorPriority)){
            $this->errorPriority = $this->processErrorPriority($errorPriority);
        }

        if(is_null($this->errorPriority))
        {
            if(!empty($errorPriority)){
                $this->errorPriority = $this->processErrorPriority($errorPriority);
            }else{
                $this->errorPriority = $this->processErrorPriority([
                    'error','warning','info'
                ]);
            }
        }
    }

    /**
     * @param $val
     */
    public function append($val)
    {
        if($val instanceof ValidatorContract){
            $this->validators[] = $val;
        }
        if(is_array($val)){
            foreach($val as $expected_validator){
                if($expected_validator instanceof ValidatorContract){
                    $this->validators[] = $expected_validator;
                }
            }
        }
    }

    public function reset()
    {
        $this->validators = [];
    }

    public function execute($data)
    {

       if(empty($this->validators) || is_null($this->validators)){
            throw new \Exception('Validators not appended or empty');
        }

        $final_result = [
            'status' => $this->noErrorLabel,
            'errors' => []
        ];
        $current_priority = null;

        foreach($this->validators as $validator)
        {
            $validator_result = $validator->execute($data);

            if($validator_result['status'] !=  $this->noErrorLabel)
            {
                if(is_null($current_priority)){
                    $current_priority = $this->errorToPriority($validator_result['status']);
                }else{
                    $current_priority = ($current_priority > $this->errorToPriority($validator_result['status'])) ? $this->errorToPriority($validator_result['status']) : $current_priority;
                }

                if($validator_result['result'] != false){
                    if(is_array($validator_result['result'])){
                        foreach($validator_result['result'] as $res_message){
                            $final_result['errors'][] = $res_message;
                        }
                    }
                    $final_result['errors'][] = $this->format_result($validator_result);
                }


            }
        }

        if(!is_null($current_priority))
        {

          $final_result['status'] = $this->priorityToError($current_priority);

        }

        return $final_result;

    }

    protected function format_result($result)
    {
        return $result['result'];
    }

    private function processErrorPriority($errorlist = [])
    {
        $ret = [];
        foreach($errorlist as $key => $error){
            $ret[$error] = $key;
        }
        return $ret;
    }

    private function errorToPriority($error)
    {
        if(!array_key_exists($error,$this->errorPriority)){
            return $this->unknowPriority;
        }
        return $this->errorPriority[$error];
    }

    private function priorityToError($priority)
    {
        return array_search($priority, $this->errorPriority);
    }
}

