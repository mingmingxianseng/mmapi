<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/5/8
 * Time: 09:41
 */

namespace mmapi\model\validate;

use mmapi\model\InvalidFieldValue;
use \Doctrine\DBAL\Types\Type as T;

/**
 * Class Type
 *
 * @package mmapi\model\validate
 */
abstract class Type
{
    /** @var  \ReflectionProperty */
    protected $rfField;
    /** @var  object */
    protected $object;
    /** @var  array */
    protected $fieldInfo;
    /** @var  string */
    protected $name;
    /**
     * @var
     */
    protected $msg;
    /**
     * @var
     */
    protected $value;
    /**
     * The map of supported doctrine mapping types.
     *
     * @var array
     */
    private static $_typesMap = [
        T::INTEGER  => IntegerType::class,
        T::SMALLINT => SmallIntType::class,
        T::STRING   => StringType::class,
        T::TEXT     => StringType::class,
        T::DATETIME => DatetimeType::class,
        T::DECIMAL  => DecimalType::class,
        T::FLOAT    => DecimalType::class,
    ];

    /**
     * @var array
     */
    private static $_typeObjects = [];

    /**
     * @return \ReflectionProperty
     */
    public function getRfField(): \ReflectionProperty
    {
        return $this->rfField;
    }

    /**
     * @desc   setRfField
     * @author chenmingming
     *
     * @param \ReflectionProperty $rfField
     *
     * @return Type
     */
    public function setRfField(\ReflectionProperty $rfField)
    {
        $this->rfField = $rfField;

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldInfo(): array
    {
        return $this->fieldInfo;
    }

    /**
     * @desc   setFieldInfo
     * @author chenmingming
     *
     * @param array $fieldInfo
     *
     * @return Type
     */
    public function setFieldInfo(array $fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
        $this->name      = $this->fieldInfo['fieldName'];

        return $this;
    }

    /**
     * @return object
     */
    public function getObject(): object
    {
        return $this->object;
    }

    /**
     * @param object $object
     *
     * @return Type
     */
    public function setObject($object): Type
    {
        $this->object = $object;
        $this->value  = $this->rfField->getValue($object);

        return $this;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @desc   setMsg
     * @author chenmingming
     *
     * @param string $msg
     *
     * @return Type
     */
    public function setMsg(string $msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * @desc   checkNull
     * @author chenmingming
     */
    protected function checkNull()
    {
        if ($this->fieldInfo['nullable'] === false && is_null($this->value)) {
            if ($this->msg) {
                throw new InvalidFieldValue($this->msg, InvalidFieldValue::ERR_NULL);
            }
            throw InvalidFieldValue::fieldNotAllowNull($this->getFieldName());
        }
    }

    /**
     * @desc   getFieldName
     * @author chenmingming
     * @return string
     */
    protected function getFieldName()
    {
        return $this->fieldInfo['options']['comment']
            ? $this->fieldInfo['options']['comment'] . "[{$this->name}]"
            : $this->name;
    }

    /**
     * @desc   checkRange
     * @author chenmingming
     */
    abstract protected function checkRange();

    /**
     * @desc   check
     * @author chenmingming
     *
     * @param $name
     *
     * @return false|Type
     */
    static public function check($name)
    {
        if (!isset(self::$_typeObjects[$name])) {
            self::$_typeObjects[$name] = false;
            if (isset(self::$_typesMap[$name])) {
                self::$_typeObjects[$name] = new self::$_typesMap[$name]($name);
            }
        }

        return self::$_typeObjects[$name];
    }

    /**
     * @desc   customError
     * @author chenmingming
     *
     * @param $errno
     *
     * @throws InvalidFieldValue
     */
    protected function customError($errno)
    {
        if ($this->msg) {
            throw  new InvalidFieldValue($this->msg, $errno);
        }
    }

    /**
     * @desc   __invoke
     * @author chenmingming
     */
    public function __invoke()
    {
        $this->checkNull();
        if ($this->fieldInfo['nullable'] === false || !is_null($this->value))
            $this->checkRange();
    }
}