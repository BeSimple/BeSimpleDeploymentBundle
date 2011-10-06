<?php

namespace BeSimple\DeploymentBundle\Command;

use Symfony\Component\EventDispatcher\EventInterface;
use BeSimple\DeploymentBundle\Deployer\Deployer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as BaseCommand;
use BeSimple\DeploymentBundle\Events;
use BeSimple\DeploymentBundle\Event\CommandEvent;
use BeSimple\DeploymentBundle\Event\DeployerEvent;
use BeSimple\DeploymentBundle\Event\FeedbackEvent;

abstract class DeploymentCommand extends BaseCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDefinition(array(
                new InputArgument('server', InputArgument::OPTIONAL, 'The target server name', null),
            ))
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deployer = $this->container->get('be_simple_deployment.deployer');
        $eventDispatcher = $this->container->get('event_dispatcher');

        $this->output = $output;
        $this->output->setDecorated(true);

        $self = $this;

        if ($output->getVerbosity() > Output::VERBOSITY_QUIET) {

            // Start event

            $eventDispatcher->addListener(Events::onDeploymentStart, function (DeployerEvent $event) use ($self) {
                $self->write(sprintf(
                	'Starting %s deployment on server "%s"',
                    $event->isTest() ? 'test' : 'real',
                    $event->getServer()
                ));
            });

            // Feedback events

            if ($output->getVerbosity() > Output::VERBOSITY_NORMAL) {
                $eventDispatcher->addListener(Events::onDeploymentRsyncFeedback, function (FeedbackEvent $event) use ($self) {
                    $self->write(sprintf(
                        '[Rsync] %s',
                        $event->getFeedback()
                    ), $event->isError() ? 'error' : 'comment');
                });

                $eventDispatcher->addListener(Events::onDeploymentSshFeedback, function (FeedbackEvent $event) use ($self) {
                    $self->write(sprintf(
                        '[SSH] %s',
                        $event->getFeedback()
                    ), $event->isError() ? 'error' : 'comment');
                });
            }

            // Success events

            $eventDispatcher->addListener(Events::onDeploymentSuccess, function (DeployerEvent $event) use ($self) {
                $self->write(sprintf(
                	'%s deployment on server "%s" succeded',
                    $event->isTest() ? 'Test' : 'Real',
                    $event->getServer()
                ), 'info');
            });

            $eventDispatcher->addListener(Events::onDeploymentRsyncSuccess, function (CommandEvent $event) use ($self) {
                $self->write(sprintf(
                    '[Rsync success] %s',
                    $event->getCommand()
                ));
            }, 'info');

            $eventDispatcher->addListener(Events::onDeploymentSshSuccess, function (CommandEvent $event) use ($self) {
                $self->write(sprintf(
                    '[SSH success] %s',
                    $event->getCommand()
                ));
            }, 'info');
        }

        $this->executeDeployment($deployer, $input->getArgument('server'));
    }

    /**
     * @param string $message
     * @param string $style
     * @return void
     */
    public function write($message, $style = 'comment')
    {
        $this->output->writeln(sprintf('<%s>%s</%s>', $style, $message, $style));
    }

    /**
     * @abstract
     * @param \BeSimple\DeploymentBundle\Deployer\Deployer $deployer
     * @param string $server
     * @return void
     */
    abstract protected function executeDeployment(Deployer $deployer, $server);
}
