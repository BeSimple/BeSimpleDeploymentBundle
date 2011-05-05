<?php

namespace BeSimple\DeploymentBundle;

final class Events
{
    // Deployer
    const onDeploymentStart   = 'onDeploymentStart';
    const onDeploymentSuccess = 'onDeploymentSuccess';

    // Rsync
    const onDeploymentRsyncStart    = 'onDeploymentRsyncStart';
    const onDeploymentRsyncFeedback = 'onDeploymentRsyncFeedback';
    const onDeploymentRsyncSuccess  = 'onDeploymentRsyncSuccess';

    // Ssh
    const onDeploymentSshStart    = 'onDeploymentSshStart';
    const onDeploymentSshFeedback = 'onDeploymentSshFeedback';
    const onDeploymentSshSuccess  = 'onDeploymentSshSuccess';
}
