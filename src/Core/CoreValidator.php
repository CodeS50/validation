<?php


namespace Codes50\Core;


abstract class CoreValidator extends DefaultPlugins
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
     * @var array
     */
    private $plugins;

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
        $this->plugins = [
            "type" => [
                self::TYPE_STRING => '',
                self::TYPE_INT => 'checkInt',
                self::TYPE_DOUBLE => 'checkDouble',
                self::TYPE_EMAIL => 'checkEmail',
                self::TYPE_BOOLEAN => 'checkBoolean',
                self::TYPE_URL => 'checkUrl',
                self::TYPE_DOMAIN => 'checkDomain',
                self::TYPE_IP => 'checkIP',
                self::TYPE_IPV4 => 'checkIPV4',
                self::TYPE_IPV6 => 'checkIPV6',
                self::TYPE_MAC => 'checkMac',
                self::TYPE_WEAK_PASSWD => 'checkWeakPassword',
                self::TYPE_DATE => 'checkDate',
                self::TYPE_SELECT => 'checkSelect'
            ],
            "attr" => [
                self::ATTR_REQUIRED => 'checkRequired',
                self::ATTR_NULLABLE => 'checkNullable',
                self::ATTR_MIN_LENGTH => 'checkMinLength',
                self::ATTR_MAX_LENGTH => 'checkMaxLength',
                self::ATTR_MIN => 'checkMin',
                self::ATTR_MAX => 'checkMax',
                self::ATTR_REGEXP => 'checkRegexp'
            ],
            "type_to_attr" => [
                self::TYPE_INT => [
                    self::ATTR_MIN,
                    self::ATTR_MAX
                ],
                self::TYPE_DOUBLE => [
                    self::ATTR_MIN,
                    self::ATTR_MAX
                ],
                self::TYPE_STRING => [
                    self::ATTR_MIN_LENGTH,
                    self::ATTR_MAX_LENGTH
                ]
            ]
        ];
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
            }
            return $this->checkOtherPlugins($data[self::ATTR_TYPE], $data);
        } else {
            return true;
        }
    }

    /**
     * @param array $rule
     * @param $value
     * @return bool|string
     */
    public function singleValid(array $rule, $value)
    {
        $data = $rule;
        $data[self::ATTR_VALUE] = $value;
        $data["value_type"] = gettype($value);
        if (!$this->isValid($data)) {
            return $this->_error;
        }

        return true;
    }

    /**
     * @param string $keyword
     * @param string $function
     * @param array $attrs
     */
    protected function registerTypePlugin(string $keyword, string $function, array $attrs = []): void
    {
        $this->plugins["type"][$keyword] = $function;
        $this->plugins["type_to_attr"][$keyword] = $attrs;
    }

    /**
     * @param string $keyword
     * @param string $function
     */
    protected function registerAttrPlugin(string $keyword, string $function): void
    {
        $this->plugins["attr"][$keyword] = $function;
    }

    /**
     * @param string $attr_type
     * @param array $data
     * @return bool
     */
    private function checkOtherPlugins(string $attr_type, array $data): bool
    {
        if (isset($this->plugins["type"][$attr_type])) {
            if (method_exists($this, $this->plugins["type"][$attr_type])) {
                $has_type = call_user_func_array([
                    $this,
                    $this->plugins["type"][$attr_type]
                ], [$data]);
                if ($has_type) {
                    if (isset($this->plugins["type_to_attr"][$attr_type])) {
                        foreach ($this->plugins["type_to_attr"][$attr_type] as $attribute) {
                            if (!(boolean)call_user_func_array(array($this, $this->plugins["attr"][$attribute]), [$data])) {
                                return false;
                            }
                        }
                    }
                    return true;
                }
                return false;
            } else {
                $this->_error = "plugin_method_not_avaliable";
                return false;
            }
        } else {
            $this->_error = "plugin_not_avaliable";
            return false;
        }
    }
}