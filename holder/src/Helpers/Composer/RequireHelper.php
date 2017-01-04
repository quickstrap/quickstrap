<?php


namespace QuickStrap\Helpers\Composer;


use Composer\Command\RequireCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class RequireHelper extends AbstractComposerHelper
{
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'composer require';
    }

    public function requirePackage(OutputInterface $output, $package, $version = null, $dev = true)
    {
        $packageArg = sprintf("%s%s", $package, ($version != null ? ':'.$version : null));

        $args = [
            'composer', 'require'
        ];

        if ($dev) {
            $args[] = '--dev';
        }

        $args[] = $packageArg;

        $input = new ArgvInput($args);
        $input->setInteractive(true);

        /** @var RequireCommand $command */
        $command = $this->getHelperSet()->getCommand()->getApplication()->find('require');

        return $this->execute($input, $output, $command);
    }
}