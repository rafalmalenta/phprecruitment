<?php


namespace App\Services;


class RequestValidator
{
    private string $json;

    private array $validValues;

    private array $bodyPattern;

    private array $requestContent;

    private array $whatsInRequestIsTooMuch;

    public function __construct(string $json)
    {
        $this->json = $json;
    }
    public function isRequestValidJson(): bool
    {
        if(gettype(json_decode($this->json, true)) === 'array') {
//            dump(json_decode($this->json, true));
            $this->requestContent = json_decode($this->json, true);
            return true;
        }
        return false;
    }
    public function setValidValuesArrayUsingPattern(array $bodyPattern):void
    {
        $this->bodyPattern = $bodyPattern;

        if ($this->isRequestValidJson()) {
            foreach ($bodyPattern as $value) {
                if (key_exists($value, $this->requestContent)) {
                    $this->validValues["$value"] = $this->requestContent["$value"];
                    unset($this->requestContent["$value"]);
                }
            }
        }
        else{
            $this->validValues = [];
        }
        $this->whatsInRequestIsTooMuch = $this->requestContent ?? [] ;

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
    public function getRequestContent(): ?array
    {
        return $this->requestContent;
    }
}