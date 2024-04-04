<?php

namespace App\MyApplication;


abstract class RuleValidate
{
    public abstract function rules(bool $isrequired );

    public function except(array $keys,bool $isrequired ){
        $arr = $this->rules(true);
        foreach ($keys as $key){
            if (array_key_exists($key,$arr)){
                unset($arr[$key]);
            }
        }
        return $arr;
    }

    public function onlyKey(array $keys,bool $isrequired ){
        $arr = [];
        foreach ($keys as $key){
            if (array_key_exists($key,$this->rules($isrequired))){
                $arr[$key] = $this->rules($isrequired)[$key];
            }
        }
        return $arr;
    }
}
