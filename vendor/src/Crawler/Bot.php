<?php
namespace Crawler;

class Bot 
{
    private $imgCount = array();
    private $urlParams = array();
    
    /**
     * Init bot
     */
    public function init() {
        $url = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
        $url = $this->firstUrl($url);
        $this->urlScan($url);
        
        // Sort result by descending
        natsort($this->imgCount);
        $this->imgCount = array_reverse($this->imgCount);
        
        $all = array_sum($this->imgCount);
        
        // echo all img count
        echo('All images: '.$all."\n");
        
        $report = new Report();
        $report->init(array(
            'baseUrl' => $url,
            'imgCount' => $this->imgCount,
            'allCount' => $all
        ));
    }
    
    /**
     * Recursive scanning url
     * @param string $url
     */
    private function urlScan($url = null){
        if(!empty($url)){
            $url = $this->checkURL($url);
            if($url !== null && !isset($this->imgCount[$url])){            

                // get html content from url
                $html = @file_get_contents($url);

                if($html){
                    $imagesMatch = null;
                    $urlsMatch = null;
                    
                    // get "img" and "a"
                    preg_match_all('/\<img\s.+?\>/i', $html, $imagesMatch);
                    preg_match_all('/\<a\s.+?\>/i', $html, $urlsMatch);

                    $count = isset($imagesMatch[0]) ? count($imagesMatch[0]) : 0;
                    $this->imgCount[$url] = $count;
                    
                    // echo url and img count
                    echo('url: '.$url.' count: '.$count."\n");

                    if(isset($urlsMatch[0]) && count($urlsMatch[0]) > 0){
                        foreach($urlsMatch[0] as $item){
                            // get "href"
                            preg_match('/href\s?=\s?(\"|\')(?P<url>(.+?))(\"|\')\s?/i', $item, $hrefMatch);
                            if(isset($hrefMatch['url'])){
                                $this->urlScan($hrefMatch['url']);
                            }
                        } 
                    }
                }
            }
        }
    }
    
    /**
     * Check and create url
     * @param string $url
     * @return bool|string
     */
    private function checkURL($url){
        $ret = null;
        
        if(!empty($url) && $url != '#'){        
            $url = $this->fixScheme($url);
            $parse = parse_url($url);
            
            if(isset($parse['path'])){
                $path = null;
                $hostMask = '/'.$this->urlParams['host'].'$/i';
                
                if(!isset($parse['host'])){
                    $path = $parse['path'];
                }elseif(preg_match ($hostMask, $parse['host']) == true){
                    $path = $parse['path'];
                }
                
                if($path !== null){
                    // check to first "/" in path
                    if(preg_match('/^\//', $path) == false){
                        $path = '/'.$path;
                    }
                    
                    // create url
                    $ret = (isset($parse['scheme']) ? $parse['scheme'] : $this->urlParams['scheme']).'://'.
                           (isset($parse['host']) ? $parse['host'] : $this->urlParams['host']).
                           $path;
                }
            }
        }

        return $ret;
    }

    /**
     * Generate first url and params
     * @param string $url
     * @return string
     */
    private function firstUrl($url){        
        $this->urlParams = parse_url($url);
        
        if(!isset($this->urlParams['scheme'])){
            $url = 'http://'.$url;
            $this->urlParams = parse_url($url);
        }
        if(!isset($this->urlParams['path'])){
            $url = $url.'/';
        }
        
        return $url;
    }
    
    /**
     * Check to first "//" in scheme and remove
     * @param string $url
     * @return string
     */
    private function fixScheme($url){
        return preg_replace('/^\/\//', '', $url);
    }
}