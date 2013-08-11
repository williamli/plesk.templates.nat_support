<?php echo AUTOGENERATED_CONFIGS; ?>
<?php
    $ipAddresses = $VAR->server->ipAddresses->all;
    $ipLimit = $VAR->server->webserver->apache->vhostIpCapacity;
    $roundcubeDocroot = $VAR->server->webserver->roundcube->docroot;
    $roundcubeConfD = "/etc/psa-webmail/roundcube";
    $roundcubeSysUser = "roundcube_sysuser";
    $roundcubeSysGroup = "roundcube_sysgroup";
    $domainsBootstrap = $VAR->server->webserver->httpConfDir . "/plesk.conf.d/webmails/roundcube/*.conf";
    $roundcubeHtaccess = $VAR->server->webserver->httpConfDir . "/plesk.conf.d/roundcube.htaccess.inc";
    $roundcubePhpIni = $roundcubeConfD . "/php.ini";
    $modPHPAvailiable = $VAR->server->php->ModAvailable;

?>

<?php
    for($ipAddress = reset($ipAddresses);
        $ipAddress;
        $ipAddress = next($ipAddresses)):
?>
<VirtualHost \
    <?php echo "{$ipAddress->escapedAddress}:{$VAR->server->webserver->httpPort}" ?> \
    <?php for ($n = 1;
            $n < $ipLimit && $ipAddress = next($ipAddresses);
            ++$n):
    ?>
    <?php echo "{$ipAddress->escapedAddress}:{$VAR->server->webserver->httpPort}" ?> \
    <?php endfor; ?>
    <?php echo ($VAR->server->webserver->proxyActive) ? "127.0.0.1:" . $VAR->server->webserver->httpPort : ''; ?> \
    >
    ServerName roundcube.webmail
    ServerAlias roundcube.webmail.*
    ServerAdmin "<?php echo $VAR->server->admin->email ?>"

    Include "<?php echo $domainsBootstrap ?>"
    UseCanonicalName Off

    DocumentRoot "<?php echo $roundcubeDocroot ?>"
    Alias /roundcube/ "<?php echo $roundcubeDocroot ?>"

    <IfModule mod_suexec.c>
        SuexecUserGroup <?php echo $roundcubeSysUser; ?> <?php echo $roundcubeSysGroup; ?>

    </IfModule>

    <IfModule mod_fcgid.c>
            FcgidInitialEnv PP_CUSTOM_PHP_CGI_INDEX fastcgi
            FcgidInitialEnv PP_CUSTOM_PHP_INI "<?php echo $roundcubePhpIni; ?>"
            FcgidMaxRequestLen 134217728
        <Directory "<?php echo $roundcubeDocroot ?>">
            Options -Indexes FollowSymLinks
            AllowOverride FileInfo
            Order allow,deny
            Allow from all
            Include "<?php echo $roundcubeHtaccess ?>"

            <Files ~ (\.php$)>
                SetHandler fcgid-script
                FCGIWrapper <?php echo $VAR->server->webserver->apache->phpCgiBin ?> .php
                Options +ExecCGI
            </Files>
        </Directory>
    </IfModule>

    <?php echo $VAR->includeTemplate('domain/PCI_compliance.php') ?>

</VirtualHost>
<?php endfor; ?>

<IfModule mod_ssl.c>
<?php
    for($ipAddress = reset($ipAddresses);
        $ipAddress;
        $ipAddress = next($ipAddresses)):
?>
<?php if ($ipAddress->sslCertificate->ce): ?>
<VirtualHost \
    <?php echo "{$ipAddress->escapedAddress}:{$VAR->server->webserver->httpsPort}" ?> \
    <?php echo ($VAR->server->webserver->proxyActive) ? "127.0.0.1:" . $VAR->server->webserver->httpsPort : ''; ?> \
    >
    ServerName roundcube.webmail
    ServerAlias roundcube.webmail.*
    ServerAdmin "<?php echo $VAR->server->admin->email ?>"

    Include "<?php echo $domainsBootstrap ?>"
    UseCanonicalName Off

    DocumentRoot "<?php echo $roundcubeDocroot ?>"
    Alias /roundcube/ "<?php echo $roundcubeDocroot ?>"

    SSLEngine on
    SSLVerifyClient none
    SSLCertificateFile "<?php echo $ipAddress->sslCertificate->ceFilePath ?>"

    <IfModule mod_suexec.c>
        SuexecUserGroup <?php echo $roundcubeSysUser; ?> <?php echo $roundcubeSysGroup; ?>

    </IfModule>

    <IfModule mod_fcgid.c>
            FcgidInitialEnv PP_CUSTOM_PHP_CGI_INDEX fastcgi
            FcgidInitialEnv PP_CUSTOM_PHP_INI "<?php echo $roundcubePhpIni; ?>"
            FcgidMaxRequestLen 134217728
        <Directory "<?php echo $roundcubeDocroot ?>">
            Options -Indexes FollowSymLinks
            AllowOverride FileInfo
            Order allow,deny
            Allow from all
            Include "<?php echo $roundcubeHtaccess ?>"

            <Files ~ (\.php$)>
                SetHandler fcgid-script
                FCGIWrapper <?php echo $VAR->server->webserver->apache->phpCgiBin ?> .php
                Options +ExecCGI
            </Files>
        </Directory>
    </IfModule>

    <?php echo $VAR->includeTemplate('domain/PCI_compliance.php') ?>

</VirtualHost>
<?php endif; ?>
<?php endfor; ?>
</IfModule>