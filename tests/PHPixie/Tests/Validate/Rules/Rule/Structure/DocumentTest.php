<?php

namespace PHPixie\Tests\Validate\Rules\Rule\Data;

/**
 * @coversDefaultClass \PHPixie\Validate\Rules\Rule\Data\Document
 */
class DocumentTest extends \PHPixie\Tests\Validate\Rules\Rule\DataTest
{
    /**
     * @covers ::allowExtraFields
     * @covers ::extraFieldsAllowed
     * @covers ::<protected>
     */
    public function testAllowExtraFields()
    {
        $this->assertSame(false, $this->rule->extraFieldsAllowed());

        $this->assertSame($this->rule, $this->rule->allowExtraFields());
        $this->assertSame(true, $this->rule->extraFieldsAllowed());

        $this->assertSame($this->rule, $this->rule->allowExtraFields(false));
        $this->assertSame(false, $this->rule->extraFieldsAllowed());
    }

    /**
     * @covers ::setFieldRule
     * @covers ::fieldRules
     * @covers ::<protected>
     */
    public function testFieldRules()
    {
        $rules = array();
        for($i=0; $i<2; $i++) {
            $rule = $this->getRule();
            $rules['pixie'.$i]= $rule;
            $result = $this->rule->setFieldRule('pixie'.$i, $rule);
            $this->assertSame($this->rule, $result);
        }

        $this->assertSame($rules, $this->rule->fieldRules());
    }

    /**
     * @covers ::field
     * @covers ::addField
     * @covers ::<protected>
     */
    public function testField()
    {
        $this->fieldTest(false, false);
        $this->fieldTest(false, true);
        $this->fieldTest(true, false);
        $this->fieldTest(true, true);
    }

    protected function fieldTest($isAdd, $withCallback)
    {
        $rule = $this->prepareValue();

        if($isAdd) {
            $method = 'addField';
            $expect = $rule;
        }else{
            $method = 'field';
            $expect = $this->rule;
        }

        $args = array();
        if($withCallback) {
            $args[]= $this->ruleCallback($rule);
        }

        $this->assertRuleBuilder($method, $args, $rule, $isAdd);
    }

    protected function assertRuleBuilder($method, $args, $rule, $isAdd)
    {
        $expect = $isAdd ? $rule : $this->rule;

        array_unshift($args, 'pixie');
        $result = call_user_func_array(array($this->rule, $method), $args);
        $this->assertSame($expect, $result);

        $rules = $this->rule->fieldRules();
        $this->assertSame($rule, end($rules));
        $this->assertSame('pixie', key($rules));
    }

    /**
     * @covers ::validate
     * @covers ::<protected>
     */
    public function testValidate()
    {
        $this->validateTest(false);

        $this->validateTest(true, false, false);
        $this->validateTest(true, true, false);
        $this->validateTest(true, false, true);
        $this->validateTest(true, true, true);
    }

    protected function validateTest($isArray, $allowExtraFields = false, $withExtraFields = false)
    {
        $this->rule = $this->rule();
        list($value, $result) = $this->prepareValidateTest(
            $isArray,
            $allowExtraFields,
            $withExtraFields
        );
        $this->rule->validate($value, $result);
    }

    protected function prepareValidateTest($isArray, $allowExtraFields, $withExtraFields)
    {
        $result = $this->getResultMock();
        $resultAt = 0;

        if(!$isArray) {
            $this->method($result, 'addArrayTypeError', null, array(), $resultAt++);
            return array(5, $result);
        }

        $values = array();

        $this->rule->allowExtraFields($allowExtraFields);

        $extraKeys = array('stella', 'blum');

        if($withExtraFields) {
            $values = array_fill_keys($extraKeys, 1);
        }else{
            $values = array();
        }

        if(!$allowExtraFields && $withExtraFields) {
            $this->method($result, 'addIvalidKeysError', null, array($extraKeys), $resultAt++);
        }

        $rules  = array();
        foreach(array('fairy', 'pixie', 'trixie') as $name) {
            $rule = $this->getRule();
            $rules[$name] = $rule;
            $this->rule->setFieldRule($name, $rule);

            $fieldResult = $this->getResultMock();
            $this->method($result, 'field', $fieldResult, array($name), $resultAt++);
            if($name !== 'trixie') {
                $value = $name.'Value';
                $values[$name] = $value;
            }else{
                $value = null;
            }

            $this->method($rule, 'validate', null, array($value, $fieldResult), 0);
        }

        return array($values, $result);
    }

    protected function document()
    {
        return new \PHPixie\Validate\Rules\Rule\Data\Document(
            $this->rules
        );
    }
}