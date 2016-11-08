<?php

namespace Mentatik\UserBundle\Controller;


use Mentatik\UserBundle\Form\UserType;
use Mentatik\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    private $em;
    private $userRepository;

    public function getUserRepository()
    {
        $manager = $this->container->get('graph_entitymanager');
        $this->em = $manager->getEm();
        $this->userRepository = $this->em->getRepository(User::class);
    }

    public function indexAction()
    {
        $this->getUserRepository();
        $user_list = $this->userRepository->findAll();
        return $this->render('MentatikUserBundle:Admin:index.html.twig',
            array('user_list'=> $user_list));
    }

    public function insertAction(Request $request)
    {
        $this->getUserRepository();
        $user = new User();
        $form = $this->createUserForm($user, 'Create');

        if ($request->getMethod() == 'POST') {
            return $this->save_user_from_form($request, $form);
        }

        return $this->render('MentatikUserBundle:Admin:form.html.twig',
            array('form'=> $form->createView()));
    }

    public function updateAction(Request $request, $id)
    {
        $this->getUserRepository();
        $user = $this->userRepository->findOneById((int)$id);
        $form = $this->createUserForm($user, 'Update');

        if ($request->getMethod() == 'POST') {
            return $this->save_user_from_form($request, $form);
        }

        return $this->render('MentatikUserBundle:Admin:form.html.twig',
            array('form'=> $form->createView()));
    }

    public function deleteAction($id)
    {
        $this->getUserRepository();
        $user = $this->userRepository->findOneById((int)$id);
        $this->em->remove($user);
        $this->em->flush();
        return $this->redirectToRoute('Mentatik_user_admin_list');
    }

    private function createUserForm(User $user, $buttonAction)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->add('submit', SubmitType::class, array(
            'label' => $buttonAction
        ));
        return $form;
    }

    private function save_user_from_form(Request $request,Form $form)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->em->persist($user);
            $this->em->flush();
        }
        return $this->redirectToRoute('Mentatik_user_admin_list');
    }
}
