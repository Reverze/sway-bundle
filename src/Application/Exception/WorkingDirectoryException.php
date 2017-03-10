<?php

namespace SwayBundle\Application\Exception;

class WorkingDirectoryException extends \Exception
{
    private $invalidWorkingDirectory = null;
    
    public function __construct(string $invalidWorkingDirectoryPath = null) {
        $this->invalidWorkingDirectory = (string) $invalidWorkingDirectoryPath;
        
        parent::__construct("Directory not found on path: '$this->invalidWorkingDirectory'");
    }
}

?>

