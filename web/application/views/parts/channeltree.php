<div id="channel-tree" style="display:none;">
    <h2>Channels</h2>
    <ul>
        <?php foreach($channels->channelTypes as $channelType) : ?>
            <li>
                <?php echo($channelType->type); ?>
                <ul>
                    <?php foreach($channelType->subTypes as $subType) : ?>
                        <li>
                            <?php echo($subType->type); ?>
                            <ul>
                                <li>
                                    <a href="javascript:ShowAddChannelModal('<?php echo($channelType->type); ?>', '<?php echo($subType->type); ?>');">Add new <?php echo($subType->type); ?>?</a>
                                </li>
                                <?php if(count($subType->sources) > 0 ) : ?>
                                    <?php foreach($subType->sources as $source) : ?>
                                        <li>
                                            <?php echo($source->name); ?><a href="javascript:DeleteChannel('<?php echo($source->id); ?>')"><?php echo(Html::image("media/images/button-markas-inaccurate.png")); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                        <li>
                                            No feeds of this type yet.
                                        </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
        <?php endforeach; ?>
    </ul>
</div>
<script type="text/javascript" language="javascript">
    $(doucument).ready(function(){
        TreeViewchannelTree();
    });
</script>
