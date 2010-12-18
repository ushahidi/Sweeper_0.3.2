<div id="sources">
    <div class="icon"></div>
    <?php $counter = 1; ?>
    <?php foreach($channels->channelTypes as $channelType) : ?>
        <h3><a href="javascript:ShowChannel('<?php echo($counter); ?>')"><?php echo($channelType->type); ?></a></h3>
        <div id="channel-type_<?php echo($counter); ?>" class="channel-container" style="display:none">
            <div class="tree">
                <ul>
                    <?php $innerCounter = 1; ?>
                    <?php foreach($channelType->subTypes as $subType) : ?>
                        <li>
                            <?php echo($subType->type); ?>
                            <ul>
                                <li id="node_<?php echo($counter); ?>_<?php echo($innerCounter); ?>" class="add-source">
                                    Add new <?php echo($subType->type); ?>
                                    <ul>
                                        <form id="form_<?php echo($counter); ?>_<?php echo($innerCounter); ?>">
                                            <fieldset>
                                                <input type="hidden" name="type" value="<?php echo($channelType->type); ?>" />
                                                <input type="hidden" name="subType" value="<?php echo($subType->type); ?>" />
                                                <input type="hidden" name="updatePeriod" value="1" />
                                                <div class="form-row">
                                                    <label for="name">The name of the <?php echo($channelType->type); ?> <?php echo($subType->type); ?>:</label>
                                                    <input type="text" name="name" class="required" />
                                                </div>
                                                <div class="form-row">
                                                    <label for="trusted">Trusted source?:</label>
                                                    <input type="checkbox" name="trusted" />
                                                </div>
                                                <?php foreach($subType->configurationProperties as $key => $properties) : ?>
                                                    <?php if($subType->type == $key) : ?>
                                                        <?php foreach($properties as $property) : ?>
                                                            <div class="form-row">
                                                                <label for="<?php echo(str_replace(" ", "", $property->name)); ?>"><?php echo($property->description); ?></label>
                                                                <input type="text" name="<?php echo(str_replace(" ", "", $property->name)); ?>" class="required" />
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <div class="form-row">
                                                    <button type="submit" onclick="SubmitForm('<?php echo($counter); ?>_<?php echo($innerCounter); ?>')" class="submit"><span>Add to this channel</span></button>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </ul>
                                </li>
                                <?php $innerInnerCounter = 1; ?>
                                <?php if(count($subType->sources) > 0 ) : ?>
                                    <?php foreach($subType->sources as $source) : ?>
                                        <li id="<?php echo($source->id); ?>" class="source-actions">
                                            <span>&nbsp;&rarr;<?php echo($source->name); ?>&nbsp;</span>
                                            <span><a href="javascript:DeleteChannel('<?php echo($source->id); ?>')">delete?</a></span>
                                            <span class="active" style="<?php echo($source->active ? "display:inline" : "display:none"); ?>" id="active_<?php echo($counter."_".$innerCounter."_".$innerInnerCounter); ?>"><a href="javascript:DeactivateSource('<?php echo($counter."_".$innerCounter."_".$innerInnerCounter); ?>', '<?php echo($source->id); ?>')">deactivate?</a></span>
                                            <span class="active" style="<?php echo(!$source->active ? "display:inline" : "display:none"); ?>" id="inactive_<?php echo($counter."_".$innerCounter."_".$innerInnerCounter); ?>"><a href="javascript:ActivateSource('<?php echo($counter."_".$innerCounter."_".$innerInnerCounter); ?>', '<?php echo($source->id); ?>')">activate?</a></span>
                                        </li>
                                    <?php $innerInnerCounter++; ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                        <li class="no-feeds">
                                            No feeds of this type yet.
                                        </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php $innerCounter++; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>
