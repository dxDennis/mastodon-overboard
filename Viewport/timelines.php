<?php

use DecodoMastodonService\Component\Main\MainHelper;
use DecodoMastodonService\Controller\Document\Document;

$helper = new MainHelper();
$document = new Document();
$document->addJs('/mastodon/assets/js/main.js')
    ->setTopNavigation($helper->parseInstanceLinks())
    ->start($_ENV['APP_NAME']);

?><div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
            <div class="col-12 col-md-3 server-instance-col">
                <div class="list-group server-cards">
                    <?php echo $helper->parseServerCards(); ?>
                    <div class="link-footer text-muted small">
                        <p><strong>Mastodon</strong>: <a href="https://joinmastodon.org"
                                                         target="_blank"><span>Über</span></a> · <a
                                    href="https://joinmastodon.org/apps" target="_blank"><span>App herunterladen</span></a>
                            · <a
                                    href="https://github.com/dxDennis/mastodon-overboard" rel="noopener noreferrer"
                                    target="_blank"><span>Quellcode anzeigen</span></a> · <a href="/mastodon/imprint" rel="noopener noreferrer"
                            ><span>Impressum</span></a> v4.0.2</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-7 trend-col">
                <?php echo $helper->parseTimelinePosts(); ?>
            </div>
            <div class="col-12 col-md-2 marginal-col"><?php
                echo $document->topNavigation;
                if (!empty(CURRENT_USER)) {
                    echo '<hr/>User: ' . CURRENT_USER . '<br/>' .
                        '<a href="/mastodon/?action=reset">renew list</a>';
                }
                ?></div>
        </div>
    </div><?php
$document->send();
