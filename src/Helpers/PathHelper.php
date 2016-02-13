<?php


namespace QuickStrap\Helpers;


use Symfony\Component\Console\Helper\Helper;

class PathHelper extends Helper
{

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
        return sprintf("%s%s%s", getcwd(), DIRECTORY_SEPARATOR, $relativePath);
    }
}