<?php


namespace App\Traits;


use App\Entity\Application;

/**
 * Trait AuthenticatedApplicationTrait
 *
 * General trait to be used by all controllers using BaseController.
 * This trait provides the ability to track which application
 * is currently making a request to the platform.
 *
 * @package App\Traits
 * @author @dev1 -> Ore Richard
 */
trait AuthenticatedApplicationTrait
{

    /**
     * A private copy of our authenticated application.
     * @var
     */
    private $application;

    /**
     * Authentication error.
     * @var
     */
    private $applicationAuthError;

    /**
     * Attempts to authenticate an application and returns true if it succeeds or false otherwise.
     * @return bool
     */
    public function authenticateApplication(): bool
    {
        $token = $this->requestHeaders->get('X-App-Key');
        if(!$token) {
            $this->applicationAuthError = 'No authorization header found'; // No authorization header found.
            return false;
        }

        // We have an authorization header. Time to get our bearer token.
        //$token = explode(' ', $token);

        // We must have exactly two parts. Bearer and the token.
        if(strlen($token) > 80){
            $this->applicationAuthError = 'Invalid token type'; // Invalid token type.
            return false;
        } else {

            $app = $this->objectManager->getRepository(Application::class)
                ->findOneBy(['accessToken' => $token]);

            if(!$app){
                $this->applicationAuthError = 'Application not found'; // This application doesn't exist.
                return false;
            } else {
                $this->application = $app;
                return true; // Successfully got an application.
            }
        }
    }

    /**
     * Returns the error associated with the last attempt to check if the authenticated application.
     * @return string
     */
    public function getApplicationAuthenticationError(): string
    {
        return $this->applicationAuthError;
    }

    /**
     * Returns the authenticated application in this request.
     * @return Application
     */
    public function getAuthenticatedApplication(): Application
    {
        return $this->application;
    }
}