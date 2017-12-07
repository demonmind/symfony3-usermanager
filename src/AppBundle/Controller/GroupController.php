<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Group;

class GroupController extends Controller
{
    /**
     * @Route("/groups", name="groupslist")
     */
    public function indexAction()
    {
        $groups = $this->getDoctrine()
            ->getRepository('AppBundle:Group')
            ->findAll();

        return $this->render('groups/index.html.twig', array(
            'groups' => $groups
        ));
    }

    /**
     * @Route("/groups/{id}", name="group_data")
     */
    public function showAction($id)
    {

        $group = $this->getDoctrine()
            ->getRepository('AppBundle:Group')
            ->find($id);

        if (empty($group)) {
            die('No Group Found');
        }

        $group_users = $group->getUsers();

        return $this->render('groups/show.html.twig', array(
            'group' => $group,
            'group_users' => $group_users
        ));
    }

    /**
     * @Route("/create/groups", name="groupcreate")
     */
    public function createAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Group'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $group = new Group();

            $group->setName($form->get('name')->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            return $this->redirectToRoute('groupslist');
        }
        return $this->render('groups/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/groups/delete/{id}", name="groupdelete")
     */
    public function deleteAction($id)
    {
        $group = $this->getDoctrine()
            ->getRepository('AppBundle:Group')
            ->find($id);

        $group_users = $group->getUsers();

        if ($group_users->count()){
            die('You can not delete this group!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();

        return $this->redirectToRoute('userlist');
    }
}
