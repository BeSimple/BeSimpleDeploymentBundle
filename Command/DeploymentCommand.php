<?php

namespace BeSimple\DeploymentBundle\Command;

use Symfony\Component\EventDispatcher\EventInterface;
use Bundle\DeploymentBundle\Deployer\Deployer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

abstract class DeploymentCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('server', InputArgument::OPTIONAL, 'The target server name', null),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deployer = $this->container->get('besimple_deployment.deployer');
        $eventDispatcher = $this->container->get('event_dispatcher');

        $this->output = $output;
        $this->output->setDecorated(true);

        if ($output->getVerbosity() > Output::VERBOSITY_QUIET) {
            $dispatcher->connect('besimple_deployment.start', function ($event) use ($that) { $that->write($event, 'start'); });
            $dispatcher->connect('besimple_deployment.error', function ($event) use ($that) { $that->write($event, 'error'); });
            $dispatcher->connect('besimple_deployment.success', function ($event) use ($that) { $that->write($event, 'success'); });

            if ($output->getVerbosity() > Output::VERBOSITY_NORMAL) {
                $dispatcher->connect('besimple_deployment.rsync', function ($event) use ($that) { $that->write($event, 'rsync'); });
            }

            // TODO: add SSH events
        }

        $this->executeDeployment($deployer, $input->getArgument('server'));
    }

    public function write(EventInterface $event, $type)
    {
        switch ($type) {

            case 'start':
                $style = 'comment';
                $message = sprintf(
                	'Starting %s deployment on server "%s"',
                    $event->get('real') ? 'real' : 'test',
                    $event->get('server')
                );
                break;

            case 'error':
                $style = 'error';
                $message = $event->get('message');
                break;

            case 'success':
                $style = 'info';
                $message = 'Deployment success';
                break;

            case 'rsync':
                $style = $event->get('type') === 'out' ? 'info' : 'error';
                $message = $event->get('line');
                break;
        }

        $this->output->writeln('<%s>%s</%s>', $style, $message, $style);
    }

    abstract protected function executeDeployment(Deployer $deployer, $server);
}