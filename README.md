# buzzscrape
A Wordpress widget that retrieves your Podcast channels from your Buzzsprout account.

## Installation
* Please create a file called `Makefile.local` .
* Inside, set the `plugins_dir` variable to specify the distribution directory to contain the production files. Example:
```
plugins_dir=/Users/MyUser/Desktop
```

## Release Notes v0.9.0
* Initial release

## TODO
* Fix an issue where the widget admin panel does not update the preview after force-refreshing the cache. Please refresh the browser after clicking the button. The end-user page still renders properly.
* Create a global setting to enable/disable CRON tasks. Currently, a refresh is performed for all widgets. It is possible to perform refresh on a per-widget basis depending on its options.
* Add option to add custom services with an icon
