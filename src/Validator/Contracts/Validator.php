<?php
namespace DGalic\Validator\Contracts;

interface Validator
{
    public function execute($data);

    public function action($data, &$message);
}