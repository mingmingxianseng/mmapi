<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/5/8
 * Time: 10:48
 */

namespace mmapi\model\validate;

use mmapi\model\InvalidFieldValue;

class StringType extends Type
{
    protected function checkRange()
    {
        if ($this->fieldInfo['length'] && strlen($this->value) > $this->fieldInfo['length']) {
            $this->customError(InvalidFieldValue::ERR_OUTOFRANGE);
            throw InvalidFieldValue::fieldOutOfRange($this->getFieldName(),$this->value);
        }
    }

}