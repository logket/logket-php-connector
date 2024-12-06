<?php

namespace Logket\Serialize;

class Serializer
{
    public static function serialize($variable, $meta=[]){
        $variable_type = strtolower(gettype($variable));

        return [
            'component' => 'dump',
            'data' => [
                'type' => 'group',
                'children' => self::serializeVariable($variable_type, $variable)
            ],
            'meta' => $meta
        ];
    }

    public static function serializeVariable($type, $value){
        if(in_array($type, ['integer', 'double', 'float'])) return NumberType::serialize($value);
        else if(in_array($type, ['string'])) return StringType::serialize($value);
        else if(in_array($type, ['boolean'])) return BooleanType::serialize($value);
        else if(in_array($type, ['null'])) return NullType::serialize($value);
        else if(in_array($type, ['resource'])) return ResourceType::serialize($value);
        else if(in_array($type, ['array'])) return ArrayType::serialize($value);
        else if(in_array($type, ['object'])) return ObjectType::serialize($value);
        else return UnknownType::serialize($value);
    }
}