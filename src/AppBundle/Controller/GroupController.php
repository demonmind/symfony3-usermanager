<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends Controller
{
    /**
     * @Route("/groups", name="groupslist")
     */
    public function indexAction()
    {
        return $this->render('groups/index.html.twig');
    }
}
