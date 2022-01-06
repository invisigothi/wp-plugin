<?
if ($_GET['lang'] === 'RU' || !isset($_GET['lang']))
{
$lang = 'RU';
}else{
$lang = 'EN';
}
?>
    <div class="settings__header">
        <h1><?=$mess[$lang]['settings_title']?></h1>
        <?if ($_GET['lang'] === 'RU' || !isset($_GET['lang'])){
                $currentLang = 'EN';
        }else{
                $currentLang = 'RU';
        }?>
        <h2><?=$mess[$lang]['lang'];?></h2>
        <a class="lang__changer" onclick="changeLang('lang','<?=$currentLang;?>'); return false;"><?=$currentLang;?></a>
</div>

<form class="settings__form" action="options.php" method="POST">
			<?php
				settings_fields( 'option_group' );     
				do_settings_sections( 'main_settings_page' ); 
				submit_button($mess[$lang]['btn_save_text']);
			?>
		</form>
