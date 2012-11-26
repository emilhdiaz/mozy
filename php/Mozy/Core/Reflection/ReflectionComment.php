<?php
namespace Mozy\Core\Reflection;

class ReflectionComment implements \Reflector {
    
    protected $comment;
    protected $description;
    protected $annotations;
    
    public function __construct(Documented $reflector) {
        $this->comment = $reflector->getDocComment();
        $this->annotations = [];
            
        // parse description
        preg_match_all('/\* +([^@\r\n]+)[\r\n]/', $this->comment, $matches);
        foreach($matches[1] as $line) {
            $this->description .= trim($line)." ";
        }
        
        // parse annotations
        preg_match_all('/@(\S+)\s*(.*)[\r\n]/', $this->comment, $matches);
        foreach($matches[1] as $key=>$name) {
            // check for parameter @var definitions
            if( $name == 'var' )
                continue;
            
            $value = [];
            foreach(explode(',', trim($matches[2][$key])) as $pair) {                
                // $pair expected to be 'Text' or  'Text Text'
                $pair = explode(' ', trim($pair));
                $n = $pair[0];
                $v = isset($pair[1]) ? $pair[1] : $n;
                $value[$n] = $v;
            }
                
            // correct for single qualifier
            if( (count($value) == 1) && (reset($value) == key($value)) )
                $value = current($value);
            
            // correct for no qualifier
            if( count($value) == 0 ) 
                $value = true;
             
            $this->annotations[$name] = $value;
        }
    }    
    
    public function getDescription() {
        return $this->description;    
    }
    
    public function getAnnotation($annotation) {
        if( !$this->hasAnnotation($annotation) )
            return null;
            
        return $this->annotations[$annotation];
    }
    
    public function getAnnotations() {
        return $this->annotations;
    }
    
    public function hasAnnotation($annotation) {
        return array_key_exists($annotation, $this->annotations);
    }
        
    public static function export() {
        
    }
    
    public function __toString() {
        return $this->comment;
    }
    
}
?>