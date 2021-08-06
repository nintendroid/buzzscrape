# buzzscrape
A Wordpress widget that retrieves your Podcast channels from your Buzzsprout account.

## Installation
* Please create a file called `Makefile.local`.
* Inside, set the `plugins_dir` variable to specify the distribution directory to contain the production files. Example:
```
plugins_dir=/Users/MyUser/Desktop
```
* Run `make install`
* Create a zip package or manually move the files into the plugins directory.
* Find the Buzzsprout Scraper plugin and activate it.

## Usage
Enter the account ID in the single field. Please be sure to hit the Update button, before clicking on the Refresh button.

## Release Notes v0.9.0
* Initial release

## TODO
* Add a global refresh button to the plugin global settings panel
* Create a global setting to enable/disable CRON tasks. Currently, a refresh is performed for all widgets. It is possible to perform refresh on a per-widget basis depending on its options.
* Add option to add custom services with an icon
