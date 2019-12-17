# PHP Validation Service

[![Latest Version](https://img.shields.io/packagist/v/codes50/validation.svg?style=flat-square)](https://packagist.org/packages/codes50/validation)
[![Packagist](https://img.shields.io/packagist/dm/codes50/validation.svg)](https://packagist.org/packages/codes50/validation)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Requires
* php: >=7.2

## Install

Install latest version using [composer](https://getcomposer.org/).

``` bash
$ composer require codes50/validation
```

## Usage

* Multiple Usage

``` php
use Codes50\Validator;

$data = [
    "int" => 10,
    "double" => 10.5,
    "string" => "asd",
    "email" => "kadiryolalan@gmail.com",
    "boolean" => false,
    "url" => "https://www.linkedin.com/in/kadir-yolalan-722538164/",
    "domain" => "github.com",
    "ip" => "192.168.1.1",
    "ipv4" => "192.168.1.1",
    "ipv6" => "2001:0db8:85a3:0000:0000:8a2e:0370:7334",
    "mac" => "20:01:0d:b8:85:a3",
    "weekpassword" => "1A2dh_",
    "select" => "1",
    "date" => "13-12-2019 00:00:00",
    "subtest" => [
        "sub1" => 111,
        "sub2" => [
            "sub3" => "a"
        ]
    ]
];
$rules = [
    "int" => [
        Validator::ATTR_TYPE => Validator::TYPE_INT,
        Validator::ATTR_REQUIRED => true,
        Validator::ATTR_MAX => 100,
        Validator::ATTR_MIN => 1
    ],
    "double" => [
        Validator::ATTR_TYPE => Validator::TYPE_DOUBLE,
        Validator::ATTR_REQUIRED => true,
        Validator::ATTR_MAX => 100,
        Validator::ATTR_MIN => 1
    ],
    "string" => [
        Validator::ATTR_TYPE => Validator::TYPE_STRING,
        Validator::ATTR_REQUIRED => true,
        Validator::ATTR_MAX_LENGTH => 3,
        Validator::ATTR_MIN_LENGTH => 1
    ],
    "email" => [
        Validator::ATTR_TYPE => Validator::TYPE_EMAIL,
        Validator::ATTR_REQUIRED => true
    ],
    "boolean" => [
        Validator::ATTR_TYPE => Validator::TYPE_BOOLEAN,
        Validator::ATTR_REQUIRED => true
    ],
    "url" => [
        Validator::ATTR_TYPE => Validator::TYPE_URL,
        Validator::ATTR_REQUIRED => true
    ],
    "domain" => [
        Validator::ATTR_TYPE => Validator::TYPE_DOMAIN,
        Validator::ATTR_REQUIRED => true
    ],
    "ip" => [
        Validator::ATTR_TYPE => Validator::TYPE_IP,
        Validator::ATTR_REQUIRED => true
    ],
    "ipv4" => [
        Validator::ATTR_TYPE => Validator::TYPE_IPV4,
        Validator::ATTR_REQUIRED => true
    ],
    "ipv6" => [
        Validator::ATTR_TYPE => Validator::TYPE_IPV6,
        Validator::ATTR_REQUIRED => true
    ],
    "mac" => [
        Validator::ATTR_TYPE => Validator::TYPE_MAC,
        Validator::ATTR_REQUIRED => true
    ],
    "weekpassword" => [
        Validator::ATTR_TYPE => Validator::TYPE_WEAK_PASSWD,
        Validator::ATTR_REQUIRED => true
    ],
    "select" => [
        Validator::ATTR_TYPE => Validator::TYPE_SELECT,
        Validator::ATTR_REQUIRED => true,
        Validator::ATTR_OPTIONS => [
            1,
            2,
            3,
            4
        ]
    ],
    "date" => [
        Validator::ATTR_TYPE => Validator::TYPE_DATE,
        Validator::ATTR_REQUIRED => true
    ],
    "subtest.sub1" => [
        Validator::ATTR_TYPE => Validator::TYPE_INT,
        Validator::ATTR_REQUIRED => true
    ]
];

$validate = Validator::make($data, $rules);
var_dump($validate->validate());
print_r($validate->error->all());
```
**print:**
```
bool(true)
Array
(
)
```
* Single Usage
```php
use Codes50\Validator;
$validate = New Validator();
$single = [
    Validator::ATTR_TYPE => Validator::TYPE_STRING,
    Validator::ATTR_REQUIRED => true,
    Validator::ATTR_MAX_LENGTH => 3
];
if($validate->singleValid($single, "test") !== true) {
    $error = $validate->singleValid($single, "test");
    echo $error;
}
```
**print:**
```
max_length
```

## Customize Usage
need to extend it with the "CoreValidator" class.
```php
use Codes50\Core\CoreValidator;

class CustomValidator extends CoreValidator
{
    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
    }
}
```

* Add Custom Attribute Plugin:
```php
use Codes50\Core\CoreValidator;

class Validator extends CoreValidator
{
    public const ATTR_TEST = 'attr_test';

    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
        $this->registerAttrPlugin(self::ATTR_TEST, "checkAttrTest");
    }

    /**
     * @param $data
     * @return bool
     */
    protected function checkAttrTest($data): bool
    {
        $this->_error = "testmessage";
        return false;
    }
}
```

* Add Custom Type Plugin:
```php
use Codes50\Core\CoreValidator;

class Validator extends CoreValidator
{
    public const TYPE_TEST = 'type_test';
    public const ATTR_TEST = 'attr_test';

    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
        $this->registerTypePlugin(self::TYPE_TEST, "checkTest", [self::ATTR_TEST, self::ATTR_MIN_LENGTH, self::ATTR_MAX_LENGTH]);
        $this->registerAttrPlugin(self::ATTR_TEST, "checkAttrTest");
    }

    /**
     * @param $data
     * @return bool
     */
    protected function checkTest($data): bool
    {
        // $this->_error = "testmessage";
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function checkAttrTest($data): bool
    {
        $this->_error = "testmessage";
        return false;
    }
}
```

* Language:
```php
use Codes50\Core\CoreValidator;

class Validator extends CoreValidator
{
    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
        // $this->setLanguage("en");
        // $this->setLanguage("tr");
        // $this->setLanguage("{your_directory}/custom_language.php", true);
    }
}
```
custom_language.php Example:

```php
return [
    "plugin_method_not_avaliable" => "Extension method not found for defined control type.",
    "plugin_not_avaliable" => "No defined control type!",
    "is_required" => "Required field! Please do not leave blank.",
    "cannot_be_null" => "Invalid Data Type! This field cannot have a NULL value.",
    "number_max_exceed" => "Enter a smaller number.",
    "number_min_exceed" => "Enter a larger number.",
    "length_max_exceed" => "You have exceeded the number of characters!",
    "length_min_exceed" => "Insufficient Number of Characters",
    "invalid_regexp" => "This field does not match.",
    "invalid_int" => "This field can only be a number.",
    "invalid_double" => "This field can only receive numeric or decimal data.",
    "invalid_email" => "Invalid email address",
    "invalid_boolean" => "Invalid data type",
    "invalid_url" => "Invalid URL",
    "invalid_domain" => "Invalid domain address",
    "invalid_ip" => "Invalid IP address",
    "invalid_ipv4" => "Invalid IP address. Must only be of type ipv4.",
    "invalid_ipv6" => "Invalid IP address. Must only be of type ipv6.",
    "invalid_mac" => "Invalid MAC address",
    "invalid_weak_password" => "Invalid password. Your password must contain at least one uppercase letter, a lowercase letter, a number, and a special character.",
    "invalid_date" => "Invalid data. This field can only be date.",
    "invalid_option" => "Invalid data type"
];
```
