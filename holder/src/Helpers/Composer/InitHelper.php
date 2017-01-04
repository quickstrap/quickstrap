<?php


namespace QuickStrap\Helpers\Composer;


use Composer\Command\InitCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class InitHelper extends AbstractComposerHelper
{
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'composer init';
    }


    public function initComposer(OutputInterface $output)
    {
        $input = new ArgvInput([
            'composer', 'init'
        ]);
        $input->setInteractive(true);

        /** @var InitCommand $command */
        $command = $this->getHelperSet()->getCommand()->getApplication()->find('init');

        return $this->execute($input, $output, $command);
    }
}