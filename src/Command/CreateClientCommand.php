<?php

namespace App\Command;


use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CreateClientCommand
 *
 * This class is our flexible testing command that allows us to create
 * oauth2 clients from the command line.
 *
 * @package App\Command
 * @author dev1 -> Ore Richard
 */
class CreateClientCommand extends Command
{

    // The oauth2 client manager.
    private $clientManager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'ums:client:create';


    public function __construct(ClientManagerInterface $clientManager, string $name = null)
    {

        // Since the __construct calls configure, and configure needs all properties ready,
        // we are calling this before inheriting default characteristics.
        $this->clientManager = $clientManager;


        parent::__construct($name);
    }

    protected function configure()
    {
        $this

            // the short description shown while running "php bin/console ums:client:create"
            ->setDescription('Creates a new client')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create oauth2 clients for testing.')

            // Allow configuring grant types via --grant-type
            ->addArgument('grant-type', null,
                'Set allowed grant type. Use multiple times to set multiple grant types. [Default: <info>password</info>]',
                null)

            // Allow configuring redirect uri's via --redirect-uri
            ->addArgument('redirect-uri', null,
                'Sets the redirect uri. Use multiple times to set multiple uris.',
                null);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        foreach ($this->getDefinition()->getArguments() as $argument) {
            if ($input->getArgument($argument->getName())) {
                continue;
            }

            $values = [];

            while(true) {

                // Get value from console.
                $value = $io->ask($argument->getDescription(), $argument->getDefault());

                // If the value is empty, go to the next command.
                if(empty(trim($value))) break;

                // else, add it to the values list.
                else $values[] .= $value;
            }

            $input->setArgument($argument->getName(), $values);
        }
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $grantType = $input->getArgument('grant-type');
        if(empty($grantType)) $grantType = ['password'];

        // Create the client from the user's input/configuration.
        $client = $this->clientManager->createClient();
        $client->setRedirectUris($input->getArgument('redirect-uri'));
        $client->setAllowedGrantTypes($grantType);
        $this->clientManager->updateClient($client);

        // Returns output to the console.
        $output->writeln("Added a new client with  public id <info>".$client->getPublicId()
            ."</info> and secret <info>".$client->getSecret()."</info>");
    }

}