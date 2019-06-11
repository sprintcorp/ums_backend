<?php

namespace App\Controller\Auth;

use App\Controller\AppController;
use App\Entity\ApplicationUser;
use App\Form\RegistrationFormType;
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

class AuthController extends AppController
{

    private $mailer;

    public function __construct(RequestStack $request, ObjectManager $manager, \Swift_Mailer $mailer)
    {
        parent::__construct($request, $manager);
        $this->mailer = $mailer;
    }

    /**
     * @Route("/signup", name="api_register", methods={"POST"})
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function register(UserPasswordEncoderInterface $passwordEncoder)
    {

        $app = $this->getAuthenticatedApplication();

        //$errors = [];
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $this->request->request->get("username");
            $email = $this->request->request->get("email");

            $usernameTestUser = $this->objectManager->getRepository(User::class)
                ->findBy(['username' => $username]);

            $emailTestUser = $this->objectManager->getRepository(User::class)
                ->findBy(['email' => $email]);


            foreach ($usernameTestUser as $user){
                if($user->hasApplication($app)){
                    return $this->json([
                        'success' => false ,
                        'error' => 'Username taken.',
                        'error_code' => 10
                    ], JsonResponse::HTTP_NOT_FOUND);
                }
            }

            foreach ($emailTestUser as $user){
                if($user->hasApplication($app)){
                    return $this->json([
                        'success' => false ,
                        'error' => 'A user with this email already exist.',
                        'error_code' => 20
                    ], JsonResponse::HTTP_NOT_FOUND);
                }
            }


            $password = $this->request->request->get("password");
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);

            $confirmationToken = hash('sha256', microtime() . $email);

            $now = new \DateTime();

            $firstname = $this->request->request->get("first_name");
            $lastname = $this->request->request->get("last_name");
            $othernames = $this->request->request->get("other_names");

            if(!$othernames) $othernames = "";

            $role = $this->request->request->get('roles');
            if(!$role || empty($role)) $role = 'ROLE_USER';
            else $role = strtoupper('role_' . $role);

            //setting properties for new user data
            $user->setFirstname($firstname)
                ->setLastName($lastname)
                ->setOthernames($othernames)
                ->setUsername($username)
                ->setEmail($email)
                ->setRoles($role)
                ->setPassword($encodedPassword)
                ->setConfirmationToken($confirmationToken)
                ->setStatus(User::$inactiveStatus)
                ->setCreatedAt($now);

            try {
                $this->objectManager->persist($user);
                $this->objectManager->flush();

                $appUser = new ApplicationUser();
                $appUser->setCreatedAt($now)
                    ->setUserId($user->getId())
                    ->setApplicationId($app->getId());

                $this->objectManager->flush();

                $app->addUser($user);
                $this->objectManager->flush();

                $this->objectManager->persist($appUser);
                $this->objectManager->flush();


                $emailData = [
                    'token' => $confirmationToken,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'username' => $username
                ];

                $message = (new \Swift_Message($_ENV['REGISTRATION_MAIL_SUBJECT']))
                    ->setFrom($_ENV['MAIL_FROM'])
                    ->setTo($email)
                    ->setBody($this->renderView('mail/registration.html.twig', $emailData), 'text/html')
                    ->addPart(
                        $this->renderView('mail/registration.txt.twig', $emailData), 'text/plain'
                    );

                // Send the email.
                $this->mailer->send($message);

                return $this->json([
                    'success' => true ,
                    'data' => [
                        'id' => $user->getId(),
                        'username' => $username,
                        'email' => $email,
                        'first_name' => $firstname,
                        'last_name' => $lastname,
                        'other_names' => $othernames,
                        'confirmationToken' => $user->getConfirmationToken(),
                        'created_at' => $user->getCreatedAt(),
                        'updated_at'=> $user->getUpdatedAt()

                    ]
                ]);
            } catch (\Exception $e) {
                $errors = [
                    'success' => false ,
                    'error' => "Unable to save new user at this time." . $e->getMessage(),
                    'error_code' => 30
                ];
            }
        } else {

            $errors = [
                'success' => false ,
                'error' => $form->getErrors(true, false)[0][0]->getMessage(),
                'error_code' => 40
            ];
        }


        return $this->json($errors, JsonResponse::HTTP_FORBIDDEN);

    }

    /**
     * @Route("/signin", name="app_signin", methods={"POST"})
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function login(UserPasswordEncoderInterface $passwordEncoder)
    {
        $username = $this->request->request->get("username");
        $username_type = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $password = $this->request->request->get("password");

        $users = $this->objectManager->getRepository(User::class)
            ->findBy([$username_type => $username]);

        if($users){

            foreach($users as $user){

                if($user->hasApplication($this->getAuthenticatedApplication())){

                    if($passwordEncoder->isPasswordValid($user, $password)){
                        //To update user login time
                        $now = new \DateTime();
                        $user->setLastLogin($now);
                        $this->objectManager->flush();

                        //return "success";
                         return $this->json([
                             'success' => true,
                             'data' => [
                                 'id'=> $user->getId(),
                                 'first_name' => $user->getFirstName(),
                                 'last_name' => $user->getLastName(),
                                 'other_names' => $user->getOtherNames(),
                                 'username' => $user->getUsername(),
                                 'email' => $user->getEmail(),
                                 'roles'=>$user->getRoles(),
                                 'confirmationToken' => $user->getConfirmationToken(),
                                 'created_at' => $user->getCreatedAt(),
                                 'updated_at'=> $user->getUpdatedAt()
                             ]
                         ]);
                    }
                }
            }
        }

        // return error.
        return $this->json([
            'success' => false ,
            'error' => 'Invalid credentials.'
        ], JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * @Route("/user/{id}", name="api_profile", methods={"GET"})
     */
    public function profile($id)
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if($user) {
            return $this->json([
                'success'=>true,
                'data' => [
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'othernames' => $user->getOtherNames()

                ]
            ], 200);
        }else {
            return $this->json([
                'success' => false,
                'error' => 'User not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

    }

    /**
     * @Route("/user/activate/{token}", methods={"PUT"})
     */
    public function confirmAction(string $confirmationToken)
    {
        $user = $this->objectManager->getRepository(User::class)
            ->findOneBy(['confirmationToken' => $confirmationToken]);

        if ($user) {
            $now = new DateTime();
            $user->setStatus(1);
            $user->setUpdatedAt($now);
            $this->objectManager->flush();


                return $this->json([
                    'success' => true ,
                    'account_confirmation_time' => $now
                ]);
        }
        else {
            return $this->json([
                'success' => false ,
                'error' => 'User not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }



}
