<?php
namespace Crawler;

class Report 
{    
    /**
     * Render html and create report
     * @param array $params
     */
    public function init($params = array()){
        $tpl = 'vendor'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, strtolower(__CLASS__)).'.phtml';
        
        if(is_file($tpl)){
            $render = function($params, $tpl){
                           foreach($params as $key => $val){
                               if($key != 'tpl'){   
                                   $$key = $val;
                               }
                           }
                           ob_start();
                               include($tpl);
                           return ob_get_clean();
                       };
                      
            $report = 'reports'.DIRECTORY_SEPARATOR.'report_'.date('d.m.Y').'.html';
            file_put_contents($report, $render($params, $tpl));
            
            echo('Created: '.$report."\n");
        }
    }
} 
