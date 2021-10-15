<?php
//---------------------------------------------------------------------------
// CONFIG
// clé publique :
$pubkey = '';
// clé privée :
$privatekey = '';
// ligne 41 vous devez mettre la balise div qui vous est donner 
// elle ressemble a sa : <div data-dedipass="68cahfe2z56fse4f6ef46s4fe2b" data-dedipass-custom="">
//---------------------------------------------------------------------------
if (!prometheus::loggedin()) {
  die('Vous devez être connecté !');
}
$s = $_SESSION['uid'];
$code = isset($_POST['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['code']) : '';
if (empty($code)) {
} else {
  $dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?public_key=' . $pubkey . '&private_key=' . $privatekey . '&code=' . $code);
  $dedipass = json_decode($dedipass);
  if ($dedipass->status == 'success') {
    $virtual_currency = $dedipass->virtual_currency;
    $old_amt = $db->getOne("SELECT credits FROM players WHERE uid = {$s}");
    $add = $virtual_currency;
    $to_add = $old_amt + $add;
    credits::set($s, $to_add);
    prometheus::log('Validation du code dedipass : ' . $code . ' valeur : ' . $virtual_currency . ' pour ' . $to_add . ' crédits',  $s, 1);
    echo 'Le code est valide et vous êtes crédité de ' . $virtual_currency . ' Crédits';
  } else {
    // Le code est invalide 
    echo 'Le code ' . $code . ' est invalide';
  }
}
?>

<?php $message->display(); ?>
<div class="header" style="text-align: center;margin-left: auto;margin-right: auto;margin-bottom: 30px;">
  Dedipass
</div>

<div class="col-xs-12">
  <!-- mettre la div en dessous de cette ligne-->

  <!-- mettre la div au dessus de cette ligne-->
</div>

<script src="//api.dedipass.com/v1/pay.js"></script>
<script defer>
  document.querySelectorAll('body > div.wrap > div.content > div > div')[0].className = "";
</script>