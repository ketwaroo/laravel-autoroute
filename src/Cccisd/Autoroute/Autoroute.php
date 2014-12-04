<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Cccisd\Autoroute;

use Cccisd\Autoroute\Constant;

/**
 * Description of Autoroute
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Autoroute
{

    use \Cccisd\Patterns\TraitSingleton,
        \Cccisd\Patterns\TraitIsInPackage;

    /**
     *
     * @var type 
     */
    protected $dispatcherInstance = null;

    /**
     * registers the auto router as last route before app starts.
     * @todo see if we can use \Route::setControllerDispatcher
     * @param type $routeMap
     */
    public function registerDispatch($routeMap = array())
    {
        if(empty($routeMap))
        {
            return;
        }

        // default handling for everything else. keep this as last route.  
        $path = \Request::path();

        list($prefix, $subPath) = \array_pad(\explode('/', $path, 2), 2, ''); // 2 parts. prefix, $subpath (controller, action, other stuff)

        if(empty($prefix))
        {
            $prefix = '/';
        }

        $controllerOverride = NULL;
        $actionOverride     = NULL;

        list($homePackage, $homeController, $homeAction) = $this->sanitiseSubPathDefinition($routeMap['/']);

        if(empty($routeMap[$prefix])) // if route not handled and we're come this far.
        {
            //use default fallback; home controller.
            $subPathDef         = $routeMap['/'];
            $controllerOverride = $prefix;
            $actionOverride     = $subPath;
            $subPath            = $prefix . '/' . $subPath;
            $prefix             = '/';
        }
        else
        {
            $subPathDef = $routeMap[$prefix];
        }

        list($packageName, $controller, $action) = $this->sanitiseSubPathDefinition($subPathDef, $controllerOverride, $actionOverride);

        $dispatcher = $this->getDispatcher($packageName);

        $dispatcher->setActionOverride($action)
                ->setControllerOveride($controller);

        // set some defaults.
        $dispatcher->setHomePackage($homePackage)
                ->setHomeController($homeController)
                ->setHomeAction($homeAction);


        $attributes = array(
            'prefix'       => $prefix,
            'isHome'       => ($prefix === '/'),
            'subPath'      => $subPath,
            'subPathDef'   => $subPathDef,
            'isAutorouted' => true,
        );

        $dispatcher->setData($attributes);

        \App::before(function() use ($attributes, $dispatcher)
        {
            $attributes['dispatcher'] = $dispatcher;

            \Route::group($attributes, function() use ($dispatcher)
            {
                $dispatcher->register();
            });
        });
    }

    /**
     * Appempts to get a custom autoroute dispatcher if the target package provides one.
     * Otherwise uses the default one.
     * @param string $packageName
     * @return Dispatcher\AbstractDispatcher
     */
    public function getDispatcher($packageName)
    {
        // try to see if Dispatcher exists at package level first.
        $packageNamespaceName = \Cccisd\Util\Package::getPackageNamespace($packageName);

        $dispatcherClass = $packageNamespaceName . $this->readPackageInstanceConfig(Constant::CONFIGKEY_DISPATCHER_CLASS_PACKAGE);

        if(!class_exists($dispatcherClass))
        {
            $dispatcherClass = __NAMESPACE__ . $this->readPackageInstanceConfig(Constant::CONFIGKEY_DISPATCHER_CLASS_DEFAULT);
        }

        $dispatcher = \app()->make($dispatcherClass, array($packageName));

        $this->currentDispatcher = $dispatcher;

        return $dispatcher;
    }

    /**
     * 
     * @return Dispatcher\AbstractDispatcher|null
     */
    public function getCurrentDispatcher()
    {

        return $this->currentcurrentDispatcher;
    }

    /**
     * 
     * @param string $def
     * @param string|null $controllerOverride
     * @param string|Null $actionOverride
     * @return array in 3 parts. use with list()
     */
    protected function sanitiseSubPathDefinition($def, $controllerOverride = NULL, $actionOverride = NULL)
    {
        list($packageName, $controller, $action) = ccc_resolve_namespaced($def);

        // fix the namespaced thing whendealing with only package name.
        // otherwise seems to behave fine.
        if(!isset($packageName) && !isset($action) && isset($controller))
        {
            $packageName = $controller;
            $controller  = NULL;
        }

        if($controllerOverride)
        {
            $controller = $controllerOverride;
        }

        if($actionOverride)
        {
            list($action) = explode('/', $actionOverride, 2); // limit to first segment of action override
        }

        return array($packageName, $controller, $action);
    }

    /**
     * 
     * @return Autoroute
     */
    public static function instance()
    {
        return static::getInstance();
    }

}
