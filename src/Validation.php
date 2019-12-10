<?php
namespace Codes50;

class Validation {
    /**
     * @var array
     */
    private $_data = [];

    /**
     * @var array
     */
    private $_conf = [];

    /**
     * @var string
     */
    private $_error = "";
    /**
     * @var array
     */
    private $_errors = [];

    /**
     * Validation constructor.
     * @param array|null $data
     * @param array|null $conf
     */
    public function __construct($data = null, $conf = null)
    {
        $this->_data = $data;
        $this->_conf = $conf;
    }

    /**
     * @return string
     */
    public function getError(): string
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
        foreach ($this->_conf as $key => $conf) {
            $data[$key] = $conf;
            $data[$key]["value"] = $this->_data[$key] ?? null;
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
        $this->_error = '';
        if ((!isset($data["required"]) || $data["required"] === false) && ($data["value"] === '' || $data["value"] === null)) {
            return true;
        }
        if (!$this->checkProperty($data)) {
            return false;
        }

        switch ($data["type"]) {
            case 'email':
                return $this->checkEmail($data);
            case 'password':
                return $this->checkPassword($data);
            case 'checkbox':
                return $this->checkCheckbox($data);
            case 'number':
            case 'integer':
                return $this->checkNumber($data);
            case 'boolean':
                return $this->checkBoolean($data);
            case 'url':
                return $this->checkUrl($data);
            case 'mac':
                return $this->checkMac($data);
            case 'ip':
                return $this->checkIP($data);
            case 'domain':
                return $this->checkDomain($data);
            case 'full_date':
                return $this->checkDate($data);
            case 'string_alpha':
                return $this->checkAlpha($data);
            case 'text':
            case 'string':
            case 'date':
                return $this->checkDefault();
            default:
                return $this->checkDefault();
        }
    }

    /**
     * @return bool
     */
    private function checkDefault(): bool
    {
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkCheckbox($data): bool
    {
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkDate($data): bool
    {
        $unix = strtotime($data["value"]);
        if ($unix !== false || $unix != -1) {
            return true;
        }
        $this->_error = 'error_invalidformat';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkBoolean($data): bool
    {
        if (is_bool($data["value"]) === true) {
            return true;
        }
        $this->_error = 'error_invalidboolean';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkNumber($data): bool
    {
        if (filter_var($data["value"], FILTER_VALIDATE_INT) !== false) {
            return true;
        }
        $this->_error = 'error_isnumber';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkPassword($data): bool
    {
        return true;
        //sms onayında hata veriyor!
        if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{1,}$/', $data["value"]) !== 0) {
            return true;
        }
        $this->_error = 'error_weakpassword';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkAlpha($data): bool
    {
        if (preg_match('/^\D+[^\!@£$€₺#^+\-%&\/\\()\[\]={}?*,;`~:.<>|"]$/', $data["value"]) !== 0) {
            return true;
        }
        $this->_error = 'error_invalidstring';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkEmail($data): bool
    {
        if (filter_var($data["value"], FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        $this->_error = 'error_invalidemail';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkUrl($data): bool
    {
        if (filter_var($data["value"], FILTER_VALIDATE_URL)) {
            return true;
        }
        $this->_error = 'error_invalidurl';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMac($data): bool
    {
        if (filter_var($data["value"], FILTER_VALIDATE_MAC)) {
            return true;
        }
        $this->_error = 'error_invalidmac';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkDomain($data): bool
    {
        if (filter_var($data["value"], FILTER_VALIDATE_DOMAIN)) {
            return true;
        }
        $this->_error = 'error_invaliddomain';
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkIP($data): bool
    {
        if (filter_var($data["value"], FILTER_VALIDATE_IP)) {
            return true;
        }
        $this->_error = 'error_invalidip';
        return false;
    }

    /* property methods */
    /**
     * @param $data
     * @return bool
     */
    private function checkProperty($data): bool
    {
        if ($this->checkRequired($data)
            && $this->checkMinLength($data)
            && $this->checkMaxLength($data)
            && $this->checkMax($data)
            && $this->checkMin($data)) {
            return true;
        }
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMax($data): bool
    {
        if (($data["type"] === "number" || $data["type"] === "integer") && isset($data["max"]) && $this->checkNumber($data)) {
            if ($data["value"] <= $data["max"]) {
                return true;
            }
            $this->_error = 'error_maxnumber';
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMin($data): bool
    {
        if (($data["type"] === "number" || $data["type"] === "integer") && isset($data["min"]) && $this->checkNumber($data)) {
            if ($data["value"] >= $data["min"]) {
                return true;
            }
            $this->_error = 'error_minnumber';
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkMaxLength($data): bool
    {
        if (isset($data["maxLength"])) {
            if (strlen($data["value"]) <= $data["maxLength"]) {
                return true;
            }
            $this->_error = 'error_maxlength';
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
        if (isset($data["minLength"])) {
            if (strlen($data["value"]) >= $data["minLength"]) {
                return true;
            }
            $this->_error = 'error_minlength';
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkRequired($data): bool
    {
        if (isset($data["required"]) && $data["required"] === true) {
            if (strlen($data["value"]) > 0 || is_bool($data["value"]) === true) {
                return true;
            }
            $this->_error = 'error_required';
            return false;
        }
        return true;
    }
}

