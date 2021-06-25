<?php


namespace App\Tests\App\Services;


use App\Services\RequestValidator;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    public function testItCorrectlyValidateJSON()
    {
        $validjson = "{\"comment\":\"das f d fin\",\"trask\":\"jhgjg\"}";
        $validator = new RequestValidator($validjson);
        $this->assertSame(true,$validator->isRequestValidJson());
        $invalidjson = "{\"comment\":\"dasda!!!!@@@@admfg fd gfd gdf ggg fdg dhtrn\"\"trask\": \"jhgjg\"}";
        $validator = new RequestValidator($invalidjson);
        $this->assertSame(false,$validator->isRequestValidJson());
    }

    /**
     * @param string $json
     * @param array $pattern
     * @param bool $allcorrect
     * @param bool $oneCorrect
     * @dataProvider getSpec
     */
    public function testItCorrectlyBuildValidValueArray(
        string $json, array $pattern, bool $allcorrect, bool $atleastOneCorrect
    )
    {
        $validator = new RequestValidator($json);
        $validator->setValidValuesArrayUsingPattern($pattern);
        if ($atleastOneCorrect) {
            foreach ($validator->getValidValues() as $name=>$value)
                $this->assertArrayHasKey($name,$validator->getValidValues());
             $this->assertSame(true, $validator->atLeastOneValuesPassed());
            if ($allcorrect){
                $this->assertSame(true, $validator->allValuesPassed());
                foreach ($pattern as $name)
                    $this->assertArrayHasKey($name,$validator->getValidValues());
            }
        }
        else{
            $this->assertSame(false, $validator->atLeastOneValuesPassed());
            $this->assertSame(false, $validator->allValuesPassed());
        }
    }
    public function testItCorrectlyBuildRedundantsArray(){
        $json = "{\"comment\":\"das f d fin\",\"trask\":\"jhgjg\",\"redundant\":\"jhgjg\"}";
        $validator = new RequestValidator($json);
        $validator->setValidValuesArrayUsingPattern(["comment","trask"]);
        $this->assertSame(false, $validator->atLeastOneValuesPassed());
        $this->assertSame(false, $validator->allValuesPassed());
    }


    public function getSpec()
    {
        return [
            [
                "{\"comment\":\"das f d fin\",\"trask\":\"jhgjg\"}",
                ["comment","trask",],
                true,
                true,
            ],
            [
                "{\"comment\":\"das f d fin\",\"trask\":\"jhgjg\"}",
                ["comment","trask","addition"],
                false,
                true,
            ],
            [
                "{\"comment\":\"das f d fin\",\"trask\":\"jhgjg\"}",
                ["other","other2"],
                false,
                false,
            ],
        ];
    }

}