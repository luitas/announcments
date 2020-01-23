<?php

namespace App\Controller;

use App\Entity\Announcement as Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Announcement::class);
        $announcements = $repository->findAll();
        return $this->render('Web/announcement/index.html.twig', [
            'announcements' => $announcements,
            'controller_name' => 'AnnouncementController',
        ]);
    }

    /**
     * @Route("/announcement/{id}", name="announcement")
     * @param $id
     * @return Response
     */
    public function show($id){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Announcement::class);

        /**
         * @var $announcement Announcement
         */
        $announcement = $repository->find($id);
        $announcement->setShowCount($announcement->getShowCount()+1);
        $em->persist($announcement);
        $em->flush();

        return $this->render('Web/announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * @Route("/all_tops", name="all_tops")
     */
    public function allTops()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Announcement::class);
        $announcements = $repository->findBy(['']);

        return $this->render('Web/announcement/index.html.twig', [
            'announcements' => $announcements,
            'controller_name' => 'AnnouncementController',
        ]);
    }
}
