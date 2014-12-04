<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Cccisd\Autoroute\Dispatcher;

use Cccisd\Autoroute\Constant;

/**
 * Description of DispatcherAbstract
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
abstract class AbstractDispatcher extends \Cccisd\Patterns\AbstractBaseModel
{

    protected $packageName
            , $controllerOveride
            , $actionOverride
            , $homePackage
            , $homeController
            , $homeAction;

    /**
     * 
     * @param string $packageName
     */
    public function __construct($packageName)
    {
        $this->setPackageName($packageName);
    }

    /**
     * returns the uri to be used by the router.
     * Done this way so that built in url generation can be used.
     * @return string
     */
    abstract public function getRouteUri();

    /**
     * where conditions to match the route uri
     * @return string|array
     */
    abstract public function getRouteWhere();

    /**
     * function called to register the dispatcher's operations within the detected prefix
     * it should generally makes calls to \Route::[get|post|any|..] and register the necessary methods.
     */
    abstract public function register();

    /**
     * 
     * @param array $data
     * @return AbstractDispatcher
     */
    public function setData($data)
    {
        return $this->_setData($data);
    }

    /**
     * 
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * 
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * 
     * @param strng $override
     * @return string
     */
    public function getControllerOveride($override = NULL)
    {
        return empty($override) ? $this->controllerOveride : $override;
    }

    /**
     * 
     * @param string $override
     * @return string
     */
    public function getActionOverride($override = NULL)
    {
        return empty($override) ? $this->actionOverride : $override;
    }

    /**
     * 
     * @param string $controllerOveride
     * @return \Cccisd\Autoroute\Dispatcher\AbstractDispatcher
     */
    public function setControllerOveride($controllerOveride)
    {
        $this->controllerOveride = $controllerOveride;
        return $this;
    }

    /**
     * 
     * @param string $actionOverride
     * @return \Cccisd\Autoroute\Dispatcher\AbstractDispatcher
     */
    public function setActionOverride($actionOverride)
    {
        $this->actionOverride = $actionOverride;
        return $this;
    }

    /**
     * 
     * @param srting $packageName
     * @return \Cccisd\Autoroute\Dispatcher\AbstractDispatcher
     */
    protected function setPackageName($packageName)
    {
        $this->packageName = (string) $packageName;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getHomePackage()
    {
        return $this->homePackage;
    }

    /**
     * 
     * @return type
     */
    public function getHomeController()
    {
        return $this->homeController;
    }

    /**
     * 
     * @return type
     */
    public function getHomeAction()
    {
        return $this->homeAction;
    }

    /**
     * 
     * @param type $homePackage
     * @return \Cccisd\Autoroute\Dispatcher\AbstractDispatcher
     */
    public function setHomePackage($homePackage)
    {
        $this->homePackage = $homePackage;
        return $this;
    }

    /**
     * 
     * @param type $homeController
     * @return \Cccisd\Autoroute\Dispatcher\DispatcherAbstract.
     */
    public function setHomeController($homeController)
    {
        $this->homeController = $homeController;
        return $this;
    }

    /**
     * 
     * @param type $homeAction
     * @return \Cccisd\Autoroute\Dispatcher\AbstractDispatcher
     */
    public function setHomeAction($homeAction)
    {
        $this->homeAction = $homeAction;
        return $this;
    }

}
