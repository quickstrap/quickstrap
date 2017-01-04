<?php


namespace QuickStrapUnit\Helpers;


use org\bovigo\vfs\vfsStream;
use QuickStrap\Helpers\PathHelper;

class PathHelperTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_path_returns_full_path()
    {
        $root = vfsStream::setup('projectDir');

        $helper = new PathHelper();
        $helper->setProjectPath($root->url());
        $fullPath = $helper->getPath($relative = 'relative/path.txt');
        static::assertEquals(
            $root->url() . DIRECTORY_SEPARATOR . $relative,
            $fullPath
        );
    }
}
