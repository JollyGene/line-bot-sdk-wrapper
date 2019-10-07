<?php

namespace JollyGene\Utils;

use Validator;
use Illuminate\Validation\Rule;

class LINEBotUtil
{
    /**
     * Validate array
     * This funciton throws exception when validation faild.
     *
     * @param array $array
     * @param array $rule
     * @return void
     */
    public function validate($array, $rule)
    {
        $validator = Validator::make($array, $rule);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->__toString());
        }
    }
}