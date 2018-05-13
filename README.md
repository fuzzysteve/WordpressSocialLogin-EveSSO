WordpressSocialLogin-EveSSO
===========================

SSO provider for wordpress Social Login

https://wordpress.org/plugins/wordpress-social-login/

Provided with no warranty




Add the details in wsl.providers.php to the file with the same name in wp-content/plugins/wordpress-social-login/includes/settings

drop the EveSSO.php file into wp-content/plugins/wordpress-social-login/hybridauth/Hybrid/Providers

Then turn it on. you'll need to fill the details in from developers.eveonline.com where you'll have created a new application.

The callback url is like https://yoursite.com/wordpress/wp-content/plugins/wordpress-social-login/hybridauth/?hauth.done=EveSSO 
you'll find yours from clicking 'Where do I get this info?' after turning it on.

I'm not providing a picture, so you'll want to flip it over to using the text version, or find out where to drop in an appropriate picture.


If you want to restrict who can log into the site, put evesso-settings.php into your plugins directory, and go to plugins in wordpress and activate `Eve SSO Settings`. This will give you a new menu item, where you can enter comma seperated lists of who is allowed in. ID only. https://esi.evetech.net/ui/#/Search/get_search lets you search for IDs. then check them.
