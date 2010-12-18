/* ========= VARIABLES ========= */
var debug = true; //Set this to true to prevent content from loading



// Turbines
function SaveConfiguration(name, number, turbineType) {
    var postData = new Object();
    var inputs = $("div#turbine_config_" + number + " input");
    for(var i=0; i<inputs.length; i++) {
        var input = inputs[i];
        postData[$(input).attr("name")] = $(input).val();
    }
    $.post(
        baseurl + "config/" + turbineType + "turbines/save",
        { "name" : name, "data" : postData },
        function(data){},
        'json'
    );
}
function ShowTurbineDescription(number) {
    $("#turbinedesctiption_" + number).show("slow");
    setTimeout("HideTurbineDescription("+number+")", 10000);
}
function HideTurbineDescription(number) {
    $("#turbinedesctiption_" + number).hide("slow");
}
function ShowConfigurationDescription(number) {
    $("#config_description_" + number).show("slow");
    setTimeout("HideConfigurationDescription('"+number+"')", 5000);
}
function HideConfigurationDescription(number) {
    $("#config_description_" + number).hide("slow");
}
function ActivateTurbine(number, name, turbineType) {
    $("p#inactive_" + number).hide("fast");
    $("p#active_" + number).show("fast");
    $.post(
        baseurl + "config/" + turbineType + "turbines/activate",
        { name: name },
        function(data){},
        'json'
    );
}
function DeactivateTurbine(number, name, turbineType) {
    $("p#active_" + number).hide("fast");
    $("p#inactive_" + number).show("fast");
    $.post(
        baseurl + "config/" + turbineType + "turbines/deactivate",
        { name: name },
        function(data){},
        'json'
    );
}

//login.php
function ValidateAndTryLogin() {
    $("div#login div.alert").slideUp("slow");
    $("div#login div.alert ul").children().remove();

    var username = $("div#login input[name=username]").val();
    var password = $("div#login input[name=password]").val();

    if(username == "" || password == "") {
        $("div#login div.alert ul").append("<li>You have to enter both a username and password</li>");
        $("div#login div.alert").slideDown();
        return;
    }

    $.post(
        baseurl + "config/user/login",
        { "username" : username, "password" : password },
        function(data) {
            if(data.result) {
                Shadowbox.close();
                window.location = baseurl;
                return;
            }
            $("div#login div.alert ul").append("<li>The username and password you entered didn't match any in the data base, please try again.</li>");
            $("div#login div.alert").slideDown();
        },
        "json"
    );
}

//Register new user
function ValidateAndTryRegister() {
    $("div#register-new-user div.alert").slideUp("slow");
    $("div#register-new-user div.alert ul").children().remove();

    var username = $("div#register-new-user input[name=username]").val();
    var password = $("div#register-new-user input[name=password]").val();
    var role = $("div#register-new-user select[name=role] option:selected").val();

    if(username == "" || password == "") {
        $("div#register-new-user div.alert ul").append("<li>You have to enter both a username and password</li>");
        $("div#register-new-user div.alert").slideDown();
        return;
    }

    $.post(
        baseurl + "config/user/register",
        { "username" : username, "password" : password, "role" :role },
        function(data) {
            Shadowbox.close();
            return;
        },
        "json"
    );
}

//Sources
function ShowChannel(counter) {
    $("div.channel-container").each(function() {
        $(this).slideUp("slow");
    });
    $("div#channel-type_" + counter).slideDown("slow");
}
function DeleteChannel(id) {
    $.getJSON(baseurl + "api/channels/deletechannel/"+id, function(data){
        $("div#sources li#" + id).remove();
    });
}
function SubmitForm(id) {
    var formId = "#form_" + id;
    $(formId).validate({
        submitHandler: function(form) {
            var type = $(formId + " input[name=type]").val();
            var subType = $(formId + " input[name=subType]").val();
            var updatePeriod = $(formId + " input[name=updatePeriod]").val();
            var name = $(formId + " input[name=name]").val();
            var trusted = $(formId + " input[name=trusted]").is(':checked');
            var json =
                '{"type":"'+type+'",'+
                 '"subType":"'+subType+'",'+
                 '"name":"'+name+'",'+
                 '"trusted":'+trusted+','+
                 '"updatePeriod":'+updatePeriod+','+
                 '"parameters":{';
             $(formId + " input[type=text]").not("[name=name]").each(function(){
                 json += '"'+this.name+'":"'+$(this).val()+'",';
             })
             json = json.substring(0, json.length - 1) + '}}';
             $(formId + " input[type=text]").each(function(){
                this.value = "";
             });
             $("#node_" + id + " .hitarea").eq(0).click();
             $.post(baseurl + "api/channels/add",
                    { channel : json },
                    function(data) {
                        $.getJSON(baseurl + "api/channels/getall", function(innerData){
                            var channelId = "";
                            var channels = innerData.data.channels;
                            for(var i=0; i<channels.length; i++) {
                                if(channels[i].name == name) {
                                    channelId = channels[i].id;
                                }
                            }
                            var image = imageurl + '/button-markas-inaccurate.png';
                            var branch = $("<li id='" + channelId + "'>" + name + "<a href=\"javascript:DeleteChannel('" + channelId + "')\"><img src='" + image + "' /></a></li>");
                            $("#node_" + id).parent().append(branch);
                            $("#node_" + id).parent().treeview({
                                add: branch
                            });
                        });
                    },
                    'json'
             );
        }
    });
}
function ActivateSource(number, id) {
    $("span#inactive_" + number).css("display", "none");
    $("span#active_" + number).css("display", "inline");
    $.post(
        baseurl + "config/sources/activate",
        { id: id },
        function(data){},
        'json'
    );
}
function DeactivateSource(number, id) {
    $("span#active_" + number).css("display", "none");
    $("span#inactive_" + number).css("display", "inline");
    $.post(
        baseurl + "config/sources/deactivate",
        { id: id },
        function(data){},
        'json'
    );
}


// CONTENT LIST
function Update() {
    var url = baseurl.replace("/web", "");
    $.post(url + "core/api/channelservices/runnextchannel.php",{ key : "swiftriver_dev" });
}
function ShowAddChannelModal(type, subType) {
    $.get(baseurl + "parts/addchannel/" + type + "/" + subType, function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 450,
            width : 500
        });
    });
}
function RepaintChannelTree() {
    $.get(baseurl + "parts/channeltree/render", function(data){
        var treeContainer = $("div#channel-tree-container");
        var child = treeContainer.children("div#channel-tree");
        $(child).remove();
        $(treeContainer).prepend(data);
        TreeViewChannelTree();
    });
}
function ConfigureTheme() {
    $.get(baseurl + "config/themes", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 450,
            width : 500
        });
    });
}
function ConfigureImpulseTurbines() {
    $.get(baseurl + "config/impulseturbines", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 450,
            width : 500
        });
    });
}
function ConfigureReactorTurbines() {
    $.get(baseurl + "config/reactorturbines", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 450,
            width : 500
        });
    });
}
function LogIn() {
    $.get(baseurl + "config/loginandregister/login", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 350,
            width : 400
        });
    });
}
function RegisterNewUser() {
    $.get(baseurl + "config/loginandregister/register", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 350,
            width : 400
        });
    });
}
function LogOut() {
    window.location = baseurl + "config/user/logout";
}
function ConfigureSources() {
    $.get(baseurl + "config/sources", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 450,
            width : 500,
            options : {
                onFinish : function() {
                    $("div.tree ul").treeview({
                        animated: "fast",
                        collapsed: true,
                        unique: true
                    });
                }
            }
        });
    });
}
function FilterByType(type) {
    nav_type = type;
    nav_subType = "null";
    nav_source = "null";
    ClearList();
    render_firstload = true;
    AddContent(new Array());
}
function FilterBySubType(subType) {
    nav_type = "null";
    nav_subType = subType;
    nav_source = "null";
    ClearList();
    render_firstload = true;
    AddContent(new Array());
}
function FilterBySource(source) {
    nav_type = "null";
    nav_subType = "null";
    nav_source = source;
    ClearList();
    render_firstload = true;
    AddContent(new Array());
}
function ClearList() {
    $("div#content-list ul li").each(function(){
        $(this).remove();
    })
}
function MoreContent(pagesize) {
    pagesize = pagesize + 10;
    $("#more_content a").attr("href", "javascript:MoreContent("+ pagesize + ")");
    listController.NavigationStateChange(new NavigationState(nav_state, nav_minVeracity, nav_maxVeracity, nav_type, nav_subType, nav_source, pagesize, nav_pageStart, nav_orderBy));
}
function ConfigureFacetGroup() {
    $.get(baseurl + "config/facetgroup", function(data) {
        Shadowbox.open({
            content : data,
            player : "html",
            height : 450,
            width : 400
        });
    });
}
function Rating(type, id) {
    $.get(
        baseurl + "config/rating/rating",
        { type: type, id: id },
        function(data) {
            Shadowbox.open({
                content : data,
                player : "html",
                height : 350,
                width : 400
                });
            }
    );
}
function Content(name, type, ratings, score, sourcelink, contentlink) {
    $.get(
        baseurl + "config/content/content",
        { name: name, type: type, ratings: ratings, score: score, sourcelink: sourcelink, contentlink: contentlink },
        function(data) {
            Shadowbox.open({
                content : data,
                player : "html",
                height : 350,
                width : 400
            });
        }
    );
}
function RemoveContentTag(contentId, tagType, tagText, liId)
{
    $("#"+liId).hide();
    $.post(
        baseurl + "api/contentcuration/removetag/",
        { contentId: contentId, tagType: tagType, tagText: tagText},
        function(data){}
    )
}
