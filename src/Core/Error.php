<?php


namespace Codes50\Core;


class Error
{
    /**
     * @var array
     */
    protected $_errors;

    /**
     * Error constructor.
     */
    public function __construct()
    {
        $this->_errors = [];
    }

    public function setField(string $field, string $message)
    {
        $this->_errors[$field] = $message;
    }

    /**
     * @param string $keyword
     * @return string|null
     */
    public function getField(string $keyword): ?string
    {
        return $this->_errors[$keyword] ?? null;
    }

    /**
     * @param string $keyword
     * @return bool
     */
    public function hasField(string $keyword): bool
    {
        return isset($this->_errors[$keyword]);
    }

    /**
     *
     */
    public function reset()
    {
        $this->_errors = [];
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->_errors;
    }
}