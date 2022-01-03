<?require_once 'fanc.php';
$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; 
$allPages = getAllPages();?>
<p>
<label><?=$title;?></label>
<input type="text" name="grel_title" value="<?php echo esc_attr( $title ); ?>" />
</p>
<optgroup label="Страницы">
                    <?php// foreach ($allPages as $page) : ?>
                        <option value="<?//php echo $page["id"]; ?>"><?//php echo $page["title"] ?></option>
                    <?php// endforeach; ?>
                </optgroup>