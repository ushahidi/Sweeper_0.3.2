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
                            {
                                $("#contentbychannelchart").append("<div class='no-data'><h1>Sorry, there is no data</h1></div>");
                                return;
                            }

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


                });
            </script>
            <div id="contentbychannelchart" class="chart" style="height:300px;width:450px;"></div>
        </div>
        <div class="column right">
            <script type="text/javascript" language="javascript">
                $(document).ready(function(){
                    $.getJSON(
                        "<?php echo url::base() ?>api/analytics/contentbychannelovertime",
                        function(data){

                            if(data.data.length == 0)
                            {
                                $("#totalcontentovertimechart").append("<div class='no-data'><h1>Sorry, there is no data</h1></div>");
                                return;
                            }

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
                                var diff = Math.ceil((new Date() - new Date(parts[2], parts[1], parts[0])) / 86400000);
                                values.push([diff, parseInt(parsedData[date])]);
                            }
                            var totalcontentovertimechart = $.jqplot('totalcontentovertimechart', [values], {
                                title:"Total Content Collected Over Time",
                                series:[{lineWidth:2, markerOptions:{style:'dimaond'}}],
                                axes:{xaxis:{label:"Number of days ago"}}
                            });
                        }
                    );


                });
            </script>
            <div id="totalcontentovertimechart" class="chart" style="height:300px;width:450px;"></div>
        </div>
    </div>
</div>