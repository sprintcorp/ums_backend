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
 * Class Add2GroupCommand
 *
 * This class is our flexible testing command that allows us to add
 * users to groups from the command line.
 *
 * @package App\Command
 * @author dev1 -> Ore Richard
 */
class Add2GroupCommand extends Command
{

    // The oauth2 client manager.
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'ums:group:add';


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
            ->setDescription('Adds a user to a group in an application.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to add users to a group.')

            ->addArgument('application', null,
                'What\'s the id of the application that owns the group [required]', null)
            ->addArgument('user', null, 'ID of the user. [required]', null)
            ->addArgument('group', null, 'ID the the group to add user to. [required]', null);
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

        $group = $this->manager->getRepository(UserGroup::class)
            ->findOneBy(['id' => $input->getArgument('group')]);

        $user = $this->manager->getRepository(User::class)
            ->findOneBy(['id' => $input->getArgument('user')]);

        if(!$application || empty($application)){

            $output->writeln("The application with id <info>".$input->getArgument('application')
                ."</info> does not exist. Please create the application first using <info>ums:app:create</info>.");
            exit;
        } else if(!$group || empty($group)){

            $output->writeln("The group with id <info>".$input->getArgument('group')
                ."</info> does not exist. Please create the group first using <info>ums:group:create</info>.");
            exit;
        } else if($group->getApplication() != $application){

            $output->writeln("This group <info>".$group->getName()."</info> with id <info>".$input->getArgument('group')
                ."</info> does not belong to the application <info>".$application->getName()."</info>.");
            exit;
        } else if(!$user->getApplications()->contains($application)){

            $output->writeln("The user <info>".$user->getUsername()."</info> with id <info>".$input->getArgument('user')
                ."</info> does not belong to the application <info>".$application->getName()."</info>.");
            exit;
        }

        $group->addUser($user);

        $this->manager->persist($group);
        $this->manager->flush();

        // Returns output to the console.
        $output->writeln("The <info>".$application->getName()."</info> user with username <info>".$user->getUsername()
            ."</info> was successfully added to the <info>".$application->getName()."<info> group ".$group->getName()."</info>.");
    }

}