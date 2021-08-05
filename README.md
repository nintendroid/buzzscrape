# buzzscrape
A Wordpress widget that retrieves your Podcast channels from your Buzzsprout account.

## Installation
* Please create a file called `Makefile.local` .
* Set the `plugins_dir` variable to specify the distribution directory containing the production files. Example:
```
plugins_dir=/Users/MyUser/Desktop
```

## Release Notes v0.9.0
* Initial release
* Known issue: The widget admin panel does not update the preview after force-refreshing the cache. Please refresh the browser after clicking the button. The end-user page is rendered properly.