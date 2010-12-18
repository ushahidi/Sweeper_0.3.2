<script type="text/javascript" language="javascript">
    //define the type and sub type for this channel
    var type = '<?php echo($type); ?>';
    var subType = '<?php echo($subType); ?>';

    function AddChannel() {
        $("#add-channel-form").validate({
            submitHandler: function(form) {
                var name = $("form#add-channel-form input[name=name]").val();
                var updatePeriod = 1; //default
                var json =
                    '{"type":"'+type+'",'+
                     '"subType":"'+subType+'",'+
                     '"name":"'+name+'",'+
                     '"updatePeriod":'+updatePeriod+','+
                     '"parameters":';
                 $("form#add-channel-form input[type=text]").not("[name=name]").each(function(){
                     json += '{"'+this.name+'":"'+$(this).val()+'"},';
                 })
                 Shadowbox.close();
                 json = json.substring(0, json.length - 1) + '}';
                 $.post("<?php echo(url::base()); ?>api/channels/add",
                        { channel : json },
                        function(data) {
                            //TODO: do something if data.message != 'OK''
                             RepaintChannelTree();
                        },
                        'json'
                 );
            }
        });
    }
</script>
<div id="add-channel">
    <form action="" id="add-channel-form">
        <fieldset>
            <div class="form-row">
                <label for="name">The name of the feed:</label>
                <input type="text" name="name" class="required" />
            </div>
            <?php foreach($channel->configurationProperties as $key => $properties) : ?>
                <?php if($subType == $key) : ?>
                    <?php foreach($properties as $property) : ?>
                        <div class="form-row">
                            <label for="<?php echo(str_replace(" ", "", $property->name)); ?>"><?php echo($property->description); ?></label>
                            <input type="text" name="<?php echo(str_replace(" ", "", $property->name)); ?>" class="required" />
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="form-row">
                <input type="submit" value="Add to this channel" onclick="AddChannel()"/>
            </div>
        </fieldset>
    </form>
</div>