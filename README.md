# User Management System

This is the base `Symfony` installation fo this system.<br>
This installation is based on the `symfony/website-skeleton` base Symfony project.

<br>

##### Why website-skekelton?
`website-skeleton` was chosen in order to have a sufficient backbone for the project that needs fewer post-installation requirements in different branches. A common alternative would be `skeleton`. But this option is easily deemed not eligible by the mere fact that a lot of packages which comes pre-installed with `website-skeleton` would still need to be installed.<br>

Significantly: 

- Doctrine ORM
- Web Server Bundle (For development)
- Annotations (For easy routing)
- Templating (twig)

<br>

#### Symfony Installation Code
The following code was run on the terminal to perform this 
skeletal installation.

<br>

```bash
composer create-project symfony/website-skelton .
```

<br>


#### Installed Version
```bash
Symfony 4.2.8
```

<br>

#### Testing Code
The following code was used to test if the installation was successful.

```bash
php bin/console server:run
```

The test ran successfully and `Symfony`'s default welcome page was accessible via a web browser.

<br>

#### Test Conditions

- System Processor: `Intel Core i3 2.0GHz`
- Bus Width: `64 bit`
- System RAM: `4gb`
- System Host OS: `Windows`
- xDebug: `Missing`
- APCu: `Missing`
- OPCache: `Missing`
- Environment: `dev`
- Test route: `http://127.0.0.1:8000`
- PHP Memory Limit: `128MB`

<br>

#### Test Result:

- Render Time: `916ms`
- Peak Memory Usage: `14.0MB`
- Initialization Time: `462ms`
