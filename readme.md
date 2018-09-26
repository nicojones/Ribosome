## <img src="https://i.imgur.com/xhDwTWD.png" height="40px"/> Ribosome

Lightweight PHP framework. Packed with goodies like
* Router
* Authentication
* Assets
* File minimisation
* director.php for file generation
* more stuff ...
* ...
* ... yes, even more

See the README online [here][1]

See the full docs [here][2] (generated from comments) or [download them][3]

Fork me on Github, that would make me happy: [https://github.com/nicojones]

My personal websites can be found at [https://kupfer.es/]

## How to set it up
1. Recommended way. Type into your terminal

        git clone https://github.com/nicojones/Ribosome.git
        
     Once cloned, navigate to the folder `/app` and run the `composer.lock` file with `composer install`. You can get Composer [here](https://getcomposer.org/).
2. Alternative way:  
	404 NOT FOUND.
	
Once downloaded, set up your environment. If you will make a virtual server just for Ribosome, make the `root` point to the `/public` folder, as `app.php` is the index file. In that way, `http://ribosome.local` (or whatever you use) points to `/public`.

If you *don’t* have a virtual server, or you are just running it as a subfolder of, say, `http://localhost`, you need to edit one file: `/app/config/config.ini`, and set the `__PATH__` global variable to the folder, **including leading slash /**. For example, if you are running it under

	http://localhost:3000/php/Ribosome/public

Then you need to set

	__PATH__ = "/php/Ribosome/public"

That’s it, refresh and you should see the Welcome message.

## micro Tutorial
We are going to build a page that shows the current date and time, taking in parameters from the URL.

First of all, we will create a `DateController`. There are two easy ways to do this:
1. By accessing the Configuration Panel on `/?_bootload_` and entering the (default) password 1234.  
	From there we go to the last option, and we expand it.
	  
	**Generate a controller or model**

	Here we can enter the name of the controller we want (in our case, `Date`, without the “Controller” part of the name) and we click on `Generate a Model as well`. We don’t need it for the example but you might need it in the future.
	Copy the controller basic structure into `/src/controllers` and the model into `/src/models`.

2. From the terminal, on your project root, call

		nicoJones:Ribosome ~$ php director.php generate:both Date 

and automatically the Controller and the Model will be generated and placed in the appropriate folder.

Now, we’re ready to start!

First of all, we need a url `path` for our date app, to access it from our browser. We can call it `date`, to keep the naming convention (Model/View/Controller).

Go to `/src/config/routing.ini` and add a new entry for this app:  

	[Date]
	path = date
	action = Date@showDate

In `/src/config/permission.ini` we don’t need to add anything (yet) because we want this page to be accessible by everyone (access level `1`.)

We are going to add the content now:
In `/src/controllers/DateController.php` we create a method called `showDate` that takes no parameters:

	public function showDate() {
	    // ....
	}

And we show a view, by adding `$this->show(...)`:
````php
public function showDate() {
    $this->show('date/index');
}
````

Last thing is to create a file named `index.php` inside of `src/resources/views/date/` with the content:  

	<h1 class="text-center"><?php echo date('Y-m-d H:i');?></h1>

That’s it, go to `/date` on your browser and you should see the current date.

### Pass parameters
There is, of course, a way to pass parameters from the URL to the Controller or View.
Say, for example, that we want to pass our own format. We could implement  `?format=H.i` and get only the time: by going to `/date/?format=H.i` and changing the view to:

	<h1 class="text-center"><?php echo date(
	!empty($_GET['format'])
	? $_GET['format']
	: 'Y-m-d H:i'
	); ?></h1>

and it would display your format… but this is really bad practice.

But we could also do it in a nicer way by getting the parameter in the Controller, with `getGet`:

	public function showDate() {
	   $format = $this->getGet('format', 'H.i');
	   $this
	        ->add('format', $format)
	        ->show('date/index');
	}

and change the view accordingly to use only the parameter:

	<h1 class="text-center"><?php echo date($format);?></h1>

*Note: To add PHP variables to the view, use the method `add` with `$this->add('foo', $fooVar)`. If you want to add it as a Javascript value, use instead `addJSVar`.*

We can even go a bit further and make the URI component more user-friendly. Ideally, we would like something like:
`/date/format/H.i.s`.
Let’s do it! From the routing page `/src/config/routing.ini` we will change how it is processed, using wildcards:

	[Date]
	path = date/:word/:format
	action = Date@showDate
	default[word] = format
	default[format] = H.i.s

Any *lowercase alphanumeric* word prepended with `:` will function as a wildcard, accepting any parameter from the URL. In this case we added two (`:word` and `:format`): in this way, we can have both `/date` and `/date/format/Y-m-d` use the same code.

*Note: `/date/somethingRandomHere/Y-m-d` will also go to the same page, and `/date/format/` (and nothing else) will take `H.i.s` as the default date format.*

Done! Try `/date/format/Y-m-s H:i:s` or `/date/format/U` (unix timestamp) to see different formats displayed.

### Authentication
We’ll change a couple of settings, in less than 2 minutes, that will allow ONLY logged in users to access the `/date` page, and unlogged users to get redirected to `/login`.

Go to `/src/config/permissions` and add a new entry for `Date`:

	[Date]
	__throw_to = "Login"
	showDate = 2
	* = 1

This is very simple. Ribosome is built on top of an access level system, meaning `1 = unlogged` and `2 = logged` (there’s also `3 = superadmin`).

What this entry in `permissions.ini` is saying is:

*A logged in user [level 2] can access `showDate`. Any unlogged user can visit any other page from the `DateController` except for `showDate`: otherwise they will be redirected to `/login`, which is the path specified under the `routing.ini` key `[Login]`.*

Try accessing `/data` now and you’ll be redirected to `/login`.
Use the default username and password and you’ll end up in the Login Main Page, currently set to `/login/home`. Navigate to `/date` and… voilà! There’s your clock!

There’s a small inconvenience, though… you’ve noticed how we got redirected *away* from `/date`, but the system didn’t redirect us *back* to `/date`. Why is that?
The reason is that we want to avoid to redirect the users *always* when they access and unauthorised page. Instead, we want to specify *to which pages* are they allowed to be redirected.
This is one of those, so let’s set that up.

In `routing.ini` we add the line `after_login = 1` so it looks like this:

	[Date]
	path = date/:word/:format
	default[word] = "format"
	default[format] = "H.i.s"
	action = Date@showDate
	after_login = 1 

This tells Ribosome that `[Date]` is a page to which the user can be redirected after login.

Okay! Now log out (`/logout`), and visit `/date` again. Login… and you’re back looking at our beautiful clock.

Feel free to tinker with the code. Don't forget to look at the docs for reference, or just download them.


### Other small stuff

#### Links, paths and redirects
To create external links, do it as you always did, `<a href="...."></a>`.  
For internal links, on the other hand, we rely *uniquely* on `routing.ini` keys. These
are the keywords encapsulated by `[..]` in `/app/config/routing.ini`, `/src/config/routing.ini`
and (if you have any Ribosome vendors in `<ROOT>/vendor`) under `/vendor/<Vendor Name>/config/routing.ini`.

Let's say we want to add a link to log in. We have the path already configured (check
`/src/config/routing.ini`, under the `[Login]` key).

To add a link, just do
````php
<a href="<?php $this->path('Login') ?>">Login</a>
````
As you can see, we rely completely on the `key`, which will unlikely be changed.  

Note: If you ever want to change the `/<path-to-login>` to be more secretive you can do so from the 
`routing.ini` file: just set the `path = /some-secret-login-path` and *all* the links
in all of your project will change!  

Also notice *you don't need to echo*, `->path()` takes care of it. That's because
it's an alias for `->url()` that echoes the result:
````php
public function fooBar() {
    // ... your code here...
    ...
    
    $this->header( 401 );
    $this->redirect( $this->url('Login') );
}
````
`url` will return the correct (absolute) url for the path you specified, but will **not** echo it.
 In the example above, `$this->redirect($url, $headers)` works really well with `$this->url($key)`
to redirect!

(From here on, we talk only about `url()`. `path()` has exactly the same features.)  

If you want to add parameters to your URL, it will be done automatically just by
adding an associative array as a second argument:
````php
$this->url('Login', ['backdoor' => 1, 'superpassword' => 1234])
````
will create the uri `/login?backdoor=1&superpassword=1234`.

#### Javascript `addJSVar`
If you have any parameters that need to be passed to your `javascript` code, you can do so
from any controller with `$this->addJSVar('nameOfVar', $value)`. Ribosome will take care of
encoding it properly and adding it as a global variable `var`. See an example lower down.

#### PHP `addVar`
Quite obvious. Pass some values to your php views! See an example lower down.

### Helper functions
There are loooots of these ones! And are all located in `/app/Kernel/Helpers/aliases.php` and
`/app/Kernel/Helpers/support_functions.php`. Further documentation can be found in the functions
themselves.

1. **Clock**. Use `clock_start($key = 'main')` to start a timer. You can start as many as you want
but make sure to use different keys. You can stop them manually with `clock_end($key = 'main')`.
And when you want to output, use `clock_time($key = 'main')`.
2. **Authenticated? Is it production? Is it an AJAX request?**. All this and much more can be found
    on `aliases.php`. You know, for when you are too lazy to include a class just for this, or your
    fingers hurt from coding.
    ````php
    if (isAjax()) {
        if (isAuth()) $this->json($userHistory);
        else $this->json(['success' => 0]);
    }
    ````
    (this is just an example)
3. **$this->json()** (in ParentController.php). Use this function to return a JSON request.
    By default (i.e. if you send it empty), the structure will be
    ````json
    {
      "success": 1,
      "responseData": {
        "message": ""
      }
    }
    ````
    So sending
    ````php
    $this->json(['success' => 0, 'message' => 'error 404', 'foo' => 'bar'])
    ````
    will automatically generate the following:
    ````json
    {
      "success": 0,
      "responseData": {
        "message": "error404",
        "foo": "bar"
      }
    }
    ```` 
    that is, everything will be placed under `responseData` except for `success`.

4. **Header, Footer and Title**. To add any of those three things, we do it from the controllers,
    as it should be:
    ````php
    public function showHome() {
        //...
        $this
            ->setTitle("Home Page")
            ->addHeader(['active' => 'home'])
            ->addFooter(['foo' => 'bar')
            ->addJSVar('imAJavascriptVariable', $phpArrayOrSomething)
            ->addVar('useMeInTheView', $myVar)
            ->show('home/index');
    }
    ````
    As it's clear in the example, you can pass parameters to the Header and Footer, which are
    special files located under `/src/resources/views/blocks` and use `header.php` and `footer.php`.

5. **Views**. You can pass parameters to the views by using `$this->addVar('foo', $bar)` from
any controller. Then use that value in the view with `$foo`, like you would normally do.
To the `->view( ... )` function you only need to pass the folder and file names, starting from
`/src/resources/views` as the parent folder.  
**DO NOT append `.php` to the function parameter**.  
If you need to include subviews, use `$this->get()`. Works identically but returns the code instead
of `echo`ing it.

### Session
To store and use session parameters all you need is the `Session` module.
Use the namespace `Core\Providers` at the beginning of the file to include it.  
Session has some magic methods to set and get variables:
````php
$foo = ['food' => 'sweet potato', 'drink' => 'water'];
Session::setFood($foo); // returns value as well.
$bar = Session::getFood(); // now: $bar = [ ... ], the whole array;

// to retrieve only the drink in one line, you can do:
Session::getExtFood('drink');

//delete it:
Session::cleanFood(); // returns the variable and deletes its value.
````
Look at the class `/app/Kernel/Providers/Session.php` for more info.

### Model
The Model (`/app/models/ParentModel.php`) is a helper class that all models extend:
````php
class MyCustomModel extends Model {
    //...
}
````
(remember to generate these classes with `director.php` or the admin tool. See the [Tutorial](#micro-Tutorial)
for more info.)

To execute a query, you need first to enable database use. You can do that by manually editing the
configuration file `/app/config/config.ini` under the `[Database]` key, or by accessing `/?_bootload_` from
your browser and using the interface.

Once that's done, it's as easy as calling the `query` function:
````php
class MyCustomModel extends Model {
    public function getUsers() {
        $peopleNamedJohn = $this->query('SELECT * from users WHERE name = :name', [':name' => 'John'], TRUE);
        
        $pdoObject = $this->query('SELECT * from users WHERE name = :name', [':name' => 'Mark'], FALSE); 
        $peopleNamedMark = $pdoObject->fetchAll();
    }
}
````
So you can do it quickly or in several steps.

We can also obtain an indexed list, say:
````php
$marksInCities = $this->queryIndexed('SELECT `city`, `name`, `phone` FROM `users` WHERE `name` = :name', [':name' => 'Peter']);
````
will return something nicely indexed, like this:
````php
$marksInCities = [
    'Berlin' => [
        ...
        ...
        ...
    ],
    'Paris' => [
        ...
    ]
];
````
If you are sure the users are unique (say you are quering by email and you want the emails to be
the keys) then set the third parameter to `$unique = TRUE`.

#### Shortcuts
There are two nice shortcuts to manipulate databases: setter and getter. This helps perform simple
and quick inserts and searches with minimum code:
````php
$user37 = $this->getRow('users', 37); // by ID
$mark   = $this->getRow('users', 'Mark', 'name');

$this->updateRow('users', 'name', 37, 'Mark Jones');

````


That should be enough to get you started. Feel free to comment if you need help with something,
or a feature that can be added or improved.
<hr/>

Known issues:
Please use gulp to minify assets into the /public folder, or code in /public. gulp.js should be properly configured.

[1]:	https://github.com/nicojones/Ribosome
[2]:	https://rawgit.com/nicojones/Ribosome/master/docs/index.html
[3]:	https://rawgit.com/nicojones/Ribosome/master/docs/-API-documentation.zip