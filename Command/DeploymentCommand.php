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
                new InputOption('tag', 't', InputOption::VALUE_NONE, 'Tag with git if real deployment')
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
        $deployer = $this->getContainer()->get('be_simple_deployment.deployer');
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

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

            $eventDispatcher->addListener(Events::onDeploymentSshStart, function (CommandEvent $event) use ($self) {
                $self->write(sprintf(
                    '[Try SSH Command] %s',
                    $event->getCommand()
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

            if($input->getOption('tag')){
                $eventDispatcher->addListener(Events::onDeploymentSuccess, function (DeployerEvent $event) use ($self) {
                    if(!$event->isTest()){
                        $path = realpath($this->getApplication()->getKernel()->getRootDir().'/..');
                        if(is_dir($path)){
                            $tag = 'deploy-'. strtolower($event->getServer());

                            $self->write(sprintf(
                                'Trying to set GIT tag %s in %s',
                                $tag,
                                $path
                            ), 'comment');

                            $gitCmd = sprintf(
                                'git --git-dir=%s --work-tree=%s',
                                $path.'/.git',
                                $path
                            );

                            $status = shell_exec($gitCmd.' status -s');
                            if($status !== null){
                                $self->write(sprintf('There are uncommitted changes - wont set the tag: %s', $status), 'error');
                                return;
                            }

                            $commit = shell_exec($gitCmd.' rev-parse HEAD');
                            if(!$commit){
                                $self->write('Last commit-hash not found for HEAD', 'error');
                                return;
                            }

                            $tagResult  = shell_exec($gitCmd.' tag -f '. $tag .' '. $commit);
                            if(!$tagResult){
                                $self->write('Tagging seems ok. Try "git push --tags"', 'info');
                                return;
                            }

                            $self->write($tagResult, 'info');
                        }
                    }
                });
            }

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
