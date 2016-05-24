<?php
namespace DGalic\Validator;

use DGalic\Validator\Exceptions\ValidatorException;
use DGalic\Validator\Contracts\Validator as ValidatorContract;

class Validator implements ValidatorContract
{
    protected $overrideAction;
    protected $type;
    protected $message;
    protected $status;
    protected $result;

    public function __construct(array $args = array())
    {
        if(is_null($this->type))
        {
            $this->type = !isset($args['type']) ? 'error' : $args['type'];
        }
        if(is_null($this->message))
        {
            $this->message = !isset($args['message']) ? 'Undefined Validation Error' : $args['message'];
        }
        if(isset($args['action']) && is_callable($args['action']))
        {
            $this->overrideAction = $args['action'];
        }
    }

    public function execute($data)
    {
        try {
           if ($this->action($data,$this->message) === false){
               throw new ValidatorException($this->message,$this->type);
           };
           $this->status = 'success';
           $this->result = null;
        }
        catch(ValidatorException $e) {
            $this->status = $e->getType();
            $this->result = $e->getMessage();
        }
        return [
            'status' => $this->status,
            'result' => $this->result
        ];
    }

    public function action($data, &$message)
    {
        if (is_callable($this->overrideAction)) {
           return $this->__call('overrideAction',[$data,&$message]);
        }

        return true;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if(is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        }
        throw new \Exception('Não foi possível invocar o método solicitado!');
    }

}

