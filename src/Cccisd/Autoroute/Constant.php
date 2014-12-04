<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Cccisd\Autoroute;

use Cccisd\Framework\Constant as FConstant;

/**
 * Constants for the autorouter.
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Constant extends FConstant
{

    const CONFIGKEY_DO_AUTOROUTE             = 'boolEnableCccisdAutoRouter';
    const CONFIGKEY_PACKAGE_PREFIX_MAP       = 'mapCccisdAutoRouter';
    const CONFIGKEY_DISPATCHER_CLASS_PACKAGE = 'stringAutrouteDispatcherClassPackage';
    const CONFIGKEY_DISPATCHER_CLASS_DEFAULT = 'stringAutrouteDispatcherClassDefault';
    const ROUTE_NAME_CURRENT                 = 'autorouteCurrentRoute';
    const ROUTE_PARAM_ISAUTOROUTED           = 'isAutorouted'; // so we know it's an autorouted route.
    const ROUTEMAP_OVERRIDE_ACTION           = 'actionOverride';
    const ROUTEMAP_OVERRIDE_CONTROLLER       = 'controllerOverride';

    /**
     * relative class path to the custom dispatcher located within the routed package namespace.
     */
    const DISPATCHER_CLASS_PACKAGE = '\\Autoroute\\Dispatcher';

    /**
     * relative class path to default dispatcher located within the autoroute package namespace.
     */
    const DISPATCHER_CLASS_DEFAULT  = '\\Dispatcher\\Dispatcher';
    //
    const REGKEY_CURRENT_CONTROLLER = 'autoroute.current-controller';
    const REGKEY_CURRENT_ACTION     = 'autoroute.current-action';

}
