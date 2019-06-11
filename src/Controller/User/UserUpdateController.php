<?php

namespace App\Controller\User;

use App\Controller\AppController;
use App\Entity\ApplicationUser;
use App\Entity\UserGroup;
use App\Form\UpdateAccountFormType;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserUpdateController extends AppController
{
    private $mailer;

    public function __construct(RequestStack $request, ObjectManager $manager, \Swift_Mailer $mailer)
    {
        parent::__construct($request, $manager);
        $this->mailer = $mailer;
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     */
    public function updateAction($id)
    {
        $now = new \DateTime();
        $email = $this->request->request->get('email');
        $username = $this->request->request->get('username');
        $firstname = $this->request->request->get('firstname');
        $lastname = $this->request->request->get('lastname');
        $othernames = $this->request->request->get('othernames');


        if (strlen($username) < 2 || strlen($username) > 25) {
            return $this->json([
                'success' => false,
                'error' => 'Username should be between 2 and 25 characters long.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }


        $user = $this->objectManager->getRepository(User::class)->findOneBy(['id' => $id]);

        $app = $this->getAuthenticatedApplication();

        if ($user) {
            if ($user->hasApplication($app)) {

                if (!empty($email)) {
                    $emailTestUser = $this->objectManager->getRepository(User::class)
                        ->findBy(['email' => $email]);

                    foreach ($emailTestUser as $user) {
                        if ($user->hasApplication($app)) {
                            return $this->json([
                                'error' => 'Email taken.',
                                'error_code' => 10
                            ], JsonResponse::HTTP_BAD_REQUEST);
                        }
                    }
                }

                if (!empty($username)) {
                    $usernameTestUser = $this->objectManager->getRepository(User::class)
                        ->findBy(['username' => $username]);

                    foreach ($usernameTestUser as $user) {
                        if ($user->hasApplication($app)) {
                            return $this->json([
                                'success' => false,
                                'error' => 'Username taken.',
                                'error_code' => 10
                            ], JsonResponse::HTTP_BAD_REQUEST);
                        }
                    }
                }

                if (!empty($email)) $user->setEmail($email);
                if (!empty($username)) $user->setUsername($username);
                if (!empty($firstname)) $user->setFirstName($firstname);
                if (!empty($lastname)) $user->setlastName($lastname);
                if (!empty($othernames)) $user->setOtherNames($othernames);
                $user->setUpdatedAt($now);
                $this->objectManager->flush();

                $emailData = [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'username' => $username
                ];

                $message = (new \Swift_Message($_ENV['ACCOUNT_UPDATE_MAIL_SUBJECT']))
                    ->setFrom($_ENV['MAIL_FROM'])
                    ->setTo($email)
                    ->setBody($this->renderView('mail/account_update_notify.html.twig', $emailData), 'text/html')
                    ->addPart(
                        $this->renderView('mail/account_update_notify.txt.twig', $emailData), 'text/plain'
                    );

                // Send the email.
                $this->mailer->send($message);

                return $this->json([
                    'success' => true,
                    'data' => [
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'first_name' => $user->getFirstName(),
                        'last_name' => $user->getLastName(),
                        'other_names' => $user->getOtherNames(),
                    ]
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'This user does not belong to this application'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json([
                'success' => false,
                'error' => 'User not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }


    /**
     * @Route("/delete/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['id' => $id]);

        $app = $this->getAuthenticatedApplication();

        if ($user) {
            if ($user->hasApplication($app)) {
                $now = new DateTime();
                /*$now = new DateTime();
                $user->setStatus(2);
                $user->setDeletedAt($now);*/
                $user->trash();
                $this->objectManager->flush();


                return $this->json([
                    'success' => true,
                    'account_delete_time' => $now
                ]);
            }else {
                return $this->json([
                    'success' => false,
                    'error' => 'User not found on this application.'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        }

    }

    /**
     * @Route("/user/password/{id}", name="changepassword", methods={"POST"})
     */
    public function profile(UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if(!$user){
            return $this->json ([
                'success' => false ,
                'error' => "User not found!",
                'error_code' => 10
            ],JsonResponse::HTTP_BAD_REQUEST);
        } else if($user->getStatus() != User::$activeStatus){
            return $this->json ([
                'success' => false ,
                'error' => "This user has been suspended!",
                'error_code' => 20
            ],JsonResponse::HTTP_BAD_REQUEST);
        } else if(!$user->hasApplication($this->getAuthenticatedApplication())){
            return $this->json ([
                'success' => false ,
                'error' => "This user does not belong to this application.",
                'error_code' => 30
            ],JsonResponse::HTTP_BAD_REQUEST);
        } else {

            $currentpassword = $this->request->request->get("old_password");

            $password = $this->request->request->get("new_password");
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);

            if($passwordEncoder->isPasswordValid($user, $currentpassword)) {

                $user->setPassword($encodedPassword);
                $user->setUpdatedAt(new DateTime());
                $this->objectManager->flush();

                return $this->json ([
                    'success' => true ,
                    'error' => "Success."
                ]);

            }else{
                return $this->json ([
                    'success' => false ,
                    'error' => "Invalid password!",
                    'error_code' => 40
                ],JsonResponse::HTTP_BAD_REQUEST);
            }
        }


    }

}
