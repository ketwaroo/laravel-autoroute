<?php

namespace Cccisd\Autoroute;

use \Cccisd\Framework\ServiceProvider\ServiceProviderAbstract;

class AutorouteServiceProvider extends ServiceProviderAbstract
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * @todo see if we can use \Route::setControllerDispatcher
     */
    public function bootstrapEvents()
    {

        if($this->readPackageInstanceConfig(Constant::CONFIGKEY_DO_AUTOROUTE))
        {
            Autoroute::instance()->registerDispatch($this->readPackageInstanceConfig(Constant::CONFIGKEY_PACKAGE_PREFIX_MAP, array()));
        }
    }

}
