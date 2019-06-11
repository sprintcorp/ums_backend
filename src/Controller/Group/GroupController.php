<?php

namespace App\Controller\Group;

use App\Controller\AppController;

use App\Entity\Permission;
use App\Entity\UserGroup;
use App\Form\GroupFormType;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class GroupController extends AppController
{
    private $mailer;

    public function __construct(RequestStack $request, ObjectManager $manager, \Swift_Mailer $mailer)
    {
        parent::__construct($request, $manager);
        $this->mailer = $mailer;
    }


    /**
     * @Route("/groups", methods={"Get"})
     */
    public function getGroup()
    {

        $groups = $this->getAuthenticatedApplication()->getGroups();

        $adjustedGroups = [];

        foreach ($groups as $group) {
            array_push($adjustedGroups, [
                'name' => $group->getName(),
                'type' => $group->getType(),
                'roles' => $group->getRoles(),
                'status' => $group->getStatus(),
                'created_at' => $group->getCreatedAt(),
                'updated_at' => $group->getUpdatedAt() == null ? "" : $group->getUpdatedAt(),
                'permission_id' => $group->getPermission()->getId()
            ]);
        }


        return $this->json([
            'groups' => $adjustedGroups
        ]);

        // query for a single Product by its primary key (usually "id")

    }

    /**
     * @Route("/groups", methods={"Post"})
     */
    public function createGroup()
    {
        $group = new UserGroup();
        $form = $this->createForm(GroupFormType::class, $group);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $this->request->request->get("name");
            $roles = $this->request->request->get("roles");
            $permission = $this->request->request->get("permission");

            if (!$permission || empty($permission)) $permission = 'view';

            $now = new \DateTime();

            $perm = new Permission();
            $perm->setCreatedAt($now)
                ->setMask($permission)
                ->setType("");

            $this->objectManager->persist($perm);
            $this->objectManager->flush();

            $group->setName($name)
                ->setRoles($roles)
                ->setPermission($perm)
                ->setStatus(1)
                ->setCreatedAt($now)
                ->setApplication($this->getAuthenticatedApplication())
                ->setType("");


            $this->objectManager->persist($group);
            $this->objectManager->flush();
        }
        return $this->json([
            'success' => true,
            'data' => [
                'name' => $group->getName(),
                'roles' => $group->getRoles(),
                'permission' => $group->getPermission()
            ]
        ]);

    }

    /**
     * @Route("/group/{id}", methods={"PUT"})
     */
    public function updateAction($id)
    {
        $now = new \DateTime();
        $name = $this->request->request->get('name');
        $roles = $this->request->request->get('roles');

        $group = $this->objectManager->getRepository(UserGroup::class)->findOneBy(['id' => $id]);

        $app = $this->getAuthenticatedApplication();

        if ($group) {
            if ($group->getApplication() == $app) {



                if (!empty($name)) $group->setName($name);
                if (!empty($roles)) $group->setRoles($roles);

                $group->setUpdatedAt($now);
                $this->objectManager->flush();

                return $this->json([
                    'success' => true,
                    'data'=>[
                        'name'=> $group->getName(),
                        'roles'=> $group->getRoles(),
                    ],
                    'updated_at' => $now
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'This group does not belong to this application'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json([
                'success' => false,
                'error' => 'group not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }


    /**
     * @Route("/group/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        $group = $this->objectManager->getRepository(UserGroup::class)->findOneBy(['id' => $id]);

        $app = $this->getAuthenticatedApplication();

        if ($group) {
            if ($group->getApplication() == $app) {
                $now = new DateTime();
                /*$now = new DateTime();
                $user->setStatus(2);
                $user->setDeletedAt($now);*/
                $group->trash();
                $this->objectManager->flush();


                return $this->json([
                    'success' => true,
                    'account_delete_time' => $now
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'Group not found on this application.'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        }


    }
}
