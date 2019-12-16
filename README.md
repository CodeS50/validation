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
    public const TYPE_TEST_ATTR = 'testattr';

    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
        $this->registerAttrPlugin(self::TYPE_TEST_ATTR, "checkAttrTest");
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

* Add Custom Attribute Plugin:
```php
use Codes50\Core\CoreValidator;

class Validator extends CoreValidator
{
    public const TYPE_TEST_ATTR = 'testattr';

    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
        $this->registerTypePlugin(self::TYPE_TEST, "checkTest", [self::TYPE_TEST_ATTR, self::ATTR_MIN_LENGTH, self::ATTR_MAX_LENGTH]);
        $this->registerAttrPlugin(self::TYPE_TEST_ATTR, "checkAttrTest");
    }

    /**
     * @param $data
     * @return bool
     */
    protected function checkTest($data)
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
