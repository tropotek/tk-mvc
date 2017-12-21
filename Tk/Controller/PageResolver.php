<?php
namespace Tk\Controller;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class PageResolver extends Resolver
{

    /**
     * @param \Tk\Request $request
     * @return callable|false|object|Iface
     */
    public function getController(\Tk\Request $request)
    {
        $controller = parent::getController($request);

        /** @var Iface $controller */
        if ($controller[0] instanceof Iface) {
            $cObj = $controller[0];
            $page = $cObj->getPage();
            if (!$cObj->getPageTitle()) {     // Set a default page Title for the crumbs
                $cObj->setPageTitle($cObj->getDefaultTitle());
            }
            $cObj->setPage($page);
            $request->setAttribute('controller.object', $cObj);
        }

        return $controller;
    }

}