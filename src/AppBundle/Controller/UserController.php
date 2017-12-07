<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);


        return $this->render('users/edit.html.twig',array(
            'user' => $user
        ));
    }

    /**
     * @Route("/users/create", name="usercreate")
     */
    public function createAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('username', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Add User'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $plainPassword = 'test';

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);

            $user->setFirstName($form->get('first_name')->getData());
            $user->setLastName($form->get('last_name')->getData());
            $user->setUsername($form->get('username')->getData());
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('userlist');
        }
        return $this->render('users/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/users/delete/{id}", name="userdelete")
     */
    public function deleteAction($id)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('userlist');
    }

    /**
     * @Route("/users/logout", name="userlogout")
     */
    public function logoutAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/users/associate/{id}", name="associate_group")
     */
    public function associateAction($id, Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('group', EntityType::class, array(
                'class' => Group::class,
                'choice_label' => function($group) {
                    return $group->getName();
                }
            ))
            ->add('save', SubmitType::class, array('label' => "Associate"))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id);

            $group_id = $form->get('group')->getData()->getId();
            $group = $this->getDoctrine()
                ->getRepository('AppBundle:Group')
                ->find($group_id);

            $group->addUser($user);
            $user->addGroup($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('group_data', array(
                'id' => $group_id
            ));
        }

        return $this->render('users/associate.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/users/removefromgroup/{id}/{user_id}", name="remove_from_group")
     */
    public function removeFromGroupAction($id, $user_id,Request $request)
    {
        $group = $this->getDoctrine()
            ->getRepository('AppBundle:Group')
            ->find($id);

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($user_id);

        $user->removeGroup($group);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();


        return $this->redirectToRoute('group_data', array(
            'id' => $id
        ));
    }
}
