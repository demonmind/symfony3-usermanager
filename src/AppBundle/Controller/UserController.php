<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/", name="userlist")
     */
    public function listAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        return $this->render('users/index.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/users/edit/{id}", name="edituser")
     */
    public function editAction($id, Request $request)
    {
        return $this->render('users/edit.html.twig');
    }

    /**
     * @Route("/users/create", name="usercreate")
     */
    public function createAction(Request $request)
    {
        return $this->render('users/create.html.twig');
    }

    /**
     * @Route("/users/delete/{id}", name="userdelete")
     */
    public function deleteAction($id, Request $request)
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/logout", name="userlogout")
     */
    public function logoutAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }
}
