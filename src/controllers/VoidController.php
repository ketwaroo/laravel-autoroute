<?php

/**
 * 
 */

namespace Cccisd\Autoroute\Controllers;

//use Cccisd\Framework\Controller\AbstractController;

/**
 * Description of VoidController is Void Controller
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class VoidController extends \Controller
{

    public function getBlank()
    {
        return 'This is ' . __METHOD__ . '. A test, if you will.';
    }

    public function getIndex()
    {
        return __METHOD__;
    }

}
