<?php


namespace QuickStrap\Helpers;


use Symfony\Component\Console\Helper\Helper;

class PathHelper extends Helper
{
    /** @var string */
    private $projectPath;
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'path';
    }

    /**
     * Get the full path (for file operations) to a file relative to the project
     * @param string $relativePath relative path to file
     * @return string
     */
    public function getPath($relativePath)
    {
        return sprintf("%s%s%s", $this->getProjectPath(), DIRECTORY_SEPARATOR, $relativePath);
    }

    /**
     * @internal this method should only be used by CwdSubscriber
     * @param $absolutePathToProject
     */
    public function setProjectPath($absolutePathToProject)
    {
        $this->projectPath = $absolutePathToProject;
    }

    protected function getProjectPath()
    {
        return $this->projectPath ?: getcwd();
    }
}