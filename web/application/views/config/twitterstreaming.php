<div id="twitter-streaming-config">
    <p>
    There are some important things to note before you start using the Twitter Stream API.<br /><br />
    1. You are likely to get <strong>LOTS</strong> of content!<br /><br />
    2. Content from the Stream API will not run through Impulse Turbines by default.<br /><br />
    3. It is not a good idea to run Stream API alongside other Twitter Searches<br /><br />
    4. If you enter the wrong username and password here you will eventually get kicked off by Twitter!
    </p>
    <div id="twitter-streaming-form">
    <p>Twitter Username</p>
    <input type="text" id="TwitterUsername" value="<?php echo $TwitterUsername ?>" />
    <p>Twitter Password</p>
    <p style="display:none; color:#990;" id="twitter-password-error">You have to enter your password each time, sorry</p>
    <input type="text" id="TwitterPassword" value="" />
    <p>Search Keywords*</p>
    <input type="text" id="SearchTerms" value="<?php echo $SearchTerms ?>" />
    <br />
        </div>
    <div id="activate-twitter-streaming" style="<?php echo ($isactive) ? 'display:none;' : ''; ?>">
        <br />
        <p>* Separate individual keywords with a space.</p>
        <p>Twitter Streaming is not currently turned on.<a href="#" onclick="StartTwitterStreaming()">Start?</a></p>
    </div>
    <div id="deactivate-twitter-streaming" style="<?php echo ($isactive) ? '' : 'display:none;'; ?>">
        <br />
        <p>* Separate individual keywords with a space.</p>
        <p>Twitter Streaming is currently running. <a href="#" onclick="StopTwitterStreaming()">Stop?</a></p>
    </div>
</div>