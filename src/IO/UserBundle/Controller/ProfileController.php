<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IO\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller managing the user profile
 */
class ProfileController extends BaseController
{
    /**
     * Show the user
     */
    public function showAction()
    {
        $url = $this->container->get('router')->generate('fos_user_profile_edit');
        return new RedirectResponse($url);
    }
}
