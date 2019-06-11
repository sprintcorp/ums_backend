<?php

namespace App\Command;


use App\Entity\Application;
use App\Entity\ApplicationUser;
use App\Entity\User;
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
 * Class CreateApplicationCommand
 *
 * This class is our flexible testing command that allows us to create
 * applications from the command line.
 *
 * @package App\Command
 * @author dev1 -> Ore Richard
 */
class CreateApplicationCommand extends Command
{

    // The oauth2 client manager.
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'ums:app:create';


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
            ->setDescription('Creates a new application')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create application for testing.')

            ->addArgument('name', null, 'What\'s the application name? [required]', null);
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

        $appToken = hash('sha256', time());
        $accessToken = hash('sha256', microtime());


        $application = new Application();
        $application->setName($input->getArgument('name'));
        $application->setAppToken($appToken);
        $application->setAccessToken($accessToken);
        $application->setCreatedAt($now);
        $application->setStatus(1);

        $this->manager->persist($application);
        $this->manager->flush();

        // Returns output to the console.
        $output->writeln("Added a new application with  name: <info>".$application->getName()
            ."</info>, appToken: <info>".$application->getAppToken()."</info> accessToken: <info>"
            .$application->getAccessToken()."</info>");
    }

}