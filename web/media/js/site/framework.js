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
        var uri = this.baseUrl +
                  "api/contentselection/get/" +
                  this.navigationState.state + "/" +
                  this.navigationState.minVeracity + "/" +
                  this.navigationState.maxVeracity + "/" +
                  this.navigationState.type + "/" +
                  this.navigationState.subType + "/" +
                  this.navigationState.source + "/" +
                  this.navigationState.pageSize + "/" +
                  this.navigationState.pageStart + "/" +
                  this.navigationState.orderBy;

         //get the current array index
         this.currentRequests[uri] = $.getJSON(uri, function(data){

            if(rerenderNavigation) {
                listController.RenderNavigation(data.navigation);
            }

             var totalCount = data.totalcount;
             if(totalCount < 1) {
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
         });
    }


    this.RenderNavigation = function(navigationTree) {
        if(navigationTree == null) {
            return;
        }
        
        $.get(
            listController.baseUrl + "parts/veracityslider/render",
            function(veracityTemplate) {
                $(listController.navContainer).children().remove();
                $(listController.navContainer).append(veracityTemplate);

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
     * Always return true at the end of an
     * object constructor
     */
    return true;
}

function NavigationState(state, minVeracity, maxVeracity, type, subType, source, pageSize, pageStart, orderBy) {
    this.state = state;
    this.minVeracity = minVeracity;
    this.maxVeracity = maxVeracity;
    this.type = type;
    this.subType = subType;
    this.source = source;
    this.pageSize = pageSize;
    this.pageStart = pageStart;
    this.orderBy = orderBy;

    this.Equals = function(navigationState) {
        if(navigationState.state != this.state)                 return false;
        if(navigationState.minVeracity != this.minVeracity)     return false;
        if(navigationState.type != this.type)                   return false;
        if(navigationState.subType != this.subType)             return false;
        if(navigationState.source != this.source)               return false;
        if(navigationState.pageSize != this.pageSize)           return false;
        if(navigationState.pageStart != this.pageStart)         return false;
        if(navigationState.orderBy != this.orderBy)             return false;
        return true;
    }

    this.Copy = function() {
        return new NavigationState(
            this.state,
            this.minVeracity,
            this.maxVeracity,
            this.type,
            this.subType,
            this.source,
            this.pageSize,
            this.pageStart,
            this.orderBy);
    }

    /**
     * Always return true at the end of an
     * object constructor
     */
    return true;
}
