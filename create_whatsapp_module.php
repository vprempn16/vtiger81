<?php
/**
 * Script to create/update the Whatsapp module and its infrastructure in Vtiger 8.1
 */

include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'modules/Whatsapp/WhatsappCustom.php';

class WhatsappModuleSetup
{
    public function run()
    {
        echo "Starting WhatsApp Module Setup...<br>";

        $custom = new WhatsappCustom();



        echo "Initializing Module and Fields...<br>";
        $custom->postInstall();

        echo "Setup completed successfully.<br>";
    }
}

// Execute setup
$setup = new WhatsappModuleSetup();
$setup->run();
