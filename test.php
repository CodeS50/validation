<?php
require 'vendor/autoload.php';

use Codes50\Validator;

$data = [
    "int" => 10,
    "double" => 10,
    "string" => "amk",
    "email" => "test@test.co",
    "boolean" => false,
    "url" => "http://x.com/asd",
    "domain" => "x.com",
    "ip" => "192.168.1.1",
    "ipv4" => "192.168.1.1",
    "ipv6" => "2001:0db8:85a3:0000:0000:8a2e:0370:7334",
    "mac" => "20:01:0d:b8:85:a3",
    "weekpassword" => "1A2dh_",
    "select" => "1",
    "date" => "1995",
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

$subrules = [
    "int" => [
        Validator::ATTR_TYPE => Validator::TYPE_INT,
        Validator::ATTR_REQUIRED => true
    ],
    "subtest.sub1" => [
        Validator::ATTR_TYPE => Validator::TYPE_INT,
        Validator::ATTR_REQUIRED => true
    ],
    "subtest.sub2.sub3" => [
        Validator::ATTR_TYPE => Validator::TYPE_DOUBLE,
        Validator::ATTR_REQUIRED => true
    ]
];

//print_r($rules);

$validate = Validator::make($data, $subrules);
var_dump($validate->validate());
print_r($validate->error->all());

$single = [
    Validator::ATTR_TYPE => Validator::TYPE_INT,
    Validator::ATTR_REQUIRED => true
];

var_dump($validate->singleValid($single, "test"));


