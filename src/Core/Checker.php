<?php


namespace Codes50\Core;


abstract class Checker
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
     * @var string
     */
    protected $_error;

    /**
     * @param $data
     * @return bool
     */
    protected function checkRequired($data): bool
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
    protected function checkMax($data): bool
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
    protected function checkMin($data): bool
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
    protected function checkMaxLength($data): bool
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
    protected function checkMinLength($data): bool
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
    protected function checkRegexp($data): bool
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
    protected function checkInt($data): bool
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
    protected function checkDouble($data): bool
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
    protected function checkEmail($data): bool
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
    protected function checkBoolean($data): bool
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
    protected function checkUrl($data): bool
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
    protected function checkDomain($data): bool
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
    protected function checkIP($data): bool
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
    protected function checkIPV4($data): bool
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
    protected function checkIPV6($data): bool
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
    protected function checkMac($data): bool
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
    protected function checkWeakPassword($data): bool
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
    protected function checkDate($data): bool
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
    protected function checkSelect($data): bool
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