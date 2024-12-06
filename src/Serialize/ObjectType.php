<?php

namespace Logket\Serialize;

use Reflection;
use ReflectionClass;
use ReflectionFunction;

class ObjectType
{
    public static function serialize($object){
        $nodes = [];

        if(is_callable($object)){
            $reflection = new ReflectionFunction($object);

            $nodes[] = [
                'type' => 'text',
                'style' => ['font-color-pink'],
                'value' => get_class($object)
            ];

            $nodes[] = [
                'type' => 'text',
                'style' => ['font-color-muted'],
                'value' => ' ('
            ];

            $reflection_parameters = $reflection->getParameters();

            if(count($reflection_parameters)>0){
                $nodes[] = [
                    'type' => 'collection',
                    'items' => self::serializeParameters($reflection_parameters)
                ];
            }

            $nodes[] = [
                'type' => 'text',
                'style' => ['font-color-muted'],
                'value' => ')'
            ];
        }
        else{
            $reflection = new ReflectionClass($object);

            $reflection_exenteds = $reflection->getParentClass();
            $reflection_interfaces = [];

            foreach($reflection->getInterfaces() as $reflection_interface){
                $reflection_interfaces[] = $reflection_interface->getName();
            }

            $nodes[] = [
                'type' => 'text',
                'style' => ['font-color-pink'],
                'value' => get_class($object)
            ];

            if($reflection_exenteds){
                $nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-muted'],
                    'value' => ' extends '
                ];

                $nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-green'],
                    'value' => $reflection_exenteds->getName()
                ];
            }

            if($reflection_interfaces){
                $nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-muted'],
                    'value' => ' implements '
                ];

                $nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-green'],
                    'value' => implode(',', $reflection_interfaces)
                ];
            }

            $nodes[] = [
                'type' => 'text',
                'style' => ['font-color-muted'],
                'value' => ' {'
            ];

            $node = [
                'type' => 'collection',
                'items' => []
            ];

            $reflection_property_names = [];
            $current_property_reflection = $reflection;

            do{
                $reflection_properties = $current_property_reflection->getProperties();

                foreach($reflection_properties as $reflection_property){
                    if(!in_array($reflection_property->getName(), $reflection_property_names)){
                        $property_value = $reflection_property->getValue($object);
                        $property_type = gettype($property_value);
                        $property_nodes = [];

                        $modifers = Reflection::getModifierNames($reflection_property->getModifiers());

                        foreach($modifers as $modifer){
                            $property_nodes[] = [
                                'type' => 'text',
                                'style' => ['font-color-green'],
                                'value' => $modifer.' '
                            ];
                        }

                        $property_nodes[] = [
                            'type' => 'text',
                            'style' => ['font-color-white'],
                            'value' => '$'.$reflection_property->getName()
                        ];

                        $property_nodes[] = [
                            'type' => 'text',
                            'style' => ['font-color-muted'],
                            'value' => ': '
                        ];

                        $property_nodes = array_merge($property_nodes, Serializer::serializeVariable($property_type, $property_value));

                        $node['items'][] = [
                            'type' => 'group',
                            'children' => $property_nodes
                        ];

                        $reflection_property_names[] = $reflection_property->getName();
                    }
                }

                $current_property_reflection = $current_property_reflection->getParentClass();
            } 
            while($current_property_reflection);

            $node['items'][] = [
                'type' => 'group',
                'children' => [['type' => 'space']]
            ];

            foreach(get_object_vars($object) as $var_name => $var_value){
                if(!in_array($var_name, $reflection_property_names)){
                    $var_type = gettype($var_value);
                    $var_nodes = [];

                    $var_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-white'],
                        'value' => '$'.$var_name
                    ];

                    $var_nodes[] = [
                        'type' => 'text',
                        'style' => ['font-color-muted'],
                        'value' => ': '
                    ];

                    $var_nodes = array_merge($var_nodes, Serializer::serializeVariable($var_type, $var_value));

                    $node['items'][] = [
                        'type' => 'group',
                        'children' => $var_nodes
                    ];
                }
            }

            $node['items'][] = [
                'type' => 'group',
                'children' => [['type' => 'space']]
            ];

            $reflection_method_names = [];
            $current_method_reflection = $reflection;

            do{
                $reflection_methods = $current_method_reflection->getMethods();

                foreach($reflection_methods as $reflection_method){
                    if(!in_array($reflection_method->getName(), $reflection_method_names)){
                        $method_nodes = [];

                        $modifers = Reflection::getModifierNames($reflection_method->getModifiers());

                        foreach($modifers as $modifer){
                            $method_nodes[] = [
                                'type' => 'text',
                                'style' => ['font-color-green'],
                                'value' => $modifer.' '
                            ];
                        }

                        $method_nodes[] = [
                            'type' => 'text',
                            'style' => ['font-color-pink'],
                            'value' => 'function '
                        ];

                        $method_nodes[] = [
                            'type' => 'text',
                            'style' => ['font-color-blue'],
                            'value' => $reflection_method->getName()
                        ];

                        $method_nodes[] = [
                            'type' => 'text',
                            'style' => ['font-color-muted'],
                            'value' => ' ('
                        ];

                        $reflection_method_parameters = $reflection_method->getParameters();

                        if(count($reflection_method_parameters)>0){
                            $method_nodes[] = [
                                'type' => 'collection',
                                'items' => self::serializeParameters($reflection_method_parameters)
                            ];
                        }

                        $method_nodes[] = [
                            'type' => 'text',
                            'style' => ['font-color-muted'],
                            'value' => ')'
                        ];

                        $node['items'][] = [
                            'type' => 'group',
                            'children' => $method_nodes
                        ];

                        $reflection_method_names[] = $reflection_property->getName();
                    }
                }

                $current_method_reflection = $current_method_reflection->getParentClass();
            }
            while($current_method_reflection);

            if(isset($node['items'])) $nodes[] = $node;

            $nodes[] = [
                'type' => 'text',
                'style' => ['font-color-muted'],
                'value' => '}'
            ];
        }

        return $nodes;
    }
    
    private static function serializeParameters($parameters){
        $nodes = [];

        foreach($parameters as $parameter){
            $parameter_nodes = [];

            if($parameter->getType()){
                $parameter_nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-pink'],
                    'value' => $parameter->getType()->getName().' '
                ];
            }

            $parameter_nodes[] = [
                'type' => 'text',
                'style' => ['font-color-white'],
                'value' => '$'.$parameter->getName()
            ];

            if($parameter->isOptional()){
                $parameter_value = $parameter->getDefaultValue();
                $parameter_type = gettype($parameter_value);

                $parameter_nodes[] = [
                    'type' => 'text',
                    'style' => ['font-color-muted'],
                    'value' => ' = '
                ];

                $parameter_nodes = array_merge($parameter_nodes, Serializer::serializeVariable($parameter_type, $parameter_value));
            }

            $nodes[] = [
                'type' => 'group',
                'children' => $parameter_nodes
            ];
        }

        return $nodes;
    }
}