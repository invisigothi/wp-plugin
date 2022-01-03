<?

// echo '<div class="wrap">';
        // echo '<p>page</p>';
        // echo '</div>';
?>
<div class="row">
    <div class="col-lg-6">
    <?if ($_GET['lang'] === 'RU' || !isset($_GET['lang'])):?>
        <h1>Настройки</h1>
        <?else:?>
                <h1>Settings</h1>  
        <?endif;?>
        <?if ($_GET['lang'] === 'RU' || !isset($_GET['lang'])){
                $currentLang = 'EN';
        }else{
                $currentLang = 'RU';
        }?>
        <a onclick="changeLang('lang','<?=$currentLang;?>'); return false;"><?=$currentLang;?></a>
</div>

<form action="options.php" method="POST">
			<?php
				settings_fields( 'option_group' );     
				do_settings_sections( 'main_settings_page' ); 
				submit_button();
			?>
		</form>
</div>
<script>
function changeLang(name, value)
{
        var l = window.location;

/* build params */
var params = {};        
var x = /(?:\??)([^=&?]+)=?([^&?]*)/g;        
var s = l.search;
for(var r = x.exec(s); r; r = x.exec(s))
{
    r[1] = decodeURIComponent(r[1]);
    if (!r[2]) r[2] = '%%';
    params[r[1]] = r[2];
}

/* set param */
params[name] = encodeURIComponent(value);

/* build search */
var search = [];
for(var i in params)
{
    var p = encodeURIComponent(i);
    var v = params[i];
    if (v != '%%') p += '=' + v;
    search.push(p);
}
search = search.join('&');

/* execute search */
l.search = search;
}
</script>