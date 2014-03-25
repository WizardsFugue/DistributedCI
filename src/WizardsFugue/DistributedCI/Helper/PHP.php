<?php
/**
 * 
 * 
 * 
 * 
 */

namespace WizardsFugue\DistributedCI\Helper;


use SystemProcess\SystemProcess;

class PHP {

    protected $toolDir;
    protected $projectDir;
    protected $resultDir;
    
    public function __construct( $toolDir, $projectDir, $resultDir )
    {
        $this->toolDir    = $toolDir;
        $this->projectDir = $projectDir;
        $this->resultDir  = $resultDir;

        $directories = array(
            $this->resultDir.'/composer/',
            $this->resultDir.'/phploc/',
        );
        foreach( $directories as $directory ){
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
        }
    }

    public function prepareComposerProject()
    {
        if( file_exists('vendor') ){

            $process = new SystemProcess('rm -rf vendor');
            $process->workingDirectory($this->projectDir);
            $process->execute();
        }
        $process = new SystemProcess('composer.phar install --dev --optimize-autoloader --no-progress');
        $process->workingDirectory($this->projectDir);
        $process->execute();
        file_put_contents( $this->resultDir.'/composer/install.log', $process->stdoutOutput );
        file_put_contents( $this->resultDir.'/composer/install.err', $process->stderrOutput );
    }
    
    public function phploc()
    {
        $xmlLog  = $this->resultDir.'/phploc/phploc.xml';
        $exclude = ' --exclude="not_used" --exclude="vendor" ';
        $process = new SystemProcess('./vendor/bin/phploc '.$exclude.' --log-xml='.$xmlLog.' --count-tests  '.$this->projectDir);
        $process->workingDirectory($this->toolDir);
        $process->execute();
        file_put_contents( $this->resultDir.'/phploc/execute.log', $process->stdoutOutput );
        file_put_contents( $this->resultDir.'/phploc/execute.err', $process->stderrOutput );
    }
    
    public function getPhplocStats()
    {
        $xmlLog  = $this->resultDir.'/phploc/phploc.xml';
        $result = null;
        if( file_exists($xmlLog) ){
            $result = simplexml_load_file($xmlLog);
        }
        return $result;
    }
    
    
} 