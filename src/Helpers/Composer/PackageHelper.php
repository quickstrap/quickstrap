<?php


namespace QuickStrap\Helpers\Composer;

use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\DependencyResolver\Pool;
use Composer\Semver\VersionParser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackageHelper extends AbstractComposerHelper
{
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'package';
    }

    public function hasPackage($name, $version = null, InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getComposer($input, $output)->getRepositoryManager()->getLocalRepository();

        $matches = $this->getPackageMatches($repository, $name, $version);

        foreach($matches as $package) {
            if ($repository->hasPackage($package)) {
                return true;
            }
        }
        return false;
    }

    private function getPackageMatches($repository, $name, $version = null)
    {
        $pool = new Pool('dev');
        $pool->addRepository($repository);

        $constraint = null;
        if ($version) {
            $parser = new VersionParser;
            $constraint = $parser->parseConstraints($version);
        }


        $matchedPackage = null;
        return $pool->whatProvides($name, $constraint);
    }

    public function getPackageVersion($name, InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getComposer($input, $output)->getRepositoryManager()->getLocalRepository();

        $matches = $this->getPackageMatches($repository, $name);

        foreach($matches as $package) {
            if ($repository->hasPackage($package)) {
                return $package->getPrettyVersion();
            }
        }
        return '';
    }

    private function getComposer(InputInterface $input, OutputInterface $output) {
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = Factory::create($io, 'composer.json', true);
        return $composer;
    }


}