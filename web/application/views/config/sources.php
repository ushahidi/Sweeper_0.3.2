<div id="sources">
    <div class="icon"></div>
    <div class="modal-header" style="padding-left:0 !important; margin-top:10px !important;">
        <h2 style="text-align:left !important; padding-top:10px !important;">Twitter Streaming</h2>
    </div>
    <p><a style="cursor:pointer" onclick="$('#twitter-streaming-config').slideDown()">Click here to show options</a></p>
    <div id="twitter-streaming-config" style="display:none; padding:20px !important;">
        <p style="font-size:80% !important; margin-left:20px !important; padding:5px !important;">
            There are some important things to note before you start using the Twitter Stream.<br /><br />
            1. You are most likely going to get LOTS of content!<br />
            2. All content from the Twitter Stream will not run through impulse turbines<br />
            3. It is not a good idea to run Twitter Stream along with other Twitter Searches
        </p>
        <p style="font-size:110% !important; margin-left:20px !important; padding:5px !important;">Twitter Username</p>
        <input type="text" id="TwitterUsername" value="<?php echo $TwitterUsername ?>" />
        <p style="font-size:110% !important; margin-left:20px !important; padding:5px !important;">Twitter Password</p>
        <input type="text" id="TwitterPsername" value="<?php echo $TwitterPassword ?>" />
        <p style="font-size:110% !important; margin-left:20px !important; padding:5px !important;">Search Keywords</p>
        <p>Seporate multiple keywords with a space</p>
        <input type="text" id="SearchTerms" value="<?php echo $SearchTerms ?>" />
        <button class="submit" type="submit" onclick="StartTwitterStreaming()">
            <span>Start Streaming</span>
        </button>
        <button class="submit" type="submit" onclick="StopTwitterStreaming()">
            <span>Stop Streaming</span>
        </button>

    </div>
    <br />
    <br />
    <br />
    <div class="modal-header" style="padding-left:0 !important; border-top:1px solid black !important; margin-top:10px !important;">
        <h2 style="text-align:left !important; padding-top:10px !important;">Other Sources</h2>
    </div>
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
                                                <div class="form-row clearfix checkbox">
                                                    <label for="trusted">Trusted source?:</label>
                                                    <input type="checkbox" name="trusted" />
                                                </div>
                                                <?php foreach($subType->configurationProperties as $key => $properties) : ?>
                                                    <?php if($subType->type == $key) : ?>
                                                        <?php foreach($properties as $property) : ?>
                                                            <?php if($property->type == "string") { ?>
                                                            <div class="form-row">
                                                                <label for="<?php echo(str_replace(" ", "", $property->name)); ?>"><?php echo($property->description); ?></label>
                                                                <input type="text" name="<?php echo(str_replace(" ", "", $property->name)); ?>" class="required" />
                                                            </div>
                                                            <?php }
                                                                else if($property->type == "multi_list") {
                                                                    $items = explode("|", $property->value);
                                                                    $current_item = 0;
                                                                    foreach($items as $item) {
                                                            ?>
                                                            <div class="form-row clearfix checkbox">
                                                                <label for="<?php echo($item); ?>"><?php echo($item); ?></label>
                                                                <input type="checkbox" name="<?php echo(str_replace(" ", "", $property->name)); ?>_<?php echo($current_item); ?>"/>
                                                            </div>
                                                            <?php
                                                                        $current_item ++;
                                                                    }
                                                                }
                                                            ?>
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
