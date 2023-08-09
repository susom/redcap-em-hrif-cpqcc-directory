<?php
namespace Cpqcc\MemberDirectory;
require_once('fpdf186/fpdf.php');
require_once "emLoggerTrait.php";

class MemberDirectory extends \ExternalModules\AbstractExternalModule {
    use emLoggerTrait;

    public function __construct() {
        parent::__construct();
        // Other code to run when object is instantiated
    }
}
