<?php

namespace App\MyApplication\Services;



use App\MyApplication\RuleValidate;
use Illuminate\Validation\Rule;

class FileRuleValidation extends RuleValidate
{
    public function rules( $isrequired  )
    {
        $req = $isrequired ? "required" : "nullble";
        return [
            "name" => [$req,"string",Rule::unique("files","name")],
            "file" => [$req,"file"],
            "id_group" => ["numeric",Rule::exists("groups","id")],
            "id_file" => ["required","numeric",Rule::exists("files","id")],
            "ids_user" => ["required","array"],
            "ids_user.*" => ["numeric",Rule::exists("users","id")]
        ];
    }

}
