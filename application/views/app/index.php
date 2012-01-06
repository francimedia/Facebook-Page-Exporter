<h1>
    App Profiles 
</h1>

<?php if(isset($accounts_options)) :?>
    <?php echo form_open('app'); ?>
        <p>
            <label>Select Profile</label>
            <?php echo $accounts_options; ?>
        </p>
        <p>
            <input type="submit" value="Load Albums" name="submit_page" />
        </p>
    </form>
<?php endif; ?> 

<?php if(isset($albums_options)) :?>
    <?php echo form_open('app'); ?>
        <p>
            <label>Select Album</label>
            <?php echo $albums_options; ?>
        </p>
        <p>
            <input type="submit" value="Download Pictures" name="submit_page" />
        </p>
    </form>
<?php endif; ?> 


