<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi\Config;

use Symfony\Component\Yaml\Yaml;

class ConfigWriter
{
    /**
     * @param Config $config
     * @return int
     */
    public function toYml(Config $config)
    {
        $values = $this->toArray($config);
        
        return Yaml::dump($values, $inline = 3, $indent = 4);
    }

    /**
     * @param Config $config
     * @param string $filename
     * @return int
     */
    public function toYmlFile(Config $config, $filename)
    {
        $yml = $this->toYml($config);

        return file_put_contents($filename, $yml);
    }
    
    /**
     * @param AbstractOptions $options
     * @return mixed[]
     */
    private function toArray(AbstractOptions $options)
    {
        $values = $options->toArray();

        $mappedValues = array_map(function ($value) {
            // Recursively extract values from the config.
            if ($value instanceof AbstractOptions) {
                return self::toArray($value);
            }

            // Otherwise, simply return the value.
            return $value;
        }, $values);
        
        // Filter out empty arrays.
        $filteredValues = array_filter($mappedValues, function($value) {
            if (is_array($value) && count($value) < 1) {
                return false;
            }
            
            return true;
        });
        
        return $filteredValues;
    }
}
