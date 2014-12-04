<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Cccisd\Autoroute\Dispatcher;

use Cccisd\Autoroute\Constant;
use Cccisd\Util\Package as PackageUtil;
use Cccisd\Framework\Controller\AbstractController as CccController;
use Illuminate\Routing\Controller as LaravelController;

/**
 * Description of Dispatcher
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Dispatcher extends AbstractDispatcher
{

//    /**
//     * controller class 
//     */
//    const CONTROLLER_CCC = '\Cccisd\Framework\Controller\AbstractController';
//    /**
//     * 
//     */
//    const CONTROLLER_LARAVEL = '\Illuminate\Routing\Controller';

    protected $packageNamespace
            , $currentControllerClass
            , $currentActionMethod
            , $currentController
            , $currentAction;

    public function getRouteUri()
    {
        return '{controller?}/{action?}/{extra?}';
    }

    public function getRouteWhere()
    {
        return array(
            'extra' => '.*',
        );
    }

    /**
     * detected packagae namespace from package name.
     * @return string
     */
    public function getPackageNamespaceName()
    {
        if(!isset($this->packageNamespace))
        {
            $this->packageNamespace = PackageUtil::getPackageNamespace($this->getPackageName());
        }
        return $this->packageNamespace;
    }

    /**
     * 
     * @param type $useHome
     * @return type
     */
    public function getControllerClassPrefix($useHome = FALSE)
    {
        if($useHome && ($homePackage = $this->getHomePackage()))
        {
            return PackageUtil::getPackageNamespace($homePackage);
        }

        return $this->getPackageNamespaceName();
    }

    /**
     * attempts to detect correct controller class and action
     * will fall back to home controller if controller class for the package is not found.
     * 
     * @return array [$controllerClass, $actionMethod, $controllerPart, $actionpart];
     */
    public function detectControllerAction()
    {
        $controllerPart  = $this->getControllerOveride();
        $actionPart      = $this->getActionOverride();
        $controllerClass = $this->getControllerClass($controllerPart);

        if(!class_exists($controllerClass))
        {
            $controllerClass = $this->getControllerClass(NULL); // should get home controller.
            $actionPart      = $controllerPart;
            $controllerPart  = NULL;
        }

        $actionMethod = $this->getActionMethod($actionPart);

        return [$controllerClass, $actionMethod, $controllerPart, $actionPart];
    }

    public function getActionMethod($actionPart)
    {
        return camel_case('action-' . ($actionPart ? $actionPart : 'Index'));
    }

    public function getControllerClass($controllerPart)
    {

        return $this->getControllerClassPrefix() . '\\Controllers\\' . studly_case(($controllerPart ? $controllerPart : 'Home') . '-Controller');
    }

    public function getDefaultAction()
    {
        return 'index';
    }

    public function getDefaultController()
    {
        return 'home';
    }

    public function register()
    {
        // not quite as JIT as I'd like..
        // @todo possibly rewrite this whole package to use only \Route::controller and overrides to package specific.
        list($controllerClass,, $controller, $action) = $this->detectControllerAction();
        //list($controllerClass) = $this->detectControllerAction();

        $test = \App::make($controllerClass); // either the whole autoroute pa

        if($test instanceof CccController)
        {
            \Route::any($this->getRouteUri(), array(
                        'as' => Constant::ROUTE_NAME_CURRENT,
                        array($this, 'run'),
                    ))
                    ->where($this->getRouteWhere());
        }
        elseif($test instanceof LaravelController)
        {
//            if(!empty($action))
//            {
//                // so that a forced action would go the right thing.
//                $method = strtolower(\Request::getMethod());
//                $c      = $this->getControllerOveride();
//                $x      = "{$controllerClass}@{$method}" . studly_case($action);
//                switch($method) // hmm
//                {
//                    case 'get':
//                        \Route::get($c, $x);
//                        break;
//                    case 'post':
//                        \Route::post($c, $x);
//                        break;
//                    case 'put':
//                        \Route::put($c, $x);
//                        break;
//                    case 'del':
//                        \Route::del($c, $x);
//                        break;
//                    default:
//                        \Route::any($c, $x);
//                }
//            }
            // this sort of make sense..
            $c = ($this->getPrefix() === '/') ? $this->getControllerOveride() : '/';
            // mah. forced routes don't work so well with implicit controllers
            \Route::controller($c, $controllerClass);
        }
    }

    /**
     * all params must be optional
     * should abort on error
     * @return mixed something that can be rendered as a string
     * @throws \Exception
     */
    public function run()
    {
        list($controllerClass, $actionMethod, $controller, $action) = $this->detectControllerAction();

        $this->setCurrentAction($action)
                ->setCurrentController($controller)
                ->setCurrentControllerClass($controllerClass)
                ->setCurrentActionMethod($actionMethod);

        /* @var $controllerObj \Cccisd\Framework\Controller\AbstractController */
        $controllerObj = \App::make($controllerClass);

        $controllerObj->setCurrentControllerUri($controller); // this much is certain.
        // register a request segment filter.
        // @todo should put this somewhere else.
        $prefix = $this->getPrefix();
        \Cccisd\Framework\Filter\Filter::instance()->enqueue(
                Constant::FILTERCONTENT_CONTROLLER_REQUESTSEGMENT
                , function(\Cccisd\Framework\Filter\FilterPayload $payload, $controller = NULL, $action = NULL) use ($prefix)
        {
            // use passed params instead of currently detected as controller can do additional routing.
            $payload->subject = array_values(array_diff($payload->subject, [
                $controller,
                $action,
                $prefix, // also remove the first prefix segment.
            ]));

            array_walk($payload->subject, function(&$v)
            {
                $v = urldecode($v);
            });

            return $payload;
        }
                , 100); // high priority


        return $controllerObj->callAction($actionMethod, []);
    }

    /**
     * 
     * @return string
     */
    public function getCurrentControllerClass()
    {
        return $this->currentControllerClass;
    }

    /**
     * 
     * @return string
     */
    public function getCurrentActionMethod()
    {
        return $this->currentActionMethod;
    }

    /**
     * 
     * @return string
     */
    public function getCurrentController()
    {
        return $this->currentController;
    }

    /**
     * 
     * @return string
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * 
     * @param string $currentControllerClass
     * @return \Cccisd\Autoroute\Dispatcher\Dispatcher
     */
    public function setCurrentControllerClass($currentControllerClass)
    {
        $this->currentControllerClass = $currentControllerClass;
        return $this;
    }

    /**
     * 
     * @param string $currentActionMethod
     * @return \Cccisd\Autoroute\Dispatcher\Dispatcher
     */
    public function setCurrentActionMethod($currentActionMethod)
    {
        $this->currentActionMethod = $currentActionMethod;
        return $this;
    }

    /**
     * 
     * @param string $currentController
     * @return \Cccisd\Autoroute\Dispatcher\Dispatcher
     */
    public function setCurrentController($currentController)
    {
        $this->currentController = $currentController;
        return $this;
    }

    /**
     * 
     * @param string $currentAction
     * @return \Cccisd\Autoroute\Dispatcher\Dispatcher
     */
    public function setCurrentAction($currentAction)
    {

        $this->currentAction = $currentAction;
        return $this;
    }

}
