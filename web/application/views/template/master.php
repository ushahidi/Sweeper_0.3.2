<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo($title); ?></title>
        <link rel="shortcut icon" href="<?php echo url::base(); ?>favicon.ico" />
        <script type="text/javascript" language="javascript">
            var baseurl = "<?php echo(url::base()); ?>";
            var imageurl = baseurl + "/media/images/";
        </script>
        <?php echo(Html::script("media/js/jquery.js")); ?>
        <?php echo(Html::script("media/js/jquery.ui.js")); ?>
        <?php echo(Html::script("media/js/jquery.treeview.js")); ?>
        <?php echo(Html::script("media/js/jquery.validate.js")); ?>
        <?php echo(Html::script("media/js/jquery.cookie.js")); ?>
        <?php echo(Html::script("media/js/jquery.corner.js")); ?>
        <?php echo(Html::script("media/js/jquery.listreorder.js")); ?>
        <?php echo(Html::script("media/js/jquery.numeric.js")); ?>
        <?php echo(Html::script("media/js/jquery.jqtransform.js")); ?>
        <?php echo(Html::script("media/js/shadowbox.js")); ?>
        <?php echo(Html::script("media/js/site/framework.js")); ?>
        <?php echo(Html::script("media/js/site/functions.js")); ?>
        <?php echo(Html::script("media/js/css_browser_selector.js")); ?>

        <?php echo(Html::style("media/css/plugins/jquery.ui.css")); ?>
        <?php echo(Html::style("media/css/plugins/jquery.treeview.css")); ?>
        <?php echo(Html::style("media/css/plugins/shadowbox.css")); ?>
        <?php echo(Html::style("media/css/site/base.css")); ?>
        <?php echo(Html::style("themes/swift/style.css")); ?>

        <!--[if IE]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!--[if lt IE 8]>
            <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script>
            <?php echo(Html::style("themes/swift/ie7.css")); ?>
        <![endif]-->

        <!--[if lt IE 9]>
            <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
            <?php echo(Html::style("themes/swift/ie8.css")); ?>
        <![endif]-->

        <script type="text/javascript" language="javascript">
            //Init the Shadowbox plugin
            Shadowbox.init({skipSetup:true});
            $(document).ready(function(){
                $("div#nav-container").corner("5px");
                $("div#menu li a").corner("top 5px");
                $("div#body").corner("top 5px");
                $("div#footer").corner("bottom 5px");
                $("div.pagination").corner("5px");
            });
        </script>
    </head>
    <body>
        <div id="page">
            <div id="header">
                 <div class="container">
                    <div id="configuration">
                            <?php echo($admin); ?>
                    </div>
                    <?php echo($header); ?>
                    <div id="menu">
                            <?php echo($menu); ?>
                    </div>
                </div>
            </div>
            <div id="right-bar">
                <div class="container">
                    <?php echo($rightbar); ?>
                </div>
            </div>
            <div id="body">
                <div id="content">
                    <?php echo($content); ?>
                </div>
            </div>
            <div id="footer">
                <div class="container">
                    <div class="notch"><!--notch--></div>
                    <?php echo($footer); ?>
                </div>
            </div>
        </div>
        <!-- Begin SwiftRiver Tracking -->
        <script type="text/javascript">
            var pkBaseURL = (("https:" == document.location.protocol) ? "https://swift.ushahidi.com/stats/" : "http://swift.ushahidi.com/stats/");
            document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
            </script><script type="text/javascript">
            try {
            var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
            piwikTracker.trackPageView();
            piwikTracker.enableLinkTracking();
            } catch( err ) {}
        </script>
        <noscript><p><img src="http://swift.ushahidi.com/stats/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
    </body>
</html>
