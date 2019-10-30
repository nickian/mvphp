![MVPHP](public/images/mvphp.png | width=300)

MVPHP is a simple, easily hackable framework for developing Minimally Viable PHP Web Applications. Many PHP frameworks have a steep learning curve and have deeply abstracted their functionality. MVPHP is designed to keep things simple, easy, and closer to pure PHP. It also provides a simple and customizable implementation of Bootstrap for you to start creating a front-end without much hassle.

**What it includes:**

* Routing
* User registration, login, and forgot password using [delight-im/PHP-Auth](http://github.com)
* [jQuery](https://jquery.com), [Bootstrap](https://getbootstrap.com), [Font Awesome](https://fontawesome.com/icons?d=gallery&m=free), and [Animate.css](https://github.com/daneden/animate.css)
* [Gulp](https://gulpjs.com) with a few modules and a pre-configured gulpfile to build the front-end.
* Bootstrap themes from [Bootswatch](https://github.com/thomaspark/bootswatch)
* Data JSON API example using an authentication key header
* SMTP Email delivery using [SwiftMail](https://github.com/swiftmailer/swiftmailer)
* Creating PDFs from HTML using [KnpLabs/snappy](https://github.com/knplabs/snappy) and [wkhtmltopdf](https://github.com/wkhtmltopdf/wkhtmltopdf)

**What it is not:**

MVPHP is not a robust and well-tested PHP framework like Laravel, Symfony, Zend, etc. This framework is intended for rapidly developing proof-of-concept PHP web applications with minimal overhead, few opinions, and a lot of flexibility. Use it at your own risk! Please [create an issue](https://github.com/nickian/mvphp/issues) if you encounter a bug or have any suggestions.

## Requirements

* PHP 7.2
* MySQL 5.6
* Apache 2 (not yet tested with NGINX)
* Composer
* Node.js to build the front-end files

## Install

1. Clone this repository to your directory of choice.
1. Run `composer install` in the project root.
1. Run `npm install` in the project root to install the tools we'll need to build the front-end files.
1. Run `gulp` to build the front-end files.
1. Create a virtual host in Apache pointing to the project root.
1. Navigate to your domain your browser and run the installer.

## Basic Usage

### Overview

The basic idea behind this framework is to start with one simple class that will handle all of the basic functionality.

`app.php` is the bootstrap file that first loads our `config.php` settings, then and instantiates our main `$app` object. An `$auth` object is also instantiated for all authentication-related functionality. We point a property of our main object (`$app->auth`) to reference the `$auth` object to keep things simple and cohesive.

```php
$app = new MVPHP();
```

Almost everything we do will utilize this `$app` object.

### Routing

`app.php` also requires the `routes.php` file in the root directory, where we will define our initial routes. You'll see several working examples in this file.

There are a few ways to define a route and what it should do:

#### Closure

```php
$app->route('/example', function() use ($app) {
    // Do stuff here
});
```

In this example, a closure is an anonymous function that will run when the request URI matches the route. In order for the methods in our main MVPHP class to be accessible inside of this anonymous function, we specify to `use ($app)` in order to import this variable into the anonymous function's variable scope.

#### Handling Variable Parameters

```php
$app->route('/example/{id}', function($params) use ($app) {
    echo $params['id'];
});
```

You can indicate variable parameters in a URI with brackets. They are identified in an associative array with the key being the name defined in the route and the value being the actual value provided in the request URI.

##### Parameter Constraints

```php
$app->route('/example/{id:int}', function($params) use ($app) {
    echo $params['id'];
});
```

You can also define constraints for matching URI parameters by using a colon followed by the constraint type. Leaving out the constraint value will default to the "string" constraint.

**Constraint Options:**

* string (default) - Alpha numeric, underscores, and hyphens
* int - Integer
* regex=pattern - Regular Expression

**Regular Expression Examples:**

Any expression that works with PHP's `preg_match` function will work here. [This tool](https://regexr.com) is useful to test expressions before you implement them.

```php
// This example requires the ID value to be a 5 digit number
$app->route('/example/{id:regex=^[0-9]{5}$}', function($params) use ($app) {
    echo $params['id'];
});

// This example requires the ID to be lower or uppercase letters between 1-10 characters long
$app->route('/example/{id:regex=^[a-zA-Z]{1,10}$}', function($params) use ($app) {
    echo $params['id'];
});
```

#### Defer to a "Controller"

```php
$app->route('/example');
```

Sometimes your app will have a lot of routes and you would rather organize them into separate files than have them all in the `routes.php` file.

If you register a route like this, MVPHP will look for a correlated file with the same name in the `controllers` directory. In the above example, a route registered to `/example` will look for a `example.php`
file in the controllers directory and require it.

Now, you can register more routes related to `/example` endpoints in `example.php` file. For example, your `controllers/example.php` file could look like this:

```php
// Reiterate this route and do something with it this time
$app->route('/example', function() use ($app) {
    // Do stuff
});

// Register any other related route endpoints here
$app->route('/example/stuff', function() use ($app) {
    // Do stuff
});

```

##### Defer to a Specific Controller

If you want to use a specific file with a different name, simply specifiy it as the second parameter:

```php
$app->route('/example', 'myfile');
```

This will try to require `controllers/myfile.php`.

### Controllers

The concept of routes, controllers, and models are very loosely defined in this framework. We avoid using a bunch of different classes for the sake of simplicity. A controller, in this framework's context, should simply be a place to mediate between requests and functionality defined the model.

Look at the files in the `controllers` directory for examples.

### Handling Different Request Methods

We can test for different request methods by using the `action` method:

```php
$app->route('/example', function() use ($app){
    if ( $app->action('post') ) {
        // Do stuff on POST
    } elseif( $app->action('get') ) {
        // Do stuff on GET
    }
});
```

### Views

The view templates are stored in the `views` directory. We can call a view (usually from a controller) like this:

```php
$app->view('my-template');
```

This simply looks for `my-template.php` in the views folder and requires the file. If we have variables in the template file, we have to pass those into the view method, like this:

```php
$my_var1 = 'Stuff I want to echo in the template.';

$app->view('my-template', [
    'my_var1' => $my_var1
]);
```

Now `$my_var1` will be accessible inside the view template file.

We don't bother with a templating engine (because PHP *is* inherently a templating language). Just access the varialbes in your HTML like this:

```html
<p><?=$my_var1;?></p>
```

Conditional statements:

```html
<?php if ( isset($my_var1) ):?>
<p><?=$my_var1;?></p>
<?php endif;?>
```

You get the idea. Pure and simple PHP templating.

### Building the Frontend

By default, the front-end uses [Bootstrap](https://getbootstrap.com). You can find all of the front-end source files in the `frontend` directory. The instructions to build the source files from the `frontend` folder are defined in the `gulpfile.js` file in the project root.

When you run `gulp` for the first time in the project root, the SCSS and JavaScript files were combined, minified, copied over to the `public` folder. Vendor JS and CSS files that we utilize (jQuery, Bootstrap, etc.) are also copied over to the `public` folder.

#### Developing the Front-End

Before editing the SCSS and JavaScript files in the `frontend` directory, you can run `gulp watch` in the project root. Gulp will watch for any changes you save to the files are save them as you go.

### Additional Functionality

#### Creating PDFs

The `models/Documents.php` class provides one method to write a PDF file from an HTML source. This class uses wkhtmltopdf and may require you to install these dependencies before it will work:

`sudo apt-get install xfonts-base xfonts-75dpi urw-fonts`

More PDF and other "Documents" related functionality will be added in the future.

#### Misc

Dig deeper into other available utility functions by looking at the `models/MVPHP.php` file, which contains our main class. More will be added in the future!
