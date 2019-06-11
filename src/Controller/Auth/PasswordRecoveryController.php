<?php


namespace App\Controller\Auth;


use App\Controller\AppController;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PasswordRecoveryController
 *
 * Handles all things related to password recovery.
 *
 * @package App\Controller\Auth
 * @author dev1 -> Ore Richard
 *
 * @Route("/password", methods={"post"}, name="password_")
 */
class PasswordRecoveryController extends AppController
{

    protected $mailer;
    protected $encoder;

    public function __construct(RequestStack $request, ObjectManager $manager, Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct($request, $manager);
        $this->mailer = $mailer;
        $this->encoder = $encoder;
    }

    /**
     *
     * This is the endpoint for requesting a password reset.
     *
     * @return JsonResponse
     * @Route("/reset", name="reset")
     * @throws \Exception
     */
    public function requestPasswordReset()
    {
        $app = $this->getAuthenticatedApplication();


        $username = $this->request->request->get('username');

        // Find out if our user supplied us with a username or an email address.
        $username_type = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find the user with the username or email address.
        $user = $this->objectManager->getRepository(User::class)
            ->findOneBy([$username_type => $username]);

        if(!$user){

            return $this->json([
                'error' => "Invalid {$username_type}"
            ]);
        } else if(!$user->hasApplication($app)){ // If the user is registered to this application.

            return $this->json([
                'error' => "This user does not belong to this application."
            ]);
        } else {

            $recovery_token = hash('sha256', $user->getId() . $user->getEmail() . microtime());
            $request_time = new DateTime();


            $mail_data = [
                'firstName' => $user->getFirstname(),
                'lastName' => $user->getLastname(),
                'token' => $recovery_token,
                'applicationName' => $app->getName(),
                'requestTime' => $request_time
            ];

            $message = (new \Swift_Message($_ENV['RECOVERY_MAIL_SUBJECT']))
                            ->setFrom($_ENV['RECOVERY_MAIL_FROM'])
                            ->setTo($user->getEmail())
                            ->setBody($this->renderView('mail/password_reset.html.twig',
                                $mail_data), 'text/html')
                            ->addPart($this->renderView('mail/password_reset.txt.twig',
                                $mail_data), 'text/plain');


            $mails_sent  = $this->mailer->send($message);
            $mail_sent_time = new DateTime();

            // If our email was successfully sent.
            if($mails_sent > 0){

                $user->setConfirmationToken($recovery_token);
                $user->setPasswordRequestedAt($request_time);

                $this->objectManager->flush();

                return $this->json([
                    'success' => true,
                    'time' => [
                        'recovery_request_time' => $request_time,
                        'email_sent_at' => $mail_sent_time
                    ]
                ]);
            } else {

                return $this->json([
                    'success' => false,
                    'error' => 'Could not send reset link to this email address.'
                ]);
            }
        }
    }


    /**
     *
     * Does a password reset from a valid confirmation token.
     *
     * @param string $confirmation_code
     * @return JsonResponse
     * @throws \Exception
     * @Route("/reset/{confirmation_code}", name="change")
     */
    public function doReset(string $confirmation_code)
    {
        $user = $this->objectManager->getRepository(User::class)
            ->findOneBy(['confirmationToken' => $confirmation_code]);

        if($user){

            $old_password = $this->request->request->get('old_password');
            $new_password = $this->request->request->get('new_password');


            $last_db_password = $user->getPassword();
            $last_db_updated_time = $user->getUpdatedAt();

            if($this->encoder->isPasswordValid($user, $old_password)){

                $now = new DateTime();

                $new_password = $this->encoder->encodePassword($user, $new_password);
                $user->setPassword($new_password);
                $user->setUpdatedAt($now);

                $this->objectManager->flush();


                $mail_data = [
                    'firstName' => $user->getFirstname(),
                    'lastName' => $user->getLastname(),
                    'applicationName' => $this->getAuthenticatedApplication()->getName(),
                    'updateTime' => $now
                ];

                $message = (new \Swift_Message($_ENV['RECOVERY_MAIL_SUBJECT']))
                    ->setFrom($_ENV['RECOVERY_MAIL_FROM'])
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('mail/password_reset_notify.html.twig',
                        $mail_data), 'text/html')
                    ->addPart($this->renderView('mail/password_reset_notify.txt.twig',
                        $mail_data), 'text/plain');


                $mails_sent  = $this->mailer->send($message);
                $mail_sent_time = new DateTime();

                if($mails_sent > 0){

                    return $this->json([
                        'success' => true ,
                        'password_change_time' => $mail_sent_time
                    ]);
                } else {

                    // Revert changes and return error.
                    $user->setPassword($last_db_password);
                    $user->setUpdatedAt($last_db_updated_time);
                    $this->objectManager->flush();

                    return $this->json([
                        'success' => false,
                        'error' =>  'Could not complete password update.'
                    ]);
                }
            } else {
                return $this->json([
                    'success' => false,
                    'error' =>  'Invalid Password.'
                ]);
            }
        } else {
            return $this->json([
                'success' => false,
                'error' => 'User not found.'
            ]);
        }
    }
}

