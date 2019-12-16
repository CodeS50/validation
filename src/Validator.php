<?php

namespace Codes50;

use Codes50\Core\CoreValidator;

final class Validator extends CoreValidator
{
    public function __construct(array $data = [], array $rules = [])
    {
        parent::__construct($data, $rules);
    }
}

