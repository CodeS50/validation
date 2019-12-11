<?php


namespace Codes50\Core;


abstract class CoreValidator extends Checker
{
    /**
     * @var array
     */
    protected $_rules;

    /**
     * @var array
     */
    protected $_data;

    /**
     * @var Error
     */
    public $error;

    /**
     * Validator constructor.
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data = [], array $rules = [])
    {
        $this->error = new Error();
        $this->_data = $data;
        $this->_rules = $rules;
    }

    /**
     * Validator constructor.
     * @param array $data
     * @param array $rules
     * @return CoreValidator
     */
    public static function make(array $data = [], array $rules = []): self
    {
        return new static($data, $rules);
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $data = [];
        foreach ($this->_rules as $data_key => $rule) {
            $exp_keys = explode(".", $data_key);
            $value = $this->_data;
            foreach ($exp_keys as $exp_key) {
                if (!isset($value[$exp_key])) {
                    $value = null;
                    break;
                }
                $value = $value[$exp_key];
            }
            $data[$data_key] = $rule;
            $data[$data_key][self::ATTR_VALUE] = $value;
            $data[$data_key]["value_type"] = gettype($value);
        }
        return $this->wholeValid($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function wholeValid(array $data): bool
    {
        $status = true;
        foreach ($data as $i => $item) {
            if (!$this->isValid($item)) {
                $this->error->setField($i, $this->_error);
                $status = false;
            }
        }
        return $status;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isValid($data): bool
    {
        $this->_error = null;

        if (!$this->checkRequired($data)
            || !$this->checkRegexp($data)) {
            return false;
        }

        if (isset($data[self::ATTR_TYPE])) {
            switch ($data[self::ATTR_TYPE]) {
                case self::TYPE_INT:
                    return ($this->checkInt($data)
                        && $this->checkMin($data)
                        && $this->checkMax($data));
                case self::TYPE_DOUBLE:
                    return ($this->checkDouble($data)
                        && $this->checkMin($data)
                        && $this->checkMax($data));
                case self::TYPE_STRING:
                    return ($this->checkMinLength($data)
                        && $this->checkMaxLength($data));
                case self::TYPE_EMAIL:
                    return $this->checkEmail($data);
                case self::TYPE_BOOLEAN:
                    return $this->checkBoolean($data);
                case self::TYPE_URL:
                    return $this->checkUrl($data);
                case self::TYPE_DOMAIN:
                    return $this->checkDomain($data);
                case self::TYPE_IP:
                    return $this->checkIP($data);
                case self::TYPE_IPV4:
                    return $this->checkIPV4($data);
                case self::TYPE_IPV6:
                    return $this->checkIPV6($data);
                case self::TYPE_MAC:
                    return $this->checkMac($data);
                case self::TYPE_WEAK_PASSWD:
                    return $this->checkWeakPassword($data);
                case self::TYPE_DATE:
                    return $this->checkDate($data);
                case self::TYPE_SELECT:
                    return $this->checkSelect($data);
                default:
                    return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param array $rule
     * @param $value
     * @return bool|string
     */
    public function singleValid(array $rule, $value){
        $data = $rule;
        $data[self::ATTR_VALUE] = $value;
        $data["value_type"] = gettype($value);
        if(!$this->isValid($data)) {
            return $this->_error;
        }

        return true;
    }
}