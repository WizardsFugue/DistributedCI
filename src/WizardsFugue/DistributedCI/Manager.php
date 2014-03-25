<?php
/**
 * 
 * 
 * 
 * 
 */

namespace WizardsFugue\DistributedCI;


use Arbit\VCSWrapper\GitCli\Checkout;
use Symfony\Component\Console\Output\OutputInterface;
use WizardsFugue\DistributedCI\Helper\PHP;

class Manager {

    const META_DATA_FILE = '.distributedCI.json';
    
    protected $config;
    
    protected function getToolRoot(){
        return realpath(__DIR__.'/../../..');
    }
    
    protected function getVarDir(){
        return $this->getToolRoot().'/var';
    }
    
    protected function getProjectRepositoryDir(){
        return $this->getVarDir().'/project/repository/';
    }
    
    protected function getPublicResultDir(){
        return $this->getToolRoot().'/public_result/';
    }
    
    public function __construct($config = array())
    {
        $this->config = $config;
        if( empty($this->config) ){
            $this->config = json_decode(file_get_contents( $this->getToolRoot().'/config.json'));
        }
    }
    
    public function addResultForCommit( \ZipArchive $zip )
    {
        $metadata = $zip->getFromName(self::META_DATA_FILE);
        if($metadata === false){
            throw new \Exception('no metadata file found');
        }
        $metadata = json_decode($metadata);
        
        $commitId = $metadata['commitID'];
        
    }
    
    
    public function updateGitInformations()
    {
        $git = $this->getProjectGit();
        try{
            $git->initialize( $this->config->projectRepository );
        }catch( \RuntimeException $e){
            $git->update();
        }
    }
    
    public function getVersions()
    {
        $git = $this->getProjectGit();
        return $git->getVersions();
    }
    
    protected function getProjectGit()
    {
        return new Checkout( $this->getProjectRepositoryDir() );
    }
    
    public function gatherStats( OutputInterface $output = null )
    {
        
        foreach($this->getVersions() as $index=>$version ){
            if($output){
                $output->writeln(date('H:i:s').' gather stats for version: '.$version);
            }
            $this->gatherStatsForVersion($version);
        }
    }
    
    public function gatherStatsForVersion( $version )
    {
        $index = array_search( $version, $this->getVersions() );
        
        $resultDir = $this->getVarDir().'/result/'.$version;

        $git = $this->getProjectGit();
        $git->update($version);
        
        $helper = new PHP($this->getToolRoot(), $this->getProjectRepositoryDir(), $resultDir );
        
        $helper->prepareComposerProject();
        $helper->phploc();
        
        
    }
    
    public function generateStatistics( OutputInterface $output = null ){
        $tsv = fopen( $this->getPublicResultDir()."/data.tsv", "w");
        fwrite($tsv, "Version\tlloc\tllocClasses\tllocFunctions\tllocGlobal".PHP_EOL);
        foreach($this->getVersions() as $index=>$version ){
            if($output){
                $output->writeln(date('H:i:s').' summarize stats for Index/version: '.$index."\t/".$version);
            }
            $resultDir = $this->getVarDir().'/result/'.$version;
            $helper = new PHP($this->getToolRoot(), $this->getProjectRepositoryDir(), $resultDir );
            $stats = $helper->getPhplocStats();
            if($stats===null){
                continue;
            }
            $stats = array(
                $index,
                $stats->lloc,
                $stats->llocClasses,
                $stats->llocFunctions,
                $stats->llocGlobal,
            );
            $stats = implode("\t",$stats);
            fwrite($tsv, $stats.PHP_EOL);
        }
    }
    
    
} 