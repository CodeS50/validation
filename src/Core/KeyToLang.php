<?php


namespace Codes50\Core;


class KeyToLang
{
    /**
     * @var KeyToLang[]
     */
    protected static $_instance;

    /**
     * @var array
     */
    private $keywords;

    public function __construct(string $lang = "en", bool $has_file = false)
    {
        $this->initLanguage($lang, $has_file);
    }

    /**
     * @param string $lang
     * @param bool $has_file
     * @return KeyToLang
     */
    public static function getInstance(string $lang = "en", bool $has_file = false)
    {
        if (!isset(self::$_instance[$lang]) || self::$_instance[$lang] === NULL)
            self::$_instance[$lang] = new self($lang, $has_file);
        return self::$_instance[$lang];
    }

    /**
     * @param string $lang
     * @param bool $has_file
     */
    private function initLanguage(string $lang, bool $has_file)
    {
        if(!empty($lang)) {
            if ($has_file) {
                $file = $lang;
            } else {
                $file = __DIR__ . "/../i18n/" . $lang . ".php";
            }

            if (is_file($file)) {
                $this->keywords = include($file);
            }
        }
    }

    public function getLabel($keyword)
    {
        return $this->keywords[$keyword] ?? $keyword;
    }
}