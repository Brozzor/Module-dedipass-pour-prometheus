<?php
// lien de l'image pour dedipass :
$linkImgDedipass = 'https://pbs.twimg.com/profile_images/690505540766699520/tKPKX-N2_200x200.png';
//--------------------------------------------------------------------------------------------------

$disable_sorting = getSetting('disable_sorting', 'value2');

$store = new store('credits');

$sortArray = [
    "sortby" => "id",
    "search" => "%"
];

$store->setSortOptions($sortArray);

?>

<script type="text/javascript">
    $(document).ready(function (e) {
        $("#storeSidebar").on('submit', (function (e) {
            e.preventDefault();

            sideBar(this);

        }));

        function sideBar(form) {
            var sortby = $(form).find('#sortby').val();
            var search = $(form).find('input[type=text][name=search]').val();

            $('#credits').html('Loading ...');

            $.ajax({
                url: "inc/ajax/store.php",
                type: "POST",
                data: "action=get&type=credits&sortby=" + sortby + "&search=" + search,
                cache: false,
                success: function (data) {
                    $('#credits').html(data);
                }
            });
        }
    });
</script>

<div class="row">
    <div class="col-xs-12">
        <div class="header">
            <?= lang('select_credit'); ?>
        </div>
    </div>
</div>

<?php if ($disable_sorting == 0) { ?>
    <div class="darker-box">
        <?= $store->getSidebar(); ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xs-12">
        <?php if (tos::getLast() < getSetting('tos_lastedited', 'value3') && prometheus::loggedin()) { ?>
        <div class="info-box">
            <form method="POST" style="width: 40%;">
                <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                <h2>
                    <?= lang('tos'); ?>
                </h2>
                <?= lang('tos_edited'); ?>
                    <br>
                    <input type="submit" class="btn btn-success" value="<?= lang('tos_accept'); ?>" name="tos_submit" style="margin-top: 5px;">
            </form>
        </div>
        <?php } ?>

        <br>
        <?php $message->display(); ?>

        <?php
        $purchase = '<i class="fa fa-money"></i> ' . lang('purchase');

        $class = 'buy-btn';
        if (tos::getLast() < getSetting('tos_lastedited', 'value3')) {
            $class = 'buy-btn disabled';
            $purchase = lang('tos_must_accept');
        }

        if (!prometheus::loggedin()) {
            $class = 'buy-btn disabled';
            $purchase = lang('buy_sign_in');
        }
    ?>
         <div id="credits">
          <h2>Dedipass</h2>
          <div class="row">
            <div class="col-md-4">
                <div class="credit-box">
                    <div class="stat-box-header">
                            Achat Dedipass
                    </div>
                    <div class="credit-content">
                        <span> <img src="<?php echo $linkImgDedipass; ?>"></span>
                        <span></span>
                        <span>Achetez des crédits par téléphone.</span>
                    </div>
                    <a href="store.php?page=dedipass" class="<?= $class ?>"><?= $purchase ?></a>
                </div>
             </div>
            </div>
        </div>

        <h2>Paiement par carte</h2>
        <?php
            echo $store->display();
            ?>
    </div>

</div>
</div>