<?php


namespace QuickStrap\Subscribers;

use Composer\Command\InitCommand;
use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ComposerSetupSubscriber implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand'
        ];
    }

    public function onCommand(ConsoleCommandEvent $event)
    {
        $project_path = $event->getInput()->getOption('project-path');
        $composer_json_path = sprintf("%s%s%s", $project_path, DIRECTORY_SEPARATOR, 'composer.json');
        if (file_exists($composer_json_path)) {
            return;
        }

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $event->getCommand()->getHelper('question');
        $question = new ConfirmationQuestion('Composer has not been initialized, initialize composer now? ', true);
        if(! $questionHelper->ask($event->getInput(), $event->getOutput(), $question)) {
            $event->getOutput()->writeln('Skipping composer init, if the command fails then you should try initializing composer.');
            return;
        }



        $input = new ArgvInput([
            'composer', 'init'
        ]);
        $input->setInteractive(true);

        /** @var InitCommand $command */
        $command = $event->getCommand()->getApplication()->find('init');
        $command->setIO(new ConsoleIO($input, $event->getOutput(), $event->getCommand()->getHelperSet()));

        $application = new Application();
        $application->add($command);
        $application->setAutoExit(false);

        if ($application->run($input, $event->getOutput()) !== 0) {
            $event->getOutput()->writeln("Composer initialization failed.");
            $event->getOutput()->writeln("Please initialize a composer package manually before trying again.");

            $event->disableCommand();
            $event->stopPropagation();
        }
    }
}