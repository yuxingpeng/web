<?php

if(!class_exists('APP')) { echo "ERROR: No APP is defined!"; exit(); }

class XMLNode
{
	public $tag = "";
	public $close = false;
	public $attri = array();
	public $child = array();
	
	function __construct($tag, $close = false, $attri = array())
	{ 
		$this->tag = $tag;
		$this->close = $close;
		$this->attri = $attri; 
	}
	
	function text()
	{
		$text  = '<' . $this->tag;
		foreach($this->attri as $k => $v) $text .= " ${k}=\"${v}\"";
		
		if($this->close) $text .= ' />';
		else
		{
			$text .= '>';
			
			foreach($this->child as $v) 
				if(is_object($v)) $text .= $v->text();
				else $text .= $v;
			
			$text .= '</' . $this->tag . '>';
		}
		
		return $text;
	}
	
	function add_child($node)
	{
		array_push($this->child, $node);
	}
	
    function add_attri($key, $val)
    {
        $this->attri[$key] = $val;
    }
    
	function add_image($src)
	{
		$this->add_child(new XMLNode("img", true, array("src" =>  APP::$uri.'/'.$src)) );
	}
	
	function add_div($attri = array())
	{
        if(is_string($attri))
        {
            if(substr($attri,0,1)==".") $node = new XMLNode("div", false, array("class" => str_replace(".","", $attri)));
            else if(substr($attri,0,1)=="#") $node = new XMLNode("div", false, array("id" => str_replace("#","", $attri)));
        }
		else $node = new XMLNode("div", false, $attri);
        
		$this->add_child($node);
		return $node;
	}
	
	function add_link($src, $class="")
	{
        if(strstr($src,":")) $href=$src; else $href=APP::$uri.'/'.$src;
		$node = new XMLNode("a", false, array("href" => $href));
		if($class!="") $node->attri['class'] = $class;
		$this->add_child($node);
		return $node;
	}
	
	function clear() { $this->add_child('<div style="clear: both"></div>'); }
	
	function add_form($v)
	{
		$form = new XMLNode("form", false, array(
					"action" => $v["action"],
					"method" => "post"));
		
		foreach($v["items"] as $one)
		{
			if($one["type"]=="text")
			{
				$form->add_div(array("class" => $v["class_prefix"]."_label"))->add_child($one["label"]);
				
				$form->add_child(new XMLNode("input", true, array(
					"type" => "text",
					"name" => $one["name"],
					"id"   => $one["name"],
					"class" => $v["class_prefix"]."_text"
				)));
			}
			
			else if($one["type"]=="password")
			{
				$form->add_div(array("class" => $v["class_prefix"]."_label"))->add_child($one["label"]);
				
				$form->add_child(new XMLNode("input", true, array(
					"type" => "password",
					"name" => $one["name"],
					"id"   => $one["name"],
					"class" => $v["class_prefix"]."_text"
				)));
			}
			
			else if($one["type"]=="submit")
			{				
				$form->add_child(new XMLNode("input", true, array(
					"type" => "submit",
					"value" => $one["name"],
					"class" => $v["class_prefix"]."_submit"
				)));
			}
			
			else if($one["type"]=="margin")
			{				
				$form->add_child(new XMLNode("div", false, array(
					"style" => "width:100%;clear:both;height:".$one["h"])));
			}
		}
												
		$this->add_child($form);
	}
}

class HTML5
{
	public $charset = "utf-8";
	public $title = "";
	
	public $css = array();
	public $js = array();
	public $meta = array();

	public $body;
	
	function __construct()
	{
		$this->body = new XMLNode("body");
	}
	
    function complete_link($url)
    {
        if(substr($url,0,7)=="http://" || substr($url,0,8)=="https://") return $url;
        else return APP::$uri."/".$url;
    }
    
	function link_css($css) { array_push($this->css, $this->complete_link($css."?".time())); }
	function link_js($js) { array_push($this->js, $this->complete_link($js."?".time())); }
	
	function text()
	{
		$text  = '<!doctype html>';
		$text .= '<html>';

		$text .= '<head>';
		$text .= '<meta charset="' . $this->charset . '">';
		$text .= '<title>' . $this->title . '</title>';

		for($i=0; $i<count($this->meta); $i++)
                        $text .= '<meta "' . $this->meta[$i] . ' >';
		
		for($i=0; $i<count($this->css); $i++)
			$text .= '<link href="' . $this->css[$i] . '" type="text/css" rel="stylesheet">';
		
		for($i=0; $i<count($this->js); $i++)
			$text .= '<script src="' . $this->js[$i] . '" type="text/javascript"></script>';
		
		$text .= '</head>';

		$text .= $this->body->text();
		
		$text .= '</html>';	
		
		return $text;
	}
    
    static function vline($color) { return '<span style="margin:0px 10px;border-right:solid 1px '.$color.'"></span>'; }
}

?>
