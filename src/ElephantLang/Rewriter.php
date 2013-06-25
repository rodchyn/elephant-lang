<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 6/19/13
 * Time: 6:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace ElephantLang;


class Rewriter {

    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function save()
    {
        file_put_contents($this->filePath, '<?php
        namespace Elephant;

        class ClassName {
        }
        ');
    }
}
