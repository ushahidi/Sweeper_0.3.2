/**
 *
 * @param baseUrl The base url for all ajax calls
 * @param subject The CSS Selector for the UL that will become the content list
 * @param navContainer The Css Selector for the Ul that will become the nav tree
 */
function ListController(baseUrl, subject, navContainer) {

    /**
     * The base url for all ajax requests
     * @var sting
     */
    this.baseUrl = baseUrl

    /**
     * The subject ul that should be turned into
     * the list.
     * @var CssSelector
     */
    this.subject = subject;

    /**
     * The ul that should be turned into the
     * navigation tree.
     * @var CssSelector
     */
    this.navContainer = navContainer;
    
    /**
     * Object array to hold the Async objects relating to 
     * ongoing ajax requests
     */
    this.currentRequests = new Object();

    /**
     * Object that contains the results from the call to the
     * core api.
     */
    this.results = new Object();
    
    /**
     * Function for seeting new navigation settings to the List
     * @param navigationState NavigationState
     */
    this.NavigationStateChange = function(navigationState){
        //if the navigation state has not changed, return
        if(this.navigationState != null && this.navigationState.Equals(navigationState))
            return;
        
        //Set the navigation state
        this.navigationState = navigationState;

        //Fire the before list changed event
        this.BeforListChanged();

        //Set list
        this.RenderList(true);

        //Fire the after list set event
        this.AfterListChanged();
    }

    /**
     * Renders the list from an api call
     */
    this.RenderList = function(rerenderNavigation) {
        //set the request Url
        var uri = this.baseUrl + "api/contentselection";

         //get the current array index
         this.currentRequests[uri] = $.post(
            uri,
            {
                state : this.navigationState.state,
                time : this.navigationState.time,
                minVeracity : this.navigationState.minVeracity,
                maxVeracity : this.navigationState.maxVeracity,
                type : this.navigationState.type,
                subType : this.navigationState.subType,
                source : this.navigationState.source,
                pageSize : this.navigationState.pageSize,
                pageStart : this.navigationState.pageStart,
                orderBy : this.navigationState.orderBy,
                tags : this.navigationState.tags
            },
            function(data)
            {
                listController.results = data;

                if(rerenderNavigation)
                    listController.RenderNavigation(data.navigation);

                 var totalCount = data.totalcount;
                 
                 if(totalCount < 1)
                 {
                     $("div#no-results").slideDown();

                     return;
                 }

                 $("div.pagination p.total-count").html(totalCount + " content items remaining");

                 for(var i=0; i<data.contentitems.length; i++) {

                    //Variable to check if the content item is in the list
                    var found = false;

                    //Check to see if the item is already there
                    var idsToSkip = listController.CurrentIds();
                    for(var x=0; x<idsToSkip.length; x++)
                        if(idsToSkip[x] == data.contentitems[i].id)
                            found = true;

                    //If it is then skip it
                    if(found == true)
                        continue;

                    var id = data.contentitems[i].id;
                    //Else, make the request to render it
                    listController.currentRequests[id] = $.post(
                           listController.baseUrl + "parts/content/render",
                           {content : data.contentitems[i]},
                           function(contentTemplate) {
                               $(listController.subject).append(
                                    "<li>" + contentTemplate + "</li>"
                               );
                           }
                    );
                }
            },
            "json"
        );
    }


    this.RenderNavigation = function(navigationTree) {
        if(navigationTree == null) {
            return;
        }

        $.get(
            listController.baseUrl + "parts/veracityslider/render",
            function(veracityTemplate) {
                $(listController.navContainer + " #veracity-slider-container").children().remove();
                $(listController.navContainer + " #veracity-slider-container").append(veracityTemplate);

                var min = (listController.navigationState.minVeracity != "null") ? listController.navigationState.minVeracity : 0;
                var max = (listController.navigationState.maxVeracity != "null") ? listController.navigationState.maxVeracity : 100;
                $("div#veracity-slider span#min").html(min);
                $("div#veracity-slider span#max").html(max);
                $("div#veracity-slider div#slider").slider({
                    range: true,
                    minValue: 0,
                    maxValue: 100,
                    values: [min,max],
                    step: 5,
                    slide: function(event, ui) {
                        $("div#veracity-slider span#min").html(ui.values[0]);
                        $("div#veracity-slider span#max").html(ui.values[1]);
                    },
                    stop : function(event, ui) {
                        var newNavState = listController.navigationState.Copy();
                        newNavState.minVeracity = ui.values[0];
                        newNavState.maxVeracity = ui.values[1];
                        listController.NavigationStateChange(newNavState);
                    }
                });

                if(listController.navigationState.tags != null && listController.navigationState.tags != "null" && listController.navigationState.tags.length != " ")
                {
                    $(listController.navContainer + " #selected-tags-container").children().remove();
                    $(listController.navContainer + " #selected-tags-container").append("<ul />");
                    var tags = listController.navigationState.tags.split("|");
                    for(var j = 0; j < tags.length; j++)
                    {
                        if(tags[j] == "")
                            continue;

                        $(listController.navContainer + " #selected-tags-container ul").append
                            (
                                "<li><a href='javascript:listController.RemoveNavigationTag(\"" + tags[j] + "\")'>x</a><span>" + tags[j] + "</span></li>"
                            )
                    }
                }
                else
                {
                    $(listController.navContainer + " #selected-tags-container").children().remove();
                    $(listController.navContainer + " #selected-tags-container").append("<p>No tags selected. Click tags below to filter view by selection.</p>");
                }

                if(navigationTree.Channels != null && navigationTree.Channels.facets != null)
                {
                    $(listController.navContainer + " #selected-channels-container").children().remove();
                    
                    if(navigationTree.Channels.selected)
                    {
                        $(listController.navContainer + " #selected-channels-container").append("<ul>");
                        $(listController.navContainer + " #selected-channels-container ul").append
                            (
                                "<li><a class='deselect' href=\"javascript:listController.DeselectFacet('type')\">x</a><span>" +
                                navigationTree.Channels.facets[0].name +
                                "</span></li>"
                            );
                    }
                    else
                    {
                        $(listController.navContainer + " #selected-channels-container").append("<ul />");

                        for(var x = 0; x < navigationTree.Channels.facets.length; x++)
                        {
                            if(navigationTree.Channels.facets[x].name == null || navigationTree.Channels.facets[x].name == "null")
                                continue;

                            $(listController.navContainer + " #selected-channels-container ul").append
                            (
                                "<li class='unselected'><a href='javascript:listController.SelectFacet(\"type\", \"" +
                                navigationTree.Channels.facets[x].id + 
                                "\")'><span>" + navigationTree.Channels.facets[x].name + "</span></a></li>"
                            );
                        }
                    }
                }
            }
        )

        



        
    }

    /**
     * The before list changed even
     */
    this.BeforListChanged = function() {
        //Stop all the current AJAX requests
        for(var request in listController.currentRequests) {
            listController.currentRequests[request].abort();
        }
        this.currentRequests = new Object();

        $("div#no-results").slideUp()
        $(this.subject).hide().children().remove();
        $(this.subject).show();
    }

    /**
     * The after list changed event
     */
    this.AfterListChanged = function() {

    }

    /**
     * Returns a list of all the current Ids in the list
     * @return string[] ids
     */
    this.CurrentIds = function() {
        var ids = new Array();
        var divs = $(this.subject + " div.content-item");
        for(var i=0; i<divs.length; i++)
            ids[ids.length] = $(divs[i]).attr("id");
        return ids;
    }

    /**
     * Updates the source score shown on the content list
     */
    this.UpdateSourceScores = function(sourceId, newScore) {
        $("h2."+sourceId).each(function(){
            $(this).html(newScore);
        });
    }

    /**
     * Given the id of a content item, this method will mark it
     * as accurate with the core and remove it from the list, It will also
     * fetch the next bit of content to preserve the list.
     * @param contentId string
     */
    this.MarkContentAsAccurate = function(contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
            $(this).remove();
        });
        $.getJSON(this.baseUrl + "api/contentcuration/markasaccurate/" + contentId, function(data) {
            listController.UpdateSourceScores(data.sourceId, data.sourceScore);
        });
        this.RenderList(false);
    }

    /**
     * Given the id of a content item, this method will mark it
     * as irrelevant with the core and remove it from the list, It will also
     * fetch the next bit of content to preserve the list.
     * @param contentId string
     */
    this.MarkContentAsIrrelevant = function(contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
            $(this).remove();
        });
        $.getJSON(this.baseUrl + "api/contentcuration/markasirrelevant/" + contentId, function(data) {
            listController.UpdateSourceScores(data.sourceId, data.sourceScore);
        });
        this.RenderList(false);
    }

    /**
     * Given the id of a content item, this method will mark it
     * as inaccurate with the core and remove it from the list, It will also
     * fetch the next bit of content to preserve the list.
     * @param contentId string
     */
    this.MarkContentAsInaccurate = function(contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
                $(this).remove();
        });
        $.getJSON(this.baseUrl + "api/contentcuration/markasinaccurate/" + contentId, function(data) {
            listController.UpdateSourceScores(data.sourceId, data.sourceScore);
        });
        this.RenderList(false);
    }

    /**
     * Given the id of a content item, this method will mark it
     * as crosstalk with the core and remove it from the list, It will also
     * fetch the next bit of content to preserve the list.
     * @param contentId string
     */
    this.MarkContentAsCrossTalk = function (contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
                $(this).remove();
        });
        $.getJSON(this.baseUrl + "api/contentcuration/markascrosstalk/" + contentId, function(data) {
            listController.UpdateSourceScores(data.sourceId, data.sourceScore);
        });
        this.RenderList(false);
    }

    this.DeselectFacet = function(facetgroup) {
        var newNavigationState = listController.navigationState.Copy();
        if(facetgroup == "type") {
            newNavigationState.type = "null";
        }
        else if (facetgroup == "subType") {
            newNavigationState.subType = "null";
        }
        else if (facetgroup == "source") {
            newNavigationState.source = "null";
        }
        listController.NavigationStateChange(newNavigationState);
    }

    this.SelectFacet = function(facetgroup, facet) {
        var newNavigationState = listController.navigationState.Copy();
        if(facetgroup == "type") {
            newNavigationState.type = facet;
        }
        else if (facetgroup == "subType") {
            newNavigationState.subType = facet;
        }
        else if (facetgroup == "source") {
            newNavigationState.source = facet;
        }
        listController.NavigationStateChange(newNavigationState);
    }

    /**
     * Function that adds a tag to the set of tags
     * currently in the navigation state
     */
    this.AddNavigationTag = function(tag) 
    {
        var newNavigationState = listController.navigationState.Copy();
        
        if(newNavigationState.tags == null || newNavigationState.tags == "null")
        {
            newNavigationState.tags = tag;
        }
        else if(newNavigationState.tags.indexOf(tag) == -1)
        {
            newNavigationState.tags += "|" + tag;
        }

        listController.NavigationStateChange(newNavigationState);
    }

    /**
     * Function that removes a given tag from the list of tags
     * currently making up the navigation state
     */
    this.RemoveNavigationTag = function(tag)
    {
        var newNavigationState = listController.navigationState.Copy();
        
        if(newNavigationState.tags == tag)
        {
            newNavigationState.tags = "";
        }
        else
        {
            newNavigationState.tags = newNavigationState.tags.replace(tag + "|", "");

            newNavigationState.tags = newNavigationState.tags.replace("|" + tag, "");
        }

        listController.NavigationStateChange(newNavigationState);
    }

    /**
     * Function that updates the list view to show content from the
     * current time.
     */
    this.UpdateListToCurrentTime = function()
    {
        var newNavigationState = listController.navigationState.Copy();

        newNavigationState.time = Math.round(new Date().getTime()/1000.0);

        listController.NavigationStateChange(newNavigationState);
    }

    /**
     * Always return true at the end of an
     * object constructor
     */
    return true;
}

function NavigationState(state, time, minVeracity, maxVeracity, type, subType, source, pageSize, pageStart, orderBy, tags) {
    this.state = state;
    this.time = time;
    this.minVeracity = minVeracity;
    this.maxVeracity = maxVeracity;
    this.type = type;
    this.subType = subType;
    this.source = source;
    this.pageSize = pageSize;
    this.pageStart = pageStart;
    this.orderBy = orderBy;
    this.tags = tags;

    this.Equals = function(navigationState) {
        if(navigationState.state != this.state)                 return false;
        if(navigationState.time != this.time)                   return false;
        if(navigationState.minVeracity != this.minVeracity)     return false;
        if(navigationState.type != this.type)                   return false;
        if(navigationState.subType != this.subType)             return false;
        if(navigationState.source != this.source)               return false;
        if(navigationState.pageSize != this.pageSize)           return false;
        if(navigationState.pageStart != this.pageStart)         return false;
        if(navigationState.orderBy != this.orderBy)             return false;
        if(navigationState.tags != this.tags)                   return false;
        return true;
    }

    this.Copy = function() {
        return new NavigationState(
            this.state,
            this.time,
            this.minVeracity,
            this.maxVeracity,
            this.type,
            this.subType,
            this.source,
            this.pageSize,
            this.pageStart,
            this.orderBy,
            this.tags);
    }

    /**
     * Always return true at the end of an
     * object constructor
     */
    return true;
}
