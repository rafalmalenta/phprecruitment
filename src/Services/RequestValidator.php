<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\Request;

class RequestValidator
{
    private Request $request;

    private array $validValues;

    private array $bodyPattern;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function init(array $bodyPattern)
    {
        $this->bodyPattern = $bodyPattern;
        $requestContent = json_decode($this->request->getContent(), true);
        foreach ($bodyPattern as $value){
            if(key_exists($value,$requestContent)) {
                $this->validValues["$value"] = $requestContent["$value"];
                unset($requestContent["$value"]);
            }
        }
    }
    public function allValuesPassed(): ?array
    {
        if(count($this->bodyPattern)===count($this->validValues))
            return $this->validValues;
        return null;
    }
    public function atLeastOneValuesPassed(): ?array
    {
        if(count($this->validValues)>0)
            return $this->validValues;
        return null;
    }
}