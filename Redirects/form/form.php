<?
$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
<p>
<label><?=$title;?></label>
<input type="text" name="DT_title" value="<?php echo esc_attr( $title ); ?>" />
</p>
