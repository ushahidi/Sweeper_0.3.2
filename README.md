SWEEPER v0.2.0
==============
 
Sweeper is an intelligent media curation tool with filters for managing real-time feeds of information.  You can find out more at - http://swiftly.org

Find us on Github - [http://github.com/ushahidi/Swiftriver](http://github.com/ushahidi/Swiftriver)

**NOTE**: This is a public beta software release for evaluation and feedback purposes only. Please do NOT use this in critical scenarios without further consultation. Contact jon [at] ushahidi.com with additional questions or feedback.

DEVELOPER NOTES
---------------

If you are a developer interested in contributing to sweeper then there are a few things to note
before you jump in and start coding.

The **master** branch will always be the latest stable release of the app so unless you
like living life dangerously this is the best place to start:

    $ git clone git://github.com/ushahidi/Sweeper.git

With the app code checked out you still need to perform the following steps before you can
start deving locally:

    $ cd Sweeper
    $ git submodule init
    $ git submodule update

Why do we do this? Well, Sweeper is just one app that runs on the [Swiftriver](http://github.com/ushahidi/Swiftriver)
framework. To allow this to all happen, the framework has its own repository and is brought into
this project using the [**git-submodule**](http://chrisjean.com/2009/04/20/git-submodules-adding-using-removing-and-updating/)
facility.

Once you have done this, you will have all the code required to run and change Sweeper!


SYSTEM REQUIREMENTS
-------------------

* Apache 1.3 or greater
* PHP 5.3 or higher
* PHP Pear Extensions
* MySQL 4.0 or higher
	
Further details on this release can be found at - [http://wiki.ushahidi.com](http://wiki.ushahidi.com)

SUPPORTED BROWSERS
------------------
* FireFox
* Safari
* IE7+

DOCUMENTATION
-------------

Documentation - [http://wiki.ushahidi.com](http://wiki.ushahidi.com)

Installation - [http://wiki.ushahidi.com/doku.php?id=install_s](http://wiki.ushahidi.com/doku.php?id=install_s)