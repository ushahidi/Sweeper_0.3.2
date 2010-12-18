<div id="register-new-user">
    <div class="icon"></div>
    <div class="form">
        <div class="alert" style="display:none;">
            <ul></ul>
        </div>
        <div class="form-row">
            <label for="username">Their username:</label>
            <input type="text" name="username" />
        </div>
        <div class="form-row">
            <label for="password">Their password:</label>
            <input type="text" name="password" />
        </div>
        <div class="form-row">
            <label for="role">Their role:</label>
            <select name="role">
                <option value="sweeper" selected="selected">Sweeper</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-row">
            <button type="submit" onclick="ValidateAndTryRegister()" class="submit"><span>Add user</span></button>
            <button type="submit" onclick="Shadowbox.close()" class="cancel"><span>Cancel</span></button>
        </div>
    </div>
</div>