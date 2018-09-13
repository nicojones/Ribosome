## Ribosome

Lightweight PHP framework, with

* Router
* Authentication
* Assets
* File minimisation
* director.php for file generation
* more stuff ...
* ...

See the README online [here](https://github.com/nicojones/Ribosome)

See the full docs [here](https://rawgit.com/nicojones/Ribosome/master/docs/index.html) (generated from comments) or [download it](https://rawgit.com/nicojones/Ribosome/master/docs/-API-documentation.zip)

Fork me on Github, that would make me happy: [https://github.com/nicojones]

My personal websites can be found at [https://kupfer.es/]

## micro Tutorial
We are going to build a page that shows the current date and time, taking in parameters from the URL.

First of all, we will create a `DateController`. There are two easy ways to do this:
1. By accessing the Configuration Panel on `/?_bootload_` and entering the (default) password 1234.  
	From there we go to the last option, and we expand it.  
	  
	![](Screen%20Shot%202018-09-13%20at%2013.49.34.png)  
	  
	Here we can enter the name of the controller we want (in our case, `Date`, without the “Controller” part of the name) and we click on `Generate a Model as well`. We don’t need it for the example but you might need it in the future.
	  
	Copy the controller basic structure into `/src/controllers` and the model into `/src/models`.

2. From the terminal, on your project root, call
		php director.php generate:both Date 
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

	public function showDate() {
	    $this->show('date/index');
	}

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
Let’s do it! From the routing page `/src/config/routing.ini` we will change how it is processed by using wildcards:
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

<hr/>

Known issues:
File minimisation isn't working: Please configure gulp to minify assets into the /public folder, or code in it. Sorry