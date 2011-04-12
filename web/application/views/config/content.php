<div id="contentsource">
    <div class="icon icon_<?php echo strtolower($_GET['type']); ?>"></div>
    <dl>
        <dt>Source:</dt>
            <dd><?php echo $_GET['name']; ?></dd>
        <dt>Channel type:</dt>
            <dd><?php echo strtolower($_GET['type']); ?></dd>
        <dt>Source veracity:</dt>
            <dd><?php echo($_GET['score'] == "null" ? "Not yet rated" : $_GET['score']); ?></dd>
        <dt>Link:</dt>
            <dd><a href="<?php echo $_GET['contentlink']; ?>" target="_blank"><?php echo $_GET['contentlink']; ?></a></dd>
    </dl>
</div>