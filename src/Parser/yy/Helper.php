<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 7/16/13
 * Time: 8:49 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Rodchyn\ElephantLang\Parser\yy;


class Helper {

    private $string = '';

    public function constructor($identifier, $helperFunction)
    {
        if($helperFunction == 'length') {
            $this->string = 'mb_strlen(' . $identifier . ')';
        }

        if($helperFunction == 'size') {
            $this->string = 'count(' . $identifier . ')';
        }

        return $this;
    }

    public function __toString()
    {
        return $this->string;
    }
}
