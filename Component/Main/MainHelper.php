<?php

namespace DecodoMastodonService\Component\Main;

use DateTime;
use DateTimeImmutable;
use DecodoMastodonService\Component\HelperComponent;
use DecodoMastodonService\Controller\Mastodon\InstanceController;

class MainHelper extends HelperComponent
{

    public array $statsCollection = [];
    public array $trendCollection = [];
    public array $orderedTrendsCollection = [];

    /**
     * @var InstanceController
     */
    public $instanceController;

    public function __construct()
    {
        $this->instanceController = new InstanceController();
    }

    /**
     * @param array $instance
     * @param $extended
     * @return string
     */
    public function parseServerCard(array $instance, $extended = false)
    {
        $htmlContent = '';

        $image = (isset($instance['data']['thumbnail']['url']) && !empty($instance['data']['thumbnail']['url']) ?
            '<img class="card-img-top" src="' . $instance['data']['thumbnail']['url'] . '" alt="' . $instance['data']['description'] . '">' :
            self::getPlaceholderImage($instance['data']['description'], '', 'card-img-top')
        );

        $userActiveMonth = $instance['data']['usage']['users']['active_month'];

        if ($userActiveMonth > 999999) {
            $userActiveMonth = number_format($userActiveMonth / 10000, 0, ',', '.') . 'Mio';
        } elseif ($userActiveMonth > 999) {
            $userActiveMonth = number_format($userActiveMonth / 1000, 0, ',', '.') . 'K';
        }

        $extendedHtml = '';
        if ($extended) {
            $extendedHtml .= '';
        }

        $activeMembers = '<div class="active-members">' .
            '<div class="favourites_count mx-2 ' . ($userActiveMonth <= 0 ? 'text-muted' : '') . '">' .
            '<i class="fas fa-users fa-fw"></i> ' . $userActiveMonth .
            '</div></div> ';

        $contactInfo = '<div class="contact-info">' .
            '<a href="' . $instance['data']['contact']['account']['url'] . '" target="_blank" class="text-muted">' .
            '<img src="' . $instance['data']['contact']['account']['avatar'] . '" style="width:16px;"> ' .
            $instance['data']['contact']['account']['display_name'] .
            '</a>' .
            '</div>';

        $extendedHtml .= (!empty(CURRENT_USER) ?
            '<div style="position:absolute; top:15px;right:15px;" ><small>' .
            '<a target="_blank" ' .
            'href="https://www.decodo.de/mastodon/?action=stats&instance=' . $instance['name'] . '">' .
            '<i class="fas fa-project-diagram"></i>' .
            '</a> &middot; ' .
            '<a target="_blank" ' .
            'href="https://' . $instance['name'] . '/api/v2/instance">' .
            '<i class="fas fa-server"></i>' .
            '</a> &middot; ' .
            '<a target="_blank" ' .
            'href="https://' . $instance['name'] . '/api/v1/trends/statuses">' .
            '<i class="fas fa-server"></i>' .
            '</a>' .
            '</small></div>'
            : '');
        if ($instance['name'] !== 'mastodon.social') {
            $extendedHtml .= '<hr><div class="text-muted"><small><small><strong>' . $instance['name'] . '</strong> ' .
                'ist Teil des dezentralen sozialen Netzwerks, das von ' .
                '<a href="https://joinmastodon.org" target="_blank">Mastodon</a> betrieben wird. ' .
                '<a href="/mastodon/?action=renewInstance&instance=' . $instance['name'] . '">' .
                '<sup><i class="text-muted fas fa-sync fa-fw"></i></sup></a>' .
                '</small></small></div>';
        }

        $extendedHtml .= '' .
            '<div class="form-check form-switch">' .
            '<input data-instance-name="' . $instance['name'] . '" class="form-check-input instance-server-visibility" type="checkbox" role="switch" checked>' .
            '<label class="form-check-label" for="flexSwitchCheckDefault">Aktiv</label>' .
            '</div>';

        $htmlContent .= '<div class="card shadow-sm mb-3 server-card" id="server-instance-'.$instance['name'].'" >' .
            '<a target="_blank"  href="https://' . $instance['name'] . '">' .
            $image .
            '</a>' .

            '<div class="card-body position-realtive">' .
            '<div class="instance-stats">' .
            '<div class="d-flex justify-content-between align-items-center text-muted">' .
            $contactInfo .
            $activeMembers .
            '</div>' .
            '</div>' .
            '<div class="card-content">' .
            '<p class="card-text"><small>' . $instance['data']['description'] . '</small></p>' .
            '</div>' .
            '<div class="extended">' .
            $extendedHtml .
            '</div>' .
            '</div>' .


            '</div>';

        return $htmlContent;
    }

    /**
     * @param bool $reset
     * @return string
     * ()=>{}
     */
    public function parseServerCards(bool $reset = false)
    {
        $htmlContent = '';
        $instanceStats = $this->getStatsCollection($reset);

        foreach ($instanceStats as $instanceName => $instance) {
            if (!empty($instance['data'])) {
                $htmlContent .= $this->parseServerCard($instance);

            } else {
                $htmlContent .= '<div class="alert alert-danger">' .
                    '<strong>' . $instanceName . '</strong> skipped<br/>' .
                    'no data received from this instance<br/>' .
                    '<a href="/mastodon/?action=renewInstance&instance=' . $instance . '"><i class="fas fa-sync fa-fw"></i> retry</a>' .
                    '</div>';
            }

        }
        return $htmlContent;
    }

    /**
     * @return string
     */
    public function parseInstanceLinks(bool $reset = false)
    {
        $htmlContent = '';
        $instanceStats = $this->getStatsCollection($reset);

        foreach ($instanceStats as $instanceName => $instance) {
            $count = (isset($instance['data']) ? count($instance['data']) : 0);

            $expiresIn = '';
            if (isset($instance['expires_at'])) {
                $expiresIn = 'wird erneuert in: ' . self::timeAgo($instance['expires_at']) . ' (' . $instance['expires_at'] . ') ';
            }
            $htmlContent .= '<li><a title="' . $expiresIn . '" href="https://' . $instanceName . '" target="_blank">' . $instanceName . '</a><br/>' .
                '<small data-instance-count="' . $instanceName . '" class="text-muted">' . $count . ' Posts</small></li>';
        }

        $htmlContent .= '<li><a href="/mastodon/imprint" >Impressum</a></li>';

        return '<ul class="list-unstyled">' . $htmlContent . '</ul>';
    }


    /**
     * @param bool $reset
     * @return array
     */
    public function getOrderedTrendsCollection(bool $reset = false): array
    {
        if (empty($this->orderedTrendsCollection) || count($this->orderedTrendsCollection) <= 0 || $reset !== false) {
            $this->orderedTrendsCollection = [];

            foreach ($this->getTrendCollection($reset) as $instanceName => $instance) {

                $count = (isset($instance['data']) && is_array($instance['data']) ? count($instance['data']) : 0);

                if ($count > 0) {
                    foreach ($instance['data'] as $article) {

                        $points = 0;
                        $points += ((int)$article['replies_count'] * 20);
                        $points += ((int)$article['reblogs_count'] * 10);
                        $points += ((int)$article['favourites_count'] * 2);
                        $points += ($instanceName === 'mastodon.social' ? 10 : 0);

                        $article['instance_name'] = $instanceName;
                        $article['instance_uri'] = $instance['uri'];
                        $article['unix_time'] = strtotime($article['created_at']);
                        $article['since'] = self::timeAgo($article['created_at']);
                        $article['time'] = date('Y-m-d H:i:s', $article['unix_time']);
                        $article['expiration_time'] = $instance['expiration_time'];

                        $this->orderedTrendsCollection[$article['unix_time']][$points][] = $article;
                    }
                }
            }
            krsort($this->orderedTrendsCollection);
        }
        return $this->orderedTrendsCollection;
    }

    /**
     * @param bool $reset
     * @return array
     */
    public function getTrendCollection(bool $reset = false): array
    {
        if (empty($this->trendCollection) || count($this->trendCollection) <= 0 || $reset !== false) {
            $this->trendCollection = $this->instanceController->getInstanceTrendsCollection($reset);
        }
        return $this->trendCollection;
    }

    /**
     * @param bool $reset
     * @return array
     */
    public function getStatsCollection(bool $reset = false): array
    {
        if (empty($this->statsCollection) || count($this->statsCollection) <= 0 || $reset !== false) {
            $this->statsCollection = $this->instanceController->getInstanceStatsCollection($reset);
        }
        return $this->statsCollection;
    }

    /**
     * @param bool $reset
     * @return string
     */
    public function parsePosts(bool $reset = false): string
    {
        $htmlContent = '';
        foreach ($this->getOrderedTrendsCollection() as $time_code => $balanced_posts) {

            krsort($balanced_posts);
            foreach ($balanced_posts as $balancePoints => $articles) {
                foreach ($articles as $article) {

                    $htmlContent .= '<div data-card-instance="' . $article['instance_name'] . '" class="trend-cards" ' .
                        'title="läuft ab in ' . $article['expiration_time'] . '">' . $this->parseCard($article, $balancePoints) . '</div>';
                }
            }
        }

        return $htmlContent;
    }

    /**
     * @param $article
     * @param $balancePoints
     * @return string
     */
    public function parseCard($article, $balancePoints)
    {

        $cardContent = $article['content'];

        $title = (isset($article['card']['title']) ? $article['card']['title'] : '');
        $imageCollection = '';

        if (isset($article['media_attachments']) && count($article['media_attachments']) > 0) {


            foreach ($article['media_attachments'] as $mediaItem) {
                $desc = (isset($mediaItem['description']) ? $mediaItem['description'] : $title);

                $colSize = 12;
                if (count($article['media_attachments']) === 2 || count($article['media_attachments']) === 4) {
                    $colSize = 6;
                } elseif (count($article['media_attachments']) === 3 || count($article['media_attachments']) === 6) {
                    $colSize = 3;
                } elseif (count($article['media_attachments']) > 6) {
                    $colSize = 4;
                }


                if ($mediaItem['type'] === 'image' || $mediaItem['type'] === 'unknown' || $mediaItem['type'] === 'gifv') {

                    $previewUrl = (isset($mediaItem['preview_url']) && !empty($mediaItem['preview_url']) ? $mediaItem['preview_url'] : '');

                    $image = (!empty($previewUrl) ?
                        '<img class="card-img-top" src="' . $previewUrl . '" alt="' . $desc . '" title="' . $desc . '">' :
                        self::getPlaceholderImage($title, '', 'card-img-top'));

                    $target = (!empty($mediaItem['remote_url']) ? $mediaItem['remote_url'] : $previewUrl);

                    $imageCollection .= '<div class="col-sm-12 col-md-' . $colSize . '" data-type="' . $mediaItem['type'] . '">' .
                        '<a href="' . $target . '" title="' . $desc . '" target="_blank">' .
                        $image .
                        '</a></div>';

                } elseif ($mediaItem['type'] === 'video') {

                    $previewUrl = (isset($mediaItem['preview_url']) && !empty($mediaItem['preview_url']) ? $mediaItem['preview_url'] : '');

                    $image = (isset($mediaItem['preview_url']) && !empty($mediaItem['preview_url']) ?
                        '<img class="card-img-top" src="' . $mediaItem['preview_url'] . '" alt="' . $desc . '"  title="' . $desc . '">' :
                        self::getPlaceholderImage($title, '', 'card-img-top'));

                    $target = (!empty($mediaItem['url']) ? $mediaItem['url'] : $previewUrl);

                    $imageCollection .= '<div class="col-sm-12 col-md-' . $colSize . '" data-type="' . $mediaItem['type'] . '">' .
                        '<a href="' . $target . '" title="' . $desc . '" target="_blank">' .
                        $image .
                        '</a></div>';
                } else {
                    $imageCollection .= '<div class="col-sm-12 col-md-' . $colSize . '" data-type="' . $mediaItem['type'] . '"><svg class="bd-placeholder-img card-img-top" width="100%" height="225" ' .
                        'xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" ' .
                        'preserveAspectRatio="xMidYMid slice" focusable="false">' .
                        '<title></title><rect width="100%" height="100%" fill="#55595c"/>' .
                        '<text x="50%" y="50%" fill="#eceeef" dy=".3em">' . $title . '</text></svg></div>';
                }

            }
        } elseif (!empty($title)) {


            $urlRoot = explode('/', $article['card']['url']);

            $image = (isset($article['card']['image']) && !empty($article['card']['image']) ?
                '<img style="width:100%; height:100%; object-fit:cover" src="' . $article['card']['image'] . '" alt="' . $article['card']['title'] . '">' :
                self::getPlaceholderImage($article['card']['title'], ''));

            $imageCollection .= '<div class="col position-realtive">' .
                '<div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">' .
                '<div class="col-3 d-none d-lg-block">' .
                $image .
                '</div>' .
                '<div class="col px-3 py-2 d-flex flex-column position-static overflow-hidden">' .
                '<p class="text-truncate m-0" style="max-width: 100%;">' . $article['card']['title'] . '</p>' .
                '<small class="text-truncate m-0 mb-2" style="max-width: 100%;">' . $article['card']['description'] . '</small>' .
                '<a href="' . $article['card']['url'] . '" class="stretched-link" target="_blank">' .
                '<small class="text-muted">' . $urlRoot[2] . '</small>' .
                '</a>' .
                '</div>' .
                '</div>' .
                '</div>';
        }

        $cardContent .= '<div class="media-collection row">' . $imageCollection . '</div>';

        $actionBar = '<div class="d-flex justify-content-between align-items-center">' .


            '<div class="d-flex flex-grow-1">' .
            '<div class="favourites_count mx-2 ' . ($article['favourites_count'] <= 0 ? 'text-muted' : '') . '">' .
            '<i class="fas fa-star fa-fw"></i> ' . $article['favourites_count'] .
            '</div> ' .
            '<div class="reblogs_count mx-2 ' . ($article['reblogs_count'] <= 0 ? 'text-muted' : '') . '">' .
            '<i class="fas fa-retweet fa-fw"></i> ' . $article['reblogs_count'] . '</div> ' .
            '<div class="replies_count mx-2 ' . ($article['replies_count'] <= 0 ? 'text-muted' : '') . '">' .
            '<i class="fas fa-reply fa-fw"></i> ' . $article['replies_count'] . '</div> ' .
            '<a href="' . $article['url'] . '" ' .
            'target="_blank" >View <i class="fa fa-external-link-alt"></i></a>' .
            '</div>' .

            (!empty(CURRENT_USER) ?
                '<div class="btn-group">' .
                '<a href="' . $article['url'] . '" ' .
                'target="_blank" type="button" class="btn btn-sm btn-outline-secondary">View</a>' .
                '<a href="/mastodon/?id=' . $article['id'] . '&instance=' . $article['instance_name'] . '" ' .
                'target="_blank" type="button" class="btn btn-sm btn-outline-secondary">raw</a>' .
                '</div>'
                : '') .


            '</div>';

        $more = '';

        $userRow = '' .
            '<div class="d-flex p-3"  title="' . $article['account']['username'] . '">' .
            $this->parseUserCard($article['account']) .
            '<div class="text-muted text-end  position-relative">' .
            '<a class="text-muted" title="' . $article['instance_name'] . '" ' .
            'href="https://' . $article['instance_name'] . '" target="_blank" ' .
            'alt="' . $article['instance_name'] . '" class="stretched-link">' .
            '<small>' . $article['instance_name'] . '</small></a>' .
            '<br/>' .
            '<small title="balance-points: ' . $balancePoints . '">' .
            '<sub><i class="fas fa-globe-europe"></i> ' . $article['since'] . '</sub>' .
            '</small>' .

            '</div>' .
            '</div>' .
            '';


        return '<div class="card shadow-sm mb-4">' .
            $userRow .
            '<div class="card-body">' .
            $cardContent .
            '<hr>' .
            $actionBar .
            $more .
            '</div>' .
            '</div>';
    }

    public static function parseUserCard($account)
    {
        return '<div class="d-flex flex-grow-1 position-relative">' .
            '<div class="flex-shrink-0">' .
            '<img src="' . $account['avatar'] . '" style="width:46px">' .
            '</div>' .
            '<div class=" ms-3">' . $account['username'] . '<br/>' .
            '<small class="text-muted">' . $account['acct'] . ' ' .
            '<a href="' . $account['url'] . '" target="_blank" ' .
            'alt="' . $account['username'] . '" class="stretched-link">&nbsp;</a>' .
            '</small>' .
            '</div>' .
            '</div>';
    }


    public function parseServerHeader(array $instance, $extended = false)
    {
        $htmlContent = '';

        $image = (isset($instance['data']['thumbnail']['url']) && !empty($instance['data']['thumbnail']['url']) ?
            '<img class="card-img-top" src="' . $instance['data']['thumbnail']['url'] . '" alt="' . $instance['data']['description'] . '">' :
            self::getPlaceholderImage($instance['data']['description'], '', 'card-img-top')
        );

        $userActiveMonth = $instance['data']['usage']['users']['active_month'];

        if ($userActiveMonth > 999999) {
            $userActiveMonth = (int)($userActiveMonth / 10000) . 'Mio.';
        } elseif ($userActiveMonth > 99999) {
            $userActiveMonth = (int)($userActiveMonth / 1000) . 'K';
        }

        $extendedHtml = '';
        if ($extended) {
            $extendedHtml .= '';
        }


        $contactImage = '<a href="' . $instance['data']['contact']['account']['url'] . '" target="_blank" class="text-muted">' .
            '<img src="' . $instance['data']['contact']['account']['avatar'] . '" style="width: 192px;"></a>';


//        $extendedHtml .= (!empty(CURRENT_USER) ?
//            '<div style="position:absolute; top:15px;right:15px;" ><small>' .
//            '<a target="_blank" ' .
//            'href="https://www.decodo.de/mastodon/?action=stats&instance=' . $instance['name'] . '">' .
//            '<i class="fas fa-project-diagram"></i>' .
//            '</a>' .
//            '</small></div>'
//            : '');

        $registerIcon = '<i class="fas fa-check text-success" title="Registrierung möglich. ' . trim($instance['data']['registrations']['message']) . '"></i> ' .
            'Registrierung möglich. ' . trim($instance['data']['registrations']['message']);

        $approvalIcon = '<i class="fas fa-certificate text-light-emphasis"></i> Verifizierung nicht nötig';

        if ($instance['data']['registrations']['approval_required']) {
            $approvalIcon = '<i class="fas fa-certificate text-danger"></i> Verifizierung nötig';
        }

        $approvalIcon = ' &middot; Verifizierung:  ' . $approvalIcon;

        if (!$instance['data']['registrations']['enabled']) {
            $registerIcon = '<i class="fas fa-times text-danger" title="Registrierung nicht möglich. ' . trim(strip_tags($instance['data']['registrations']['message'])) . '"></i> ' .
                'Registrierung nicht möglich. ' . trim($instance['data']['registrations']['message']);
            $approvalIcon = '';
        }


        $htmlContent .= '<div class="card shadow-sm mb-3">' .
            '<a target="_blank"  href="https://' . $instance['name'] . '">' .
            $image .
            '</a>' .

            '<div class="banner-info-box card-body position-realtive">' .
            '<div class="instance-stats">' .
            '<div class="d-flex">' .
            '<div class="instance-avatar">' . $contactImage . '</div>' .
            '<div class="card-content m-3" style="left: 210px; position: absolute; bottom:0">' .
            '<p class="card-text">' .
            '<h1><strong>' . $instance['data']['contact']['account']['display_name'] . '</strong>' .
            '<small><small>' .
            ' [' . $instance['data']['contact']['email'] . ']' .
            '</small></small></h1> ' .

            '' . $instance['data']['description'] . '</p>' .
            '<hr/>' .

            '<small>' .
            '<i class="fas fa-users fa-fw"></i> ' . $userActiveMonth . ' &middot; ' .
            ' Version: ' . $instance['data']['version'] . ' &middot; ' .
            ' Sprachen: ' . implode(',', $instance['data']['languages']) . ' &middot; ' .
            ' Registrierung: ' . $registerIcon .
            $approvalIcon .
            '</small>' .

            '</div>' .
            '</div>' .
            '</div>' .

            '</div>' .


            '</div>';

        return $htmlContent;
    }

}
