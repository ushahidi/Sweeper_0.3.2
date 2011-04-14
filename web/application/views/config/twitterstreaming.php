<div id="sources">
    <div class="icon"></div>
    <div id="twitter-streaming-config">
        <br />
        <br />
        <p>
            There are some important things to note before you start using the Twitter Stream.<br /><br />
            1. You are most likely going to get LOTS of content!<br /><br />
            2. All content from the Twitter Stream will not run through impulse turbines<br /><br />
            3. It is not a good idea to run Twitter Stream along with other Twitter Searches<br /><br />
            4. If you enter the wrong username and password here you will eventually get kicked off the twitter API!
        </p>
        <div id="twitter-streaming-form">
            <p>Twitter Username</p>
            <input type="text" id="TwitterUsername" value="<?php echo $TwitterUsername ?>" />
            <p>Twitter Password</p>
            <input type="text" id="TwitterPassword" value="<?php echo $TwitterPassword ?>" />
            <p>Search Keywords(separate multiple keywords with a space)</p>
            <input type="text" id="SearchTerms" value="<?php echo $SearchTerms ?>" />
            <br />
            <br />
            <br />
            <div id="activate-twitter-streaming" style="<?php echo ($isactive) ? 'display:none;' : ''; ?>">
                <h3>Twitter Streaming is not currently turned on.</h3>
                <button class="submit" type="submit" onclick="StartTwitterStreaming()"><span>Start Streaming ?</span></button>
            </div>


            <div id="deactivate-twitter-streaming" style="<?php echo ($isactive) ? '' : 'display:none;'; ?>">
                <h3>Twitter Streaming is currently running.</h3>
                <button class="submit cancel" type="submit" onclick="StopTwitterStreaming()"><span>Stop Streaming ?</span></button>
            </div>
        </div>
    </div>
</div>
