<?php

namespace App\MyApplication\Services;


use App\MyApplication\RuleValidate;
use Illuminate\Validation\Rule;

class GroupRuleValidation extends RuleValidate
{
    public function rules( $isrequired )
    {
        $req = $isrequired ? "required" : "nullble";

        return [
            "name" => [$req,"string",Rule::unique("groups","name")],
            "type" => [$req,"string",Rule::in(["private","public"])],
            "id_group" => ["required",Rule::exists("groups","id")],
        ];
    }
}
