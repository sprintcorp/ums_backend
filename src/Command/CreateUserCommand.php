<?php

namespace App\Command;


use App\Entity\Application;
use App\Entity\ApplicationUser;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CreateUserCommand
 *
 * This class is our flexible testing command that allows us to create
 * users from the command line.
 *
 * @package App\Command
 * @author dev1 -> Ore Richard
 */
class CreateUserCommand extends Command
{

    // The oauth2 client manager.
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'ums:user:create';


    public function __construct(ObjectManager $manager)
    {

        // Since the __construct calls configure, and configure needs all properties ready,
        // we are calling this before inheriting default characteristics.
        $this->manager = $manager;


        parent::__construct();
    }

    protected function configure()
    {
        $this

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create users for testing.')

            ->addArgument('application', null, 'What\'s the id of the application that owns the user? [required]', null)
            ->addArgument('firstname', null, 'User\'s first name [optional]', null)
            ->addArgument('lastname', null, 'User\'s last name [optional]', null)
            ->addArgument('othernames', null, 'User\'s other names [optional]', null)
            ->addArgument('username', null, 'User\'s username [required]', null)
            ->addArgument('email', null, 'User\'s email address [required]', null)
            ->addArgument('role', null, 'User\'s role (One of user, admin, superuser) {Default: user} [required]', null)
            ->addArgument('password', null, 'User\'s password [required]', null);
    }

    private function getInteract(InputInterface $input, OutputInterface $output, $io, $argument){

        // Track the required and optional status.
        $status = true;

        while($status) {
            $value = $io->ask($argument->getDescription(), $argument->getDefault());

            if(empty(trim($value))){

                if(!preg_match('/\[required\]/', $argument->getDescription())){ // It is not a required argument.
                    $status = false;
                    break;
                } else {

                    $output->writeln($argument->getName() . " is required.\n");
                }
            } else $status = false;
        }

        $input->setArgument($argument->getName(), $value);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        foreach ($this->getDefinition()->getArguments() as $argument) {
            if ($input->getArgument($argument->getName())) {
                continue;
            }

            $this->getInteract($input, $output, $io, $argument);
        }
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Initialize creation time.
        $now = new \DateTime();

        // Create the client from the user's input/configuration.

        $application = $this->manager->getRepository(Application::class)
            ->findOneBy(['id' => $input->getArgument('application')]);

        //die(print_r($application, true));

        if(!$application || empty($application)) {

            $output->writeln("The application with id <info>".$input->getArgument('application')
                ."</info> does not exist. Please create the application first using <info>ums:app:create</info>.");
            exit;
        }

        $user  = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setEmail($input->getArgument('email'));
        $user->setPassword(password_hash($input->getArgument('password'), PASSWORD_DEFAULT, ['cost' => 10]));
        $user->setRoles(['role_' . $input->getArgument('role')]);
        $user->setFirstName($input->getArgument('firstname'));
        $user->setLastName($input->getArgument('lastname'));
        $user->setOtherNames($input->getArgument('othernames'));
        $user->setCreatedAt($now);
        $user->setStatus(1);

        $this->manager->persist($user);
        $this->manager->flush();

        $appUser = new ApplicationUser();
        $appUser->setCreatedAt($now);
        $appUser->setUserId($user->getId());
        $appUser->setApplicationId($application->getId());
        $this->manager->flush();


        $application->addUser($user);
        $this->manager->flush();


        $this->manager->persist($appUser);
        $this->manager->flush();

        // Returns output to the console.
        $output->writeln("Added a new user with  username: <info>".$user->getUsername()
            ."</info> to application <info>".$application->getName()."</info>");
    }

}