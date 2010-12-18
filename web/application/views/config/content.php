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
            <dd><a target="_blank" href="<?php echo $_GET['contentlink']; ?>"><?php echo $_GET['contentlink']; ?></a></dd>
    </dl>
</div>