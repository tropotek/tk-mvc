<?php
namespace Tk\Controller;


use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @notes Adapted from Symfony
 */
class PageResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{

    /**
     * @param Request $request
     * @return callable|false|object|Iface
     */
    public function getController(Request $request)
    {
        $controller = parent::getController($request);

        /** @var Iface $controller */
        if (is_array($controller) && $controller[0] instanceof Iface) {
            $cObj = $controller[0];
            //$page = $cObj->getPage();                 // * Removed: This causes issues when loading controllers dynamically
            if (!$cObj->getPageTitle()) {     // Set a default page Title for the crumbs
                $cObj->setPageTitle($cObj->getDefaultTitle());
            }
            //$cObj->setPage($page);                    // * Removed: This causes issues when loading controllers dynamically
            $request->attributes->set('controller.object', $cObj);
        }

        return $controller;
    }

}