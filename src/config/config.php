<?php

/**
 * @copyright (c) 2014, 3C Institute
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
use Cccisd\Autoroute\Constant as ARConst;

return array(
    ARConst::CONFIGKEY_DO_AUTOROUTE             => true,
    ARConst::CONFIGKEY_DISPATCHER_CLASS_DEFAULT => ARConst::DISPATCHER_CLASS_DEFAULT,
    ARConst::CONFIGKEY_DISPATCHER_CLASS_PACKAGE => ARConst::DISPATCHER_CLASS_PACKAGE,
    ARConst::CONFIGKEY_PACKAGE_PREFIX_MAP       => array(
        '/' => 'cccisd/autoroute::Void.blank', // load blank action in void controller.
    ),
);

