<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Cccisd\Autoroute\Controllers;

use Cccisd\Framework\Controller\AbstractController;

/**
 *
 * @author "Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>"
 */
class HomeController extends AbstractController
{

    public function checkAuth()
    {
        
    }

    public function actionIndex()
    {
        return __METHOD__;
    }

}
