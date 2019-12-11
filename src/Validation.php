<?php

namespace Codes50;

class Validation
{
    public const ATTR_TYPE = 'type';
    public const ATTR_VALUE = 'value';
    public const ATTR_OPTIONS = 'options';
    public const ATTR_REQUIRED = 'required';
    public const ATTR_NULLABLE = 'nullable';
    public const ATTR_MIN_LENGTH = 'min_length';
    public const ATTR_MAX_LENGTH = 'max_length';
    public const ATTR_MIN = 'min';
    public const ATTR_MAX = 'max';
    public const ATTR_REGEXP = 'regexp';

    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'int';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_EMAIL = 'email';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_URL = 'url';
    public const TYPE_DOMAIN = 'domain';
    public const TYPE_IP = 'ip';
    public const TYPE_IPV4 = 'ipv4';
    public const TYPE_IPV6 = 'ipv6';
    public const TYPE_MAC = 'mac';
    public const TYPE_WEAK_PASSWD = 'weak_passwd';
    public const TYPE_DATE = 'date';
    public const TYPE_SELECT = 'select';

    /**
     * @var array
     */
    private $_rules;

    /**
     * @var array
     */
    private $_data;

    /**
     * @var string|null
     */
    private $_error;
    /**
     * @var array
     */
    private $_errors;

    /**
     * Validation constructor.
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data = [], array $rules = [])
    {
        $this->_data = $data;
        $this->_rules = $rules;
        $this->_errors = [];
    }

    /**
     * @return string
     */
    public function getError(): ?string
    {
        return $this->_error;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
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
    public function wholeValid(array $data): bool
    {
        $this->_errors = [];
        $status = true;
        foreach ($data as $i => $item) {
            if (!$this->isValid($item)) {
                $this->_errors[$i] = $this->_error;
                $status = false;
            }
        }
        return $status;
    }

    /**
     * @param $data
     * @return bool
     */
    public function isValid($data): bool
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
     * @param $data
     * @return bool
     */
    private function checkRequired($data): bool
    {
        if (isset($data[self::ATTR_REQUIRED]) && $data[self::ATTR_REQUIRED] === true) {
            if ($data["value_type"] === "boolean") {
                return true;
            }

            // Nullable fix
            if ($data["value_type"] === "NULL" && isset($data[self::ATTR_NULLABLE]) && $data[self::ATTR_NULLABLE] === true) {
                return true;
            }

            if (strlen($data[self::ATTR_VALUE]) > 0) {
                return true;
            }
            $this->_error = self::ATTR_REQUIRED;
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMax($data): bool
    {
        if (isset($data[self::ATTR_MAX])) {
            if ($data["value_type"] === "integer" || $data["value_type"] === "double") {
                if ($data[self::ATTR_VALUE] <= $data[self::ATTR_MAX]) {
                    return true;
                }
            }
            $this->_error = self::ATTR_MAX;
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMin($data): bool
    {
        if (isset($data[self::ATTR_MIN])) {
            if ($data["value_type"] === "integer" || $data["value_type"] === "double") {
                if ($data[self::ATTR_VALUE] >= $data[self::ATTR_MIN]) {
                    return true;
                }
            }
            $this->_error = self::ATTR_MIN;
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMaxLength($data): bool
    {
        if (isset($data[self::ATTR_MAX_LENGTH])) {
            if (strlen($data[self::ATTR_VALUE]) <= $data[self::ATTR_MAX_LENGTH]) {
                return true;
            }
            $this->_error = self::ATTR_MAX_LENGTH;
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMinLength($data): bool
    {
        if (isset($data[self::ATTR_MIN_LENGTH])) {
            if (strlen($data[self::ATTR_VALUE]) >= $data[self::ATTR_MIN_LENGTH]) {
                return true;
            }
            $this->_error = self::ATTR_MIN_LENGTH;
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkRegexp($data): bool
    {
        if (isset($data[self::ATTR_REGEXP])) {
            if (filter_var($data[self::ATTR_REGEXP], FILTER_VALIDATE_REGEXP) !== false) {
                if ((boolean)preg_match($data[self::ATTR_REGEXP], $data[self::ATTR_VALUE])) {
                    return true;
                }
            }

            $this->_error = self::ATTR_REGEXP;
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkInt($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_INT) !== false) {
            return true;
        }
        $this->_error = self::TYPE_INT;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkDouble($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_FLOAT) !== false) {
            return true;
        }
        $this->_error = self::TYPE_DOUBLE;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkEmail($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        $this->_error = self::TYPE_EMAIL;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkBoolean($data): bool
    {
        if (is_bool($data[self::ATTR_VALUE]) === true) {
            return true;
        }
        $this->_error = self::TYPE_BOOLEAN;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkUrl($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_URL)) {
            return true;
        }
        $this->_error = self::TYPE_URL;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkDomain($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return true;
        }
        $this->_error = self::TYPE_DOMAIN;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkIP($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_IP)) {
            return true;
        }
        $this->_error = self::TYPE_IP;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkIPV4($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        }
        $this->_error = self::TYPE_IPV4;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkIPV6($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        }
        $this->_error = self::TYPE_IPV6;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMac($data): bool
    {
        if (filter_var($data[self::ATTR_VALUE], FILTER_VALIDATE_MAC)) {
            return true;
        }
        $this->_error = self::TYPE_MAC;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkWeakPassword($data): bool
    {
        if ((boolean)preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[.!@#$&+\/*_\-])[0-9a-zA-Z.!@#$&+\/*_\-]{6,}$/', $data[self::ATTR_VALUE])) {
            return true;
        }
        $this->_error = self::TYPE_WEAK_PASSWD;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkDate($data): bool
    {
        $unix = strtotime($data[self::ATTR_VALUE]);
        if ($unix !== false || $unix != -1) {
            return true;
        }
        $this->_error = self::TYPE_DATE;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkSelect($data): bool
    {
        if (isset($data[self::ATTR_OPTIONS])
            && is_array($data[self::ATTR_OPTIONS])
            && in_array($data[self::ATTR_VALUE], $data[self::ATTR_OPTIONS])) {
            return true;
        }

        $this->_error = self::TYPE_SELECT;
        return false;
    }

}

