<?php
namespace Mozy\Core\AutoLoad;

class ClassLoader extends Loader {

    public function load($className) {
        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);

        foreach($this->extensions as $extension) {
            $fullFilePath = stream_resolve_include_path($filePath . $extension);

            if( $fullFilePath ) {
                include_once($fullFilePath);
                if( class_exists($className) && method_exists($className, 'bootstrap') )
                    $className::bootstrap();
                return;
            }
        }
    }
}
?>