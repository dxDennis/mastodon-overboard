<?php
/**
 * @var $requestId integer
 * @var $requestInstance string
 */

use DecodoMastodonService\Component\Main\MainHelper;
use DecodoMastodonService\Controller\Document\Document;
use DecodoMastodonService\Controller\Request;

$requestInstance = Request::get('instance');

$helper = new MainHelper();
$instances = $helper->getStatsCollection();

$content = '<strong>can not find id ' . $requestInstance . ' in instance collection</strong>' .
    '<hr><pre>' . print_r($instances, 1) . '</pre>';

if (isset($instances[$requestInstance])) {
    $instance = $instances[$requestInstance];
    $content = $helper->parseServerHeader($instance, true);

    $content .= '<div class="card">
<div class="row">
<div class="col">
            <table class="table">
                <tr><th>accounts.max_featured_tags</th><td>' . $instance['data']['configuration']['accounts']['max_featured_tags'] . '</td></tr>
                <tr><th>statuses.max_characters</th><td>' . $instance['data']['configuration']['statuses']['max_characters'] . '</td></tr>
                <tr><th>statuses.max_media_attachments</th><td>' . $instance['data']['configuration']['statuses']['max_media_attachments'] . '</td></tr>
                <tr><th>statuses.characters_reserved_per_url</th><td>' . $instance['data']['configuration']['statuses']['characters_reserved_per_url'] . '</td></tr>
            </table>
</div>
<div class="col">' .
        '<div class="card-body text-end"><a href="/mastodon" class="btn btn-lg btn-secondary">zurück</a></div>
        </div>
</div>
</div>

        </div>';
};

$document = new Document();
$document->title = 'What`s Mastogoing - ' . $requestInstance;
$document->start();
?>
    <style>
        .banner-info-box {
            position: absolute;
            bottom: 0px;
            width: 100%;
            box-shadow: 0 -159px 76px rgba(0, 0, 0, 0.8) inset;
            color: #fff;
            text-shadow: 0 2px 1px rgba(0, 0, 0, 0.8);
        }

        .banner-info-box .card-content {

            color: #fff;
        }
    </style>
    <div class="container">
        <?php echo $content; ?>


    </div>
<?php
if (!empty(CURRENT_USER)) {
    echo Document::debugModal($instance);
}

$document->send();
