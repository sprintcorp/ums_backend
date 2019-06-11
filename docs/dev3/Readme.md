# Documentation for User management System (UMS) Endpoints

#### The endpoint of the ums helps in carrying out all neccessary operations required to have a system that helps manage user administration in the system which focuses on the following;<br>

- Database Migration<br>This focuses on creating a database in which all user data are stored in a table after its creation from a user entity. The databse migration is created by running the following command

  ```bash
  php bin/console make:migration
  ```
  The above command helps create a migration file from the user entity

```bash
php bin/console doctrine:migrations:migrate.
```
This command helps represenrt the migration files into a table in the daabase.

- Registration<br> This process involve creating an api that helps register a new user into the sytstem by taking in user provided data and matching it with the table cells in order to store it accordingly. This process was carried out creating a Usercontroller using the below command
  ```bash
  php bin/console make:controller UserController.
  ```
  Afterwards, a register model was created to help point to the registration function created within the usercontroller. this function is structured to take take user password with minimum length of 6 and has a password confirmation which helps to guide against wrong input from user end by matching both password and password confirmation. The main purpose of using this method instead of fos-bundle is because fos-bundle supports and maintenance is not provided for symfony 4 which is stated on the symfony website
  `WARNING: You are browsing the documentation for version 2.0.x which is not maintained anymore. If some of your projects are still using this version, consider upgrading`
and this makes it not a good choice for the development.
- Login <br> This is a process where by an api to authenticate registered and authorized user are created in order to give them access into a defined system base on validating provided credentials with database credentials. This process is carried out using symfony auhtentication system in order to grant user access to pages base on roles defined in the database. The controls to this page and access are all definedin the `pagickages/security.yaml` files under `access_control`. The process involve in creating the authentication involve using the below command
  ```bash
  php bin/console make:auth
  ```
  Upon running this command a name is needed to be define for this authentication, after which it defines authenticator path to be use by the application within the in the `packages/security.yaml` file. Afterwards a function to help get success responce upon login can be define within the `UserController` and creating a route to help poit to the function.
- Logout<br> This give a user the ability to logout we don’t need to write any code, we need to make some configuration. First, we need to set a route. For this it can’t be a controller method, it just needs to be a route. To do that, we need to modify `routes.yaml` in the `config folder`. To create the logout route you will add the following to our file
  ```bash
  api_logout:
      path: /logout
    ```
    Yaml is spacing sensitive so make sure there are 4 spaces in front of the path attribute.
- Delete Account <br> This endpoint provide user with an option of deleting account by using user unique identifier, passing it to a the function in the usercontroller.
- Update/Edit Account<br> This endpoint provide user with an option of updating/editing account by using user unique identifier, passing it to a the function that will help update user data in the usercontroller
- Create Api Dummy Data<br> This api dummy data are used to test each function of the whole application.


