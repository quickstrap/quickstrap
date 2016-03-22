<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi\Config;

abstract class AbstractOptions
{
    /**
     * Cast to array
     *
     * @return mixed[]
     */
    public function toArray()
    {
        $methods = get_class_methods($this);

        $getters = array_filter($methods, function($method) {
            return stripos($method, 'get') === 0;
        });

        $transform = function ($letters) {
            $letter = array_shift($letters);
            return '_' . strtolower($letter);
        };

        $values = [];

        foreach ($getters as $getter) {
            $key = lcfirst(substr($getter, 3));
            $normalizedKey = preg_replace_callback('/([A-Z])/', $transform, $key);
            $values[$normalizedKey] = call_user_func([$this, $getter]);
        }

        return $values;
    }
}
