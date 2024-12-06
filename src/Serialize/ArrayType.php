<?php

namespace Logket\Serialize;

class ArrayType
{
    public static function serialize(array $array){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-pink'],
            'value' => 'array'
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => ':'
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-green'],
            'value' => count($array)
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => ' ['
        ];

        if(count($array)>0){
            $node = [
                'type' => 'collection',
                'items' => []
            ];

            foreach($array as $array_key => $array_value){
                $variable_type = strtolower(gettype($array_value));
                $array_nodes = [];

                if(gettype($array_key)=='integer'){
                    $array_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-blue'],
                        'value' => $array_key
                    ];
                }
                else{
                    $array_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-muted'],
                        'value' => '"'
                    ];
                    
                
                    $array_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-yellow'],
                        'value' => $array_key
                    ];

                    $array_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-muted'],
                        'value' => '"'
                    ];
                }

                $array_nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-muted'],
                    'value' => ' => '
                ];

                $array_nodes = array_merge($array_nodes, Serializer::serializeVariable($variable_type, $array_value));

                $node['items'][] = [
                    'type' => 'group',
                    'children' => $array_nodes
                ];
            }

            $nodes[] = $node;
        }

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => ']'
        ];

        return $nodes;
    }
}