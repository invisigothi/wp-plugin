<?require_once 'fanc.php';
$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
<p>
<label><?=$title;?></label>
<input type="text" name="grel_title" value="<?php echo esc_attr( $title ); ?>" />
</p>
