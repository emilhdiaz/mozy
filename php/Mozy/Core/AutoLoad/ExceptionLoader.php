<?php
namespace Mozy\Core\AutoLoad;

class ExceptionLoader extends Loader {

    public function load($className) {
        if( strpos($className, 'Exception') === false )
            return;

        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $endOfNamespace = strrpos($filePath, DIRECTORY_SEPARATOR) ? strrpos($filePath, DIRECTORY_SEPARATOR) + 1 : 0;
        $filePath = substr_replace($filePath, '_Exceptions/', $endOfNamespace, 0);

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