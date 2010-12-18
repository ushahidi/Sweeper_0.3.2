<div id="login">
    <div class="icon"></div>
    <div class="form">
        <div class="alert" style="display:none;">
            <ul></ul>
        </div>
        <div class="form-row">
            <label for="username">Your username:</label>
            <input type="text" name="username" />
        </div>
        <div class="form-row">
            <label for="password">Your password:</label>
            <input type="password" name="password" />
        </div>
        <div class="form-row">
            <button type="submit" onclick="ValidateAndTryLogin()" class="submit"><span>Log In</span></button>
            <button type="submit" onclick="Shadowbox.close()" class="cancel"><span>Cancel</span></button>
        </div>
    </div>
</div>