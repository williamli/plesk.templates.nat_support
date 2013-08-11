<?php echo AUTOGENERATED_CONFIGS; ?>

<?php
/**
 * @var Template_VariableAccessor $VAR
 */
?>
<?php if ($VAR->domain->disabled): ?>
# Domain is disabled
<?php return ?>
<?php endif ?>

<?php if ($VAR->domain->physicalHosting->ssl): ?>
<?php foreach ($VAR->domain->physicalHosting->ipAddresses as $ipAddress): ?>

<?php echo $VAR->includeTemplate('domain/nginxDomainVirtualHost.php',
    array(
        'ssl' => true,
        'frontendPort' => $VAR->server->nginx->httpsPort,
        'backendPort' => $VAR->server->webserver->httpsPort,
        'documentRoot' => $VAR->domain->physicalHosting->httpsDir,
        'ipAddress' => $ipAddress,
    )) ?>

<?php endforeach ?>
<?php endif ?>

<?php foreach ($VAR->domain->physicalHosting->ipAddresses as $ipAddress): ?>

<?php echo $VAR->includeTemplate('domain/nginxDomainVirtualHost.php',
    array(
        'ssl' => false,
        'frontendPort' => $VAR->server->nginx->httpPort,
        'backendPort' => $VAR->server->webserver->httpPort,
        'documentRoot' => $VAR->domain->physicalHosting->httpDir,
        'ipAddress' => $ipAddress,
    )) ?>

<?php endforeach ?>
