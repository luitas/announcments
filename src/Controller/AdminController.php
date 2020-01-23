<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/user", name="admin_user")
     * @IsGranted("ROLE_USER")
     */
    public function userAdmin()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminUserController',
        ]);
    }

}

