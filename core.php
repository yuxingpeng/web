<?php

class _APP
{
    public static $dir = '';
    public static $uri = '';

    public static $request = '';
    public static $narg = 0;
    public static $args = array();

    public static $G = array();
  
    public static function Dump($txt)
    {
        echo $txt; exit();
    }
    
    public static function E($msg)
    {
        $html = '<html>' .
                '<head><title>ERROR DEBUGGING STACK BACKTRACE</title></head>' .
                '<body style="font-family:\'helvetica neue\', Helvetica, Arial, Verdana, sans-serif;">';
        
        $html.= '<div style="margin-bottom:10px;width:100%;padding:5px;border:solid 1px #F66;' .
                'font-size:16px;box-sizing:border-box;color:D00;background-color:#FEE">ERROR: ' . $msg . '</div>';
        
        $html.= '<div style="margin-bottom:10px;width:100%;padding:5px;border:solid 1px #FA3;' .
                'font-size:14px;box-sizing:border-box;background-color:#FFE;line-height:22px;">';
        
        $tr = debug_backtrace();
        
        for($i=0; $i<count($tr); $i++)
        {
            $html .= '<span style="display:inline-block;width:30px;color:D60">'.$i.'</span> ';
            $html .= '<span style="display:inline-block;padding-right:20px;color:#090">at ' .
                     str_replace(self::$dir, '',$tr[$i]['file']) . ':'. $tr[$i]['line'] .
                     '</span>';
            
            $html .= $tr[$i]['class'] . '::'. $tr[$i]['function'];
            if($i==0) { $html .= '<br>'; continue; }

            if(count($tr[$i]['args'])>0)
            {
                $html .= ' <span style="color:#33C">(';
                
                foreach($tr[$i]['args'] as $k => $v)
                {
                    if($k>0) $html .= ', ';
                    if(is_string($v)) $html .= '\''.$v.'\''; else $html .= $v;
                }
                
                $html .= ')</span>';
            }
            
            $html .= '<br>';
        }
        
        self::Dump($html . '</div></body></html>');
    }
    
    public static function C($name)
    {
        if(!class_exists($name))
        {
            $file = self::$dir.'/class/'.$name.'.php';
            if(!file_exists($file)) self::E('Cannot find the source file for a class.');
            require($file);
        }
    }
    
    function redirect($url)
    {
        header('Location: '.self::$uri.'/'.$url);
        exit();
    }
    
    function init()
    {
        self::$request = trim(str_replace(self::$uri, '', $_SERVER['REQUEST_URI']), '/');
        self::$args = explode('/', self::$request);
        self::$narg = count(self::$args); 
    }
}

?>
