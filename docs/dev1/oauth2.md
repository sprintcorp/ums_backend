## UMS OAuth2 Implementation

Library Used: `FOSOAuthServerBundle` <br>
Access token time to live: `One day (86400)` <br>
Refresh token lifetime: `2 weeks (1209600)` 

<br>

#### The introduction of four new entities

- `App\Entity\Client`
- `App\Entity\AccessToken`
- `App\Entity\RefreshToken`
- `App\Entity\AuthCode`

> This entities are a prerequisite to using the FOSOAuthServerBundle.

<br>

#### FOSOAuthServerBundle Installation

```bash
composer require friendsofsymfony/oauth-server-bundle
```
> Version Installed: `1.5`


<br>

<em>All entities were generated with their respective `make:entity` commands.</em>

<br>


## Testing & Demo Data

A functional test can be carried out on the OAuth2 server using the command interface `ums:client:create`.

<br>

#### Using the `ums:client:create` command.

```bash
Set the redirect uri. Use multiple times to set multiple uris.:
> ...

Set allowed grant type. Use multiple times to set multiple grant types. [Default: password]:
> ...
```

###### Expected Response Format:

<em>Added a new client with  public id `client id` and secret `secret key`</em>

<br>


