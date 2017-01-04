<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi\Config;

class Matrix extends AbstractOptions
{
    /** @var  string[] */
    private $allow_failures = [];

    /**
     * @return string[]
     */
    public function getAllowFailures()
    {
        return $this->allow_failures;
    }

    /**
     * @param string $language
     * @param string[] $builds
     */
    public function setAllowFailures($language, array $builds)
    {
        $this->allow_failures = [];
        
        foreach ($builds as $build) {
            $this->addAllowFailure($language, $build);
        }
    }

    /**
     * @param string $language
     * @param string $build
     * @return bool
     */
    public function addAllowFailure($language, $build)
    {
        foreach ($this->allow_failures as $allow_failure) {
            if (array_key_exists($language, $allow_failure) && $allow_failure[$language] === $build) {
                return false;
            }
        }
        
        $this->allow_failures[] = [
            $language => $build
        ];
        
        return true;
    }
}
