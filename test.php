<?php
require 'vendor/autoload.php';

use Codes50\Validation;

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
            "sub3" => 1.1
        ]
    ]
];
$rules = [
    "int" => [
        Validation::ATTR_TYPE => Validation::TYPE_INT,
        Validation::ATTR_REQUIRED => true,
        Validation::ATTR_MAX => 100,
        Validation::ATTR_MIN => 1
    ],
    "double" => [
        Validation::ATTR_TYPE => Validation::TYPE_DOUBLE,
        Validation::ATTR_REQUIRED => true,
        Validation::ATTR_MAX => 100,
        Validation::ATTR_MIN => 1
    ],
    "string" => [
        Validation::ATTR_TYPE => Validation::TYPE_STRING,
        Validation::ATTR_REQUIRED => true,
        Validation::ATTR_MAX_LENGTH => 3,
        Validation::ATTR_MIN_LENGTH => 1
    ],
    "email" => [
        Validation::ATTR_TYPE => Validation::TYPE_EMAIL,
        Validation::ATTR_REQUIRED => true
    ],
    "boolean" => [
        Validation::ATTR_TYPE => Validation::TYPE_BOOLEAN,
        Validation::ATTR_REQUIRED => true
    ],
    "url" => [
        Validation::ATTR_TYPE => Validation::TYPE_URL,
        Validation::ATTR_REQUIRED => true
    ],
    "domain" => [
        Validation::ATTR_TYPE => Validation::TYPE_DOMAIN,
        Validation::ATTR_REQUIRED => true
    ],
    "ip" => [
        Validation::ATTR_TYPE => Validation::TYPE_IP,
        Validation::ATTR_REQUIRED => true
    ],
    "ipv4" => [
        Validation::ATTR_TYPE => Validation::TYPE_IPV4,
        Validation::ATTR_REQUIRED => true
    ],
    "ipv6" => [
        Validation::ATTR_TYPE => Validation::TYPE_IPV6,
        Validation::ATTR_REQUIRED => true
    ],
    "mac" => [
        Validation::ATTR_TYPE => Validation::TYPE_MAC,
        Validation::ATTR_REQUIRED => true
    ],
    "weekpassword" => [
        Validation::ATTR_TYPE => Validation::TYPE_WEAK_PASSWD,
        Validation::ATTR_REQUIRED => true
    ],
    "select" => [
        Validation::ATTR_TYPE => Validation::TYPE_SELECT,
        Validation::ATTR_REQUIRED => true,
        Validation::ATTR_OPTIONS => [
            1,
            2,
            3,
            4
        ]
    ],
    "date" => [
        Validation::ATTR_TYPE => Validation::TYPE_DATE,
        Validation::ATTR_REQUIRED => true
    ],
    "subtest.sub1" => [
        Validation::ATTR_TYPE => Validation::TYPE_INT,
        Validation::ATTR_REQUIRED => true
    ]
];

$subrules = [
    "int" => [
        Validation::ATTR_TYPE => Validation::TYPE_INT,
        Validation::ATTR_REQUIRED => true
    ],
    "subtest.sub1" => [
        Validation::ATTR_TYPE => Validation::TYPE_INT,
        Validation::ATTR_REQUIRED => true
    ],
    "subtest.sub2.sub3" => [
        Validation::ATTR_TYPE => Validation::TYPE_DOUBLE,
        Validation::ATTR_REQUIRED => true
    ]
];

//print_r($rules);

$validate = new Validation($data, $subrules);
var_dump($validate->validate());
print_r($validate->getErrors());
