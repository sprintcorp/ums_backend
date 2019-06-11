<?php

namespace App\Command;


use App\Entity\Application;
use App\Entity\ApplicationUser;
use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserGroup;
use DateTime;
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
 * Class CreateGroupCommand
 *
 * This class is our flexible testing command that allows us to create
 * groups and an associated permission from the command line.
 *
 * @package App\Command
 * @author dev1 -> Ore Richard
 */
class CreateGroupCommand extends Command
{

    // The oauth2 client manager.
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'ums:group:create';


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
            ->setDescription('Creates a new group and its associated permission.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create groups with an associated permission for testing.')

            ->addArgument('application', null,
                'What\'s the id of the application that owns the group [required]', null)
            ->addArgument('name', null, 'Name of the group [required]', null)
            ->addArgument('role', null, 'What role should be assigned to the group '.
                '(One of: admin, user) [required]', null)
            ->addArgument('permission', null, 'What\'s the permission assigned to the group '.
                '(One of: list, view, create, edit, delete) [required]', null);
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
        $now = new DateTime();

        $application = $this->manager->getRepository(Application::class)
            ->findOneBy(['id' => $input->getArgument('application')]);

        if(!$application || empty($application)){

            $output->writeln("The application with id <info>".$input->getArgument('application')
                ."</info> does not exist. Please create the application first using <info>ums:app:create</info>.");
            exit;
        }

        $permission = new Permission();
        $permission->setMask(strtoupper($input->getArgument('permission')));
        $permission->setCreatedAt($now);

        $group = new UserGroup();
        $group->setApplication($application);
        $group->setRoles(strtoupper('role_' . $input->getArgument('role')));
        $group->setName($input->getArgument('name'));
        $group->setStatus(1);
        $group->setPermission($permission);
        $group->setCreatedAt($now);

        $this->manager->persist($group);
        $this->manager->flush();

        // Returns output to the console.
        $output->writeln("Added a new group to application <info>".$application->getName()."</info> with  name: <info>".$group->getName()
            ."</info>, role: <info>".$group->getRoles()."</info> permission: <info>"
            .$permission->getMask()."</info> with permission id: <info>".$permission->getId()."</info>");
    }

}