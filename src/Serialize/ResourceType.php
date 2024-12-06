<?php

namespace Logket\Serialize;

class ResourceType
{
    public static function serialize(mixed $resource){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-green'],
            'value' => get_resource_type($resource)
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-pink'],
            'value' => ' resource'
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => ' {'
        ];

        if(get_resource_type($resource)=='stream'){
            $stream_meta_data = stream_get_meta_data($resource);

            if(count($stream_meta_data)>0){
                $node = [
                    'type' => 'collection',
                    'items' => []
                ];

                foreach(stream_get_meta_data($resource) as $resource_key => $resource_value){
                    $variable_type = strtolower(gettype($resource_key));
                    $resource_nodes = [];

                    $resource_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-white'],
                        'value' => $resource_key
                    ];

                    $resource_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-muted'],
                        'value' => ': '
                    ];

                    $resource_nodes = array_merge($resource_nodes, Serializer::serializeVariable($variable_type, $resource_value));

                    $node['items'][] = [
                        'type' => 'group',
                        'children' => $resource_nodes
                    ];
                }

                $nodes[] = $node;
            }
        }

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => '}'
        ];

        return $nodes;
    }
}