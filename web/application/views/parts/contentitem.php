<div class="container">
    <div class="content-item" id="<?php echo($content->id); ?>">
        <div class="left-column">
            <div class="notch"></div>
            <div class="veracity">
                <div class="badge">
                    <?php $content->source->ratings = 3; ?>
                    <?php if ($content->source->ratings > 2) : ?>
                        <?php if ($content->source->score > 9) : ?>
                            <p class="trusted">Trusted</p>
                        <?php else : ?>
                            <p class="not_trusted">Not trusted</p>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="unknown">Unknown</p>
                    <?php endif; ?>
                </div>
                <h2 class="<?php echo($content->source->id); ?>"><?php echo($content->source->score == "null")? "-" : $content->source->score;?></h2>
            </div>
            <?php if($enableActions) : ?>
                <ol class="actions">
                    <li class="accurate"><a href="javascript:listController.MarkContentAsAccurate('<?php echo($content->id); ?>')" title="Mark this content as acurate"><span>Mark as accurate</span></a></li>
                    <li class="inaccurate"><a href="javascript:listController.MarkContentAsInaccurate('<?php echo($content->id); ?>')" title="Mark this content as inaccurate"><span>Mark as inaccurate</span></a></li>
                    <li class="crosstalk"><a href="javascript:listController.MarkContentAsCrossTalk('<?php echo($content->id); ?>')" title="Mark this content as cross talk"><span>Mark as cross talk</span></a></li>
                    <li class="flag"><a href="javascript:listController.MarkContentAsIrrelevant('<?php echo($content->id); ?>')" title="Mark this content as Irrelevant"><span>Mark as irrelevant</span></a></li>
                </ol>
            <?php endif; ?>
        </div>


        <div class="right-column">
            <p class="source"><a href="javascript:Content('<?php echo($content->source->name); ?>', '<?php echo($content->source->type); ?>', '<?php echo($content->source->ratings); ?>', '<?php echo($content->source->score); ?>', '<?php echo($content->source->link); ?>', '<?php echo($content->link); ?>')" class="<?php echo strtolower($content->source->type); ?>" title="View source details"><!--<?php echo strtolower($content->source->name); ?>--></a><!--source--></p>
            <div class="meta">
                <p class="date"><?php echo(date('D d M Y H:i (P\G\M\T)', $content->date)); ?></p>
            </div>
            <div class="languages">
                <?php for($i=0; $i<count($content->text); $i++): ?>
                    <div class="language <?php echo($content->text[$i]->languageCode); ?> <?php if($i>0) { echo ('more'); } ?>">
                        <?php
                            /* Add links to string */
                            $title = html_entity_decode($content->text[$i]->title);
                            $title = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a target=\"_blank\" href=\"\\2\">\\2</a>", $title);
                            $title = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a target=\"_blank\" href=\"http://\\2\">\\2</a>", $title);
                            /* Add link to twitter mention */
                            $title = preg_replace("/@(\w+)/", "<a target=\"_blank\" href=\"http://www.twitter.com/\\1\">@\\1</a>", $title);
                        ?>
                        <h2 class="title">
                            <?php echo($title); ?>
                        </h2>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="tags">
                <?php foreach($content->tags as $type => $tags) : ?>
                    <?php if(is_array($tags) && count($tags) > 0) : ?>
                        <?php if(count($tags) > 0) : ?>
                            <ol class="tag-list">
                                <!--<li><strong><?php echo($type); ?>:</strong></li>-->
                                <?php foreach($tags as $key => $tag) : ?>
                                    <li id="<?php echo($content->id); ?>-<?php echo(str_replace(" ", "", strtolower($tag))); ?>">
                                        <a class="tag-remove" href="JavaScript:RemoveContentTag('<?php echo($content->id); ?>', '<?php echo($type); ?>', '<?php echo($tag); ?>', '<?php echo($content->id); ?>-<?php echo(str_replace(" ", "", strtolower($tag))); ?>');" title="Remove this tag"><span>x</span></a>
                                        <a class="tag-select" href="JavaScript:listController.AddNavigationTag('<?php echo strtolower($tag); ?>')"><?php echo strtolower($tag); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
                <?php if(property_exists($content, "extensions") ) : ?>
                    <div class="extensions">
                        <?php if(property_exists($content->extensions, "tagClusteringScores")) : ?>
                            <?php if(property_exists($content->extensions->tagClusteringScores, "AccurateContent")) : ?>
                                <ol class="clustering clearfix">
                                    <li class="title">clustering scores for <strong>accurate</strong> content:</li>
                                    <?php foreach($content->extensions->tagClusteringScores->AccurateContent as $key => $value) : ?>
                                    <li class="<?php echo(strtolower(str_replace(' ', '', $key))); ?> clearfix">
                                            <div class="image"></div>
                                            <div class="text">
                                                <span><?php echo($key); ?>:</span>
                                                &nbsp;<?php echo(round($value * 100)); ?>%
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php endif; ?>
                            <?php if(property_exists($content->extensions->tagClusteringScores, "AllContent")) : ?>
                                <ol class="clustering clearfix">
                                    <li class="title">clustering scores for <strong>all</strong> content:</li>
                                    <?php foreach($content->extensions->tagClusteringScores->AllContent as $key => $value) : ?>
                                    <li class="<?php echo(strtolower(str_replace(' ', '', $key))); ?> clearfix">
                                            <div class="image"></div>
                                            <div class="text">
                                                <span><?php echo($key); ?>:</span>
                                                &nbsp;<?php echo(round($value * 100)); ?>%
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
        </div>
    </div>
</div>