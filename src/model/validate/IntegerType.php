<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/5/8
 * Time: 10:42
 */

namespace mmapi\model\validate;

use mmapi\model\InvalidFieldValue;

class IntegerType extends Type
{
    protected function checkRange()
    {
        $max = $this->fieldInfo['options']['max'] ?? (isset($conf['options']['unsigned']) ? 4294967295 : 2147483647);
        if ($this->value > $max) {
            $this->customError(InvalidFieldValue::ERR_OUTOFRANGE);
            throw InvalidFieldValue::fieldOutOfRange($this->getFieldName(),$this->value);
        }
    }

}