<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class RequestValidator
{
    private Request $request;

    private array $validValues;

    private array $bodyPattern;

    private array $whatsInRequestIsTooMuch;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function setRequestPattern(array $bodyPattern):void
    {
        $this->bodyPattern = $bodyPattern;
        $requestContent = json_decode($this->request->getContent(), true);
        if(!$requestContent){
            $this->validValues = [];
        }
        else
            foreach ($bodyPattern as $value){
                if(key_exists($value,$requestContent)) {
                    $this->validValues["$value"] = $requestContent["$value"];
                    unset($requestContent["$value"]);
                }
            }
        $this->whatsInRequestIsTooMuch = $requestContent;
    }
    public function allValuesPassed(): bool
    {
        if(count($this->whatsInRequestIsTooMuch)>0)
            return false;
        if(count($this->bodyPattern)===count($this->validValues))
            return true;
        return false;
    }
    public function atLeastOneValuesPassed(): bool
    {
        if(count($this->whatsInRequestIsTooMuch)>0)
            return false;
        if(count($this->validValues)>0)
            return true;
        return false;
    }
    public function getValidValues(): array
    {
        return $this->validValues;
    }
}