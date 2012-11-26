<?php 
namespace Mozy\Core;

class TestLoader extends Loader {

    public function load($className) {            
        $filePath = str_replace('\\', '/', $className);
        $endOfNamespace = strrpos($filePath, '/') ? strrpos($filePath, '/') + 1 : 0;
        $filePath = substr_replace($filePath, '_Tests/', $endOfNamespace, 0);
        
        foreach($this->extensions as $extension) {
            $fullFilePath = stream_resolve_include_path($filePath . $extension);
           
            if( $fullFilePath ) {
                include_once($fullFilePath);
                return;
            }
        }
    }
}
?>