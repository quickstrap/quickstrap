<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi\Config;

class Config extends AbstractOptions
{
    /** @var  string */
    private $language;
    
    /** @var  string[] */
    private $php = [];

    /** @var  string[] */
    private $before_script = [];

    /** @var  string[] */
    private $script = [];

    /** @var  Matrix */
    private $matrix;

    /**
     * Configuration constructor.
     *
     * @param string $language
     * @param  mixed[]|null $options
     */
    public function __construct($language = 'php', $options = null)
    {
        $this->setLanguage($language);
        
        $this->matrix = new Matrix();
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = strtolower($language);
    }

    /**
     * @return string[]
     */
    public function getPhp()
    {
        return $this->php;
    }

    /**
     * @param string[] $phpVersions
     */
    public function setPhp(array $phpVersions)
    {
        $this->php = [];

        array_walk($phpVersions, [$this, 'addPhp']);
    }

    /**
     * @param string $phpVersion
     */
    public function addPhp($phpVersion)
    {
        if (!in_array($phpVersion, $this->php, false)) {
            $this->php[] = $phpVersion;
        }
    }

    /**
     * @return string[]
     */
    public function getBeforeScript()
    {
        return $this->before_script;
    }

    /**
     * @param string[] $before_script
     */
    public function setBeforeScript(array $before_script)
    {
        $this->before_script = [];

        array_walk($before_script, [$this, 'addBeforeScript']);
    }

    /**
     * @param string $before_script
     */
    public function addBeforeScript($before_script)
    {
        $this->before_script[] = $before_script;
    }

    /**
     * @return string[]
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @param string[] $script
     */
    public function setScript(array $script)
    {
        $this->script = [];

        array_walk($script, [$this, 'addScript']);
    }

    /**
     * @param string $script
     */
    public function addScript($script)
    {
        $this->script[] = $script;
    }

    /**
     * @return Matrix
     */
    public function getMatrix()
    {
        return $this->matrix;
    }

    /**
     * @param Matrix $matrix
     */
    public function setMatrix(Matrix $matrix)
    {
        $this->matrix = $matrix;
    }
}
