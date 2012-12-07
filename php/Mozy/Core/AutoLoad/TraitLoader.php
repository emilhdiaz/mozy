<?php
namespace Mozy\Core\AutoLoad;

class TraitLoader extends Loader {

    public function load($className) {

        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $endOfNamespace = strrpos($filePath, DIRECTORY_SEPARATOR) ? strrpos($filePath, DIRECTORY_SEPARATOR) + 1 : 0;
        $filePath = substr_replace($filePath, '_Traits/', $endOfNamespace, 0);

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