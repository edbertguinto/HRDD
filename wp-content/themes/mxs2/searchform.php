<?php
/**

 */
 ?>
<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
<div>
    <input type="text" name="s" id="s" value="<?php echo __('Search', 'MxS'); ?>" onfocus="if (this.value == '<?php echo __('Search', 'MxS'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo __('Search', 'MxS'); ?>';}" />
    <input type="submit" id="gs" value="&#8594;"/>
    </div>
</form>