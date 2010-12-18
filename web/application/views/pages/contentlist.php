<?php ?>
<script type="text/javascript" language="javascript">
    var nav_state = '<?php echo(isset($_SESSION["nav_state"]) ? $_SESSION["nav_state"] : $state); ?>'; <!-- changed -->
    var nav_minVeracity = <?php echo(isset($_SESSION["nav_minVeracity"]) ? $_SESSION["nav_minVeracity"] : "0"); ?>;
    var nav_maxVeracity = <?php echo(isset($_SESSION["nav_maxVeracity"]) ? $_SESSION["nav_maxVeracity"] : "100"); ?>;
    var nav_type = '<?php echo(isset($_SESSION["nav_type"]) ? $_SESSION["nav_type"] : "null"); ?>';
    var nav_subType = '<?php echo(isset($_SESSION["nav_subType"]) ? $_SESSION["nav_subType"] : "null"); ?>';
    var nav_source = '<?php echo(isset($_SESSION["nav_source"]) ? $_SESSION["nav_source"] : "null"); ?>';
    var nav_pageSize = <?php echo(isset($_SESSION["nav_pageSize"]) ? $_SESSION["nav_pageSize"] : "20"); ?>;
    var nav_pageStart = <?php echo(isset($_SESSION["nav_pageStart"]) ? $_SESSION["nav_pageStart"] : "0"); ?>;
    var nav_orderBy = '<?php echo(isset($_SESSION["nav_orderBy"]) ? $_SESSION["nav_orderBy"] : "null"); ?>';
    var nav_baseUrl = "<?php echo(url::base()); ?>";
    var render_firstload = true;
    $(document).ready(function(){
        setInterval("Update()", 10000);
        listController = new ListController(nav_baseUrl, "div#content-list ul", "div#nav-container");
        listController.NavigationStateChange(new NavigationState(nav_state, nav_minVeracity, nav_maxVeracity, nav_type, nav_subType, nav_source, nav_pageSize, nav_pageStart, nav_orderBy));
        $("#more_content a").attr("href", "javascript:MoreContent("+ nav_pageSize +")");
    });
</script>
<div id="content-list">
    <div class="pagination">
        <p class="total-count"></p>
    </div>
    <div class="container">
        <div id="no-results" style="display:none;">
            <h2>Sorry, nothing to show</h2>
        </div>
    </div>
    <ul>
    </ul>
</div>
