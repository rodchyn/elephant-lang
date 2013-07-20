<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 7/15/13
 * Time: 8:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Rodchyn\ElephantLang;


class Init {
    public static function init()
    {

    }
}

namespace Rodchyn\ElephantLang\Parser;

function yy($type) {
    $args = func_get_args();
    array_shift($args);

    $type = __NAMESPACE__.'\yy\\'.$type;

    $inst = new $type;
    $inst = call_user_func_array(array($inst, 'constructor'), $args);

    return $inst;
}
