<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/5/8
 * Time: 10:46
 */

namespace mmapi\model\validate;

use mmapi\model\InvalidFieldValue;

class SmallIntType extends Type
{
    protected function checkRange()
    {
        $max = $this->fieldInfo['options']['max'] ?? (isset($this->fieldInfo['options']['unsigned']) ? 65535 : 32767);
        if ($this->value > $max) {
            $this->customError(InvalidFieldValue::ERR_OUTOFRANGE);
            throw InvalidFieldValue::fieldOutOfRange($this->getFieldName(), $this->value);
        }
    }

}