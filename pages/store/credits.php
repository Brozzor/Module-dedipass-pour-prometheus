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
    <div class="col">
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
    <div class="col">
        <?php if (tos::getLast() < getSetting('tos_lastedited', 'value3') && prometheus::loggedin()) { ?>
            <div class="info-box">
                <div class="row">
                    <div class="col-md-4">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                            <h2><?= lang('tos'); ?></h2>
                            <?= lang('tos_edited'); ?><br>
                            <input type="submit" class="btn btn-success" value="<?= lang('tos_accept'); ?>" name="tos_submit"
                                   style="margin-top: 5px;">
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>

        <br>
        <?php $message->display(); ?>

        <?php
            $purchase = '<svg class="svg-inline--fa fa-money-bill fa-w-20 fa-fw" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="money-bill" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M608 64H32C14.33 64 0 78.33 0 96v320c0 17.67 14.33 32 32 32h576c17.67 0 32-14.33 32-32V96c0-17.67-14.33-32-32-32zM48 400v-64c35.35 0 64 28.65 64 64H48zm0-224v-64h64c0 35.35-28.65 64-64 64zm272 176c-44.19 0-80-42.99-80-96 0-53.02 35.82-96 80-96s80 42.98 80 96c0 53.03-35.83 96-80 96zm272 48h-64c0-35.35 28.65-64 64-64v64zm0-224c-35.35 0-64-28.65-64-64h64v64z"></path></svg> ' . lang('purchase');

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
            <?php
            echo $store->display();
            ?>
        </div>
    </div>
</div>