<div id="dashboard">
    <div id="intro">
        <h1>Welcome to Sweeper - a Swiftriver app!</h1>
        <p>In 1972, a crack commando unit was sent to prison by a military court for a crime they didn't commit. They promptly escaped from a maximum security stockade to the Los Angeles underground. Today, still wanted by the government, they survive as soldiers of fortune. If you have a problem, if no-one else can help, and if you can find them, maybe you can hire the A-Team.</p>
    </div>
    <div class="row clearfix">
        <div class="column left">
            <script type="text/javascript" language="javascript">
                $(document).ready(function(){
                    $.getJSON(
                        "<?php echo url::base() ?>api/analytics/totalcontentbychannel",
                        function(data){

                            if(data.data.length == 0)
                                return;

                            $("#contentbychannelchart").slideDown
                            (
                                "fast",
                                function()
                                {
                                    var parsedData = new Object();
                                    for(var j = 0; j < data.data.length; j++)
                                    {
                                        var found = false;
                                        for(name in parsedData)
                                        {
                                            if(name == data.data[j].channelType)
                                            {
                                                parsedData[data.data[j].channelType] = parsedData[data.data[j].channelType] +  data.data[j].numberofcontentitems
                                                found = true;
                                            }
                                        }
                                        if(!found)
                                        {
                                            parsedData[data.data[j].channelType] = data.data[j].numberofcontentitems;
                                        }
                                    }

                                    var values = [];
                                    var names = [];
                                    var maxValue = 0;

                                    for(name in parsedData)
                                    {
                                        names.push(name);
                                        values.push(parsedData[name]);
                                        if(parsedData[name] > maxValue)
                                            maxValue = parsedData[name];
                                    }

                                    maxValue++;

                                    var contentbychannelchart = $.jqplot('contentbychannelchart', [values], {
                                        title:"Total Content Collected By Channel",
                                        seriesDefaults:{
                                            renderer:$.jqplot.BarRenderer,
                                            rendererOptions:{barPadding: 8, barMargin: 20}
                                        },
                                        axes:{
                                            xaxis:{
                                                renderer:$.jqplot.CategoryAxisRenderer,
                                                ticks:names
                                            },
                                            yaxis:{
                                                min:0,
                                                max:maxValue
                                            }
                                        }
                                    });                                
                                }
                            );
                        }
                    );
                });
            </script>
            <div id="contentbychannelchart" class="chart" style="height:250px;width:450px;display:none;"></div>
        </div>
        <div class="column right">
            <script type="text/javascript" language="javascript">
                $(document).ready(function(){
                    $.getJSON(
                        "<?php echo url::base() ?>api/analytics/contentbychannelovertime",
                        function(data){

                            if(data.data.length == 0)
                                return;

                            $("#totalcontentovertimechart").slideDown
                            (
                                "fast",
                                function()
                                {
                                    var parsedData = new Object();
                                    for(var j = 0; j < data.data.length; j++)
                                    {
                                        var found = false;
                                        for(date in parsedData)
                                        {
                                            if(date == data.data[j].dayOfTheYear)
                                            {
                                                parsedData[data.data[j].dayOfTheYear] = parsedData[data.data[j].dayOfTheYear] +  data.data[j].numberOfContentItems
                                                found = true;
                                            }
                                        }
                                        if(!found)
                                        {
                                            parsedData[data.data[j].dayOfTheYear] = data.data[j].numberOfContentItems;
                                        }
                                    }

                                    var values = [];
                                    for(date in parsedData)
                                    {
                                        var parts = date.split("-");
                                        values.push([parts[2] + "-" + parts[1] + "-" + parts[0], parseInt(parsedData[date])]);
                                    }
                                    var totalcontentovertimechart = $.jqplot('totalcontentovertimechart', [values], {
                                        title:"Total Content Collected Over Time",
                                        series:[{lineWidth:2, markerOptions:{style:'dimaond'}}],
                                        axes:
                                        {
                                            xaxis:
                                            {
                                                renderer:$.jqplot.DateAxisRenderer,
                                                tickOptions:{formatString:'%b %#d, %y'},
                                            },
                                            yaxis:
                                            {
                                                min:0
                                            }
                                        }
                                    });
                                }
                            );
                        }
                    );
                });
            </script>
            <div id="totalcontentovertimechart" class="chart" style="height:250px;width:450px;display:none;"></div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="column left">
            <script type="text/javascript" language="javascript">
                $(document).ready(function(){
                    $.getJSON(
                        "<?php echo url::base() ?>api/analytics/accumulatedcontentovertime",
                        function(data){

                            if(data.data.length == 0)
                                return;

                            $("#accumulatedcontentovertime").slideDown
                            (
                                "fast",
                                function()
                                {
                                    var parsedData = new Object();
                                    for(var j = 0; j < data.data.length; j++)
                                    {
                                        parsedData[data.data[j].date] = data.data[j].accumulatedtotal
                                    }
                                    var values = [];
                                    for(date in parsedData)
                                    {
                                        var parts = date.split("-");
                                        values.push([parts[2] + "-" + parts[1] + "-" + parts[0], parseInt(parsedData[date])]);
                                    }
                                    var totalcontentovertimechart = $.jqplot('accumulatedcontentovertime', [values], {
                                        title:"Accumulated Content Over Time",
                                        series:[{lineWidth:2, markerOptions:{style:'dimaond'}}],
                                        axes:
                                        {
                                            xaxis:
                                            {
                                                renderer:$.jqplot.DateAxisRenderer,
                                                tickOptions:{formatString:'%b %#d, %y'},
                                                tickInterval:'1 month'
                                            },
                                            yaxis:
                                            {
                                                min:0
                                            }
                                        }
                                    });
                                }
                            );
                        }
                    );
                });
            </script>
            <div id="accumulatedcontentovertime" class="chart" style="height:250px;width:450px;display:none;"></div>
        </div>
        <div class="column right">
            <script type="text/javascript" language="javascript">
                $(document).ready(function(){
                    $.getJSON(
                        "<?php echo url::base() ?>api/analytics/totalcontentbychannel",
                        function(data){

                            if(data.data.length == 0)
                                return;

                            $("#sourcepercentage").slideDown
                            (
                                "fast",
                                function()
                                {
                                    var parsedData = new Object();
                                    for(var j = 0; j < data.data.length; j++)
                                    {
                                        var found = false;
                                        for(name in parsedData)
                                        {
                                            if(name == data.data[j].channelType)
                                            {
                                                parsedData[data.data[j].channelType] = parsedData[data.data[j].channelType] +  data.data[j].numberofcontentitems
                                                found = true;
                                            }
                                        }
                                        if(!found)
                                        {
                                            parsedData[data.data[j].channelType] = data.data[j].numberofcontentitems;
                                        }
                                    }

                                    var values = [];
                                    for(name in parsedData)
                                        values.push([name, parseInt(parsedData[name])]);


                                    var contentbychannelchart = $.jqplot('sourcepercentage', [values], {
                                        title:"Percentage of Content from Sources",
                                        seriesDefaults:
                                        {
                                            renderer:$.jqplot.PieRenderer
                                        },
                                        legend:
                                        {
                                            show:true
                                        }
                                    });
                                }

                            );
                        }
                    );
                });
            </script>
            <div id="sourcepercentage" class="chart" style="height:250px;width:450px;display:none;"></div>
        </div>
    </div>
</div>