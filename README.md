# onelogin-scim-php

A simple PHP API built using [Slim](http://www.slimframework.com/) that handles and responds to all of the SCIM requests made by OneLogin for user provisioning.

The sample is based on the [OneLogin Core User Schema](https://developers.onelogin.com/scim/define-user-schema) but could be easily adapted to support the Enterprise schema.


## Dependencies
This projects uses composer. Execute at the root of the project
```
composer install
```
in order to install all the project dependencies.

You will also need to publish the app with Apache or Nginx.
Read more about how publish a Slim app [here](http://www.slimframework.com/docs/v3/start/web-servers.html).


## Endpoints
The endpoints are configured in the `src`folder, at the `routes.php` file and each has a corresponding method of a controller (UserController or GroupController) which is found in the `src/Controllers` directory.

```php
// Users
$app->get('/Users', 'UserController:list')->setName('users.list');
$app->get('/Users/{id}', 'UserController:get')->setName('users.get');
$app->post('/Users', 'UserController:create')->setName('users.create');
$app->put('/Users/{id}', 'UserController:update')->setName('users.update');
$app->delete('/Users/{id}', 'UserController:delete')->setName('users.delete');

// Groups

$app->get('/Groups', 'GroupController:list')->setName('groups.list');
$app->post('/Groups', 'GroupController:create')->setName('groups.create');
$app->patch('/Groups/{id}', 'GroupController:update')->setName('groups.update');
```

In addition there is an endpoint to generate the Bearer Token required to authenticate the requests,
```
$app->get('/jwt', 'JWTController:generate')->setName('jwt.generate');
```

Right now that endpoint is public and open, but in production should be removed or protected by user credentials.

## User/Group Store
When you run this sample, you first need to execute
```
php db/database.php
```

and it will create a `scim.sqlite` file in the `db` folder that contains `users` and `groups`. 
Be sure the proper permissions are set

This file based db is powered using [Sqlite](https://www.sqlite.org/index.html) and is not intended for production use. It is simply for this example.

It is expected that you would replace the calls to this db with your own database or api calls.


## Logging

This sample shamelessly uses `logs/app.log` all over the place as a simple way to display whats going on as the endpoints are hit. It is expected that you will replace this with your favorite logging solution.


## Run the sample
You can run this locally and watch the logs as OneLogin sends provisioning requests.

### 1. Download the code and install dependencies
From your terminal

```sh
git clone https://github.com/onelogin/onelogin-scim-php.git
cd onelogin-scim-php && composer install
```

### 2. Set your Authorization bearer token
The OneLogin SCIM implementation uses a bearer token supplied in an authorization header of each request. This api will validate the token matches before allowing any provisioning tasks to take place.

Access the /jwt endpoint described previously to get a valid token.

You will need to enter the same token into the **SCIM Bearer Token** field when setting up your SCIM app via the OneLogin portal.

### 3. Run the code
This will start the SCIM API on `http://localhost:8080`
```sh
cd public
php -S localhost:8080
```

### 4. Expose the API to the internet
To run this sample end to end with OneLogin it needs to be exposed to the internet so that OneLogin can make provisioning requests to the various endpoints.

For this we recommend using Apache or Nginx. On produciton, be sure you protect the /jwt endpoint and you enable HTTPs

### 5. Configure your SCIM app in OneLogin
If you already have a SCIM app configured then simply paste the ngrok url in the **SCIM Base URL** field on the **Configuration** tab of your app.

![onelogin scim app](https://s3.amazonaws.com/onelogin-screenshots/dev_site/images/scim-app.png)

If you don't have a SCIM app configured yet then [follow this guide to create an app](https://developers.onelogin.com/scim/create-app) using the **SCIM Provisioner with SAML (Core Schema v1.1)**.

### 6. Trigger provisioning tasks and test the app
Follow [this guide to enable provisioning and test](https://developers.onelogin.com/scim/test-your-scim) your SCIM API.
