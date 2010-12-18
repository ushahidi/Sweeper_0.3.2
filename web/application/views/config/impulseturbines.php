<div id="turbines">
    <div class="icon"></div>
    <ul id="turbine-list">
        <?php for($i=0; $i<count($turbines); $i++) : ?>
            <?php $turbine = $turbines[$i]; ?>
            <li class="clearfix">
                <div class="name-container clearfix">
                    <p class="name"><a href="javascript:ShowTurbineDescription('<?php echo($i); ?>')"><?php echo($turbine->name); ?></a></p>
                </div>
                <div class="description" style="display:none;" id="turbinedesctiption_<?php echo($i); ?>">
                    <p><?php echo($turbine->description) ?></p>
                    <?php if(is_array($turbine->configurationProperties) && count($turbine->configurationProperties) > 0) : ?>
                        <div class="config-container clearfix" id="turbine_config_<?php echo($i); ?>">
                            <?php for($j=0; $j<count($turbine->configurationProperties); $j++) : ?>
                                <?php $config = $turbine->configurationProperties[$j]; ?>
                                <div class="config-property-container clearfix">
                                    <p class="name"><a href="javascript:ShowConfigurationDescription('<?php echo($i); ?>_<?php echo($j); ?>')"><?php echo($config->name); ?></a></p>
                                    <input name="<?php echo($config->name); ?>" type="text" onchange="SaveConfiguration('<?php echo($turbine->name); ?>', '<?php echo($i); ?>')" class="config-property-value <?php echo($config->type); ?>" id="config_<?php echo($i); ?>_<?php echo($j); ?>" value="<?php echo($config->value); ?>"/>
                                    <p class="description" style="display:none;" id="config_description_<?php echo($i); ?>_<?php echo($j); ?>"><?php echo($config->description); ?></p>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="active" style="<?php if(!$turbine->active) echo("display:none"); ?>" id="active_<?php echo($i); ?>">Currently active, <a href="javascript:DeactivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>', 'impulse')">deactivate?</a></p>
                <p class="active" style="<?php if($turbine->active) echo("display:none"); ?>" id="inactive_<?php echo($i); ?>">Not currently active, <a href="javascript:ActivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>', 'impulse')">activate?</a></p>
            </li>
        <?php endfor; ?>
    </ul>
</div>