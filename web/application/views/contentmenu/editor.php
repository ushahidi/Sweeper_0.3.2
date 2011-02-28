<div id="list-option" class="clearfix">
    <ul>
        <li>
            <a class="<?php echo($dashboard_class); ?>" href="<?php echo(url::base()); ?>dashboard">
                Dashboard
            </a>
        </li>
        <li>
            <a class="<?php echo($new_content_class); ?>" href="<?php echo(url::base()); ?>contentlist/get/new_content">
                New content
            </a>
        </li>
        <li>
            <a class="<?php echo($accurate_class); ?>" href="<?php echo(url::base()); ?>contentlist/get/accurate">
                Accurate
            </a>
        </li>
        <li>
            <a class="<?php echo($inaccurate_class); ?>" href="<?php echo(url::base()); ?>contentlist/get/inaccurate">
                Inaccurate
            </a>
        </li>
        <li>
            <a class="<?php echo($chatter_class); ?>" href="<?php echo(url::base()); ?>contentlist/get/chatter">
                Crosstalk
            </a>
        </li>
        <li>
            <a class="<?php echo($irrelevant_class); ?>" href="<?php echo(url::base()); ?>contentlist/get/irrelevant">
                Irrelevant
            </a>
        </li>
    </ul>
</div>
