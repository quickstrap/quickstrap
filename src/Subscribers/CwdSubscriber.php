<?php


namespace QuickStrap\Subscribers;


use QuickStrap\Helpers\PathHelper;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CwdSubscriber implements EventSubscriberInterface
{
    private $old_working_dir;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => ['onCommand', 10000], // be first
            ConsoleEvents::TERMINATE => ['onTerminate', -10000] // be last
        ];
    }

    public function onCommand(ConsoleCommandEvent $event)
    {
        /** @var PathHelper $path */
        $path = $event->getCommand()->getHelper('path');

        $this->old_working_dir = getcwd();
        $working_dir = $event->getInput()->getOption('project-path');
        $real_working_dir = realpath($working_dir);
        if(!$real_working_dir) {
            $event->getOutput()->writeln(sprintf('The specified project-path "%s" does not exist.', $working_dir));
            $event->stopPropagation();
            $event->disableCommand();
            return;
        }

        $event->getOutput()->writeln(sprintf("Changing directory to %s", $working_dir));
        chdir($real_working_dir);
    }

    public function onTerminate(ConsoleTerminateEvent $event)
    {
        chdir($this->old_working_dir);
    }
}