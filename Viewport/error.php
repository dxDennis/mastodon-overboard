<?php
use DecodoMastodonService\Controller\Document\Document;

global $routingData;

$document = new Document();
$document->addJs('/mastodon/assets/js/main.js')
    ->setTopNavigation('')
    ->start($_ENV['APP_NAME']);

?>
    <div class="container">
    <div class="card card-body">
        <div class="row pr-lg-5">
            <div class="col-md-7 mb-4">
                <div class="view my-5">
                    <video playsinline="" autoplay="" muted="" loop="" width="100%">
                        <source src="/mastodon/assets/img/grape-pack-illustration-5-transcode.mp4" type="video/mp4">
                    </video>
                </div>
            </div>
            <div class="col-md-5 d-flex align-items-center">
                <div class="alert alert-warning">
                    <h2>Can not find <strong><?php echo $routingData['viewport']; ?></strong></h2>
                    <hr>
                    <a href="/mastodon/">back</a>
                    <?php
                    if (!empty(CURRENT_USER)) {
                        echo '<pre>' . print_r(['$routingData' => $routingData], 1) . '</pre>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    </div><?php
$document->send();
