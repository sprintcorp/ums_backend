<?php


namespace App\Controller;


use App\Exceptions\ApplicationNotExistException;
use App\Traits\AuthenticatedApplicationTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 *
 * This controller is the base controller that helps us to manage
 * the request and application relationship easily.
 *
 * This controller is essential to determining which application are
 * we currently working on.
 *
 * It is encouraged for every controller dealing with front end requests not relating to
 * to the creation of applications extend this controller.
 *
 * @package App\Controller
 * @author dev1 -> Ore Richard
 */
class AppController extends AbstractController
{
    use AuthenticatedApplicationTrait;

    /**
     * A copy of the request coming into the controller,
     * usable by our trait.
     *
     * @var Request
     */
    protected $request;

    /**
     * A copy of our object manager coming into the controller,
     * usable by our trait.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * A copy of our request headers coming into the controller,
     * usable by our trait.
     *
     * @var ObjectManager
     */
    protected $requestHeaders;

    /**
     * BaseController constructor.
     *
     * Here we are passing in everything that may be needed by our trait.
     *
     * @param Request $request
     * @param ObjectManager $manager
     */
    public function __construct(RequestStack $request, ObjectManager $manager)
    {
        $this->request = $request->getCurrentRequest();

        $this->objectManager = $manager;
        $this->requestHeaders = $this->request->headers;


        /**
         * Authenticate the application.
         */
        if(!$this->authenticateApplication()){
            //return new Response(json_encode($this->getApplicationAuthenticationError()));

            /*return $this->json([
                'error' => $this->getApplicationAuthenticationError()
            ]);*/

            throw new ApplicationNotExistException($this->getApplicationAuthenticationError());
        }
    }
}