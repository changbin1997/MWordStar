<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'post';

//  点赞
if (isset($_POST['agree'])) {
    if ($_POST['agree'] == $this->cid) {
        exit((string)agree($this->cid));
    }
    exit('error');
}

// 获取文章底部交互区域的按钮设置
$engagementSection = str_replace(' ', '', $this->options->engagementSection);
if ($engagementSection != '') $engagementSection = explode(',', $engagementSection);

// 语言初始化
languageInit($this->options->language);
$this->need('components/header.php');
?>

<div id="main">
    <div class="container post-page main-content mb-0">
        <?php if ($this->options->breadcrumb == 'on'): ?>
            <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="breadcrumb-nav">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <?php $this->category(' '); ?>
                    </li>
                    <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->title(); ?></li>
                </ol>
            </nav>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12 col-lg-8 col-sm-12 article-page content-area">
                <main class="mwordstar-block">
                    <header class="entry-header">
                        <h1 class="entry-title p-name" itemprop="name headline">
                            <a itemprop="url" href="<?php $this->permalink(); ?>" rel="bookmark">
                                <?php
                                if ($this->hidden) {
                                    echo $GLOBALS['t']['post']['thisPostIsPasswordProtected'];
                                }else {
                                    $this->title();
                                }
                                ?>
                            </a>
                        </h1>
                    </header>
                    <?php $headerImg = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl); ?>
                    <?php if ($headerImg): ?>
                        <div class="header-img border-top">
                            <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php echo $GLOBALS['t']['post']['featuredImage']; ?>" style="background-image: url(<?php echo $headerImg; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
                        </div>
                    <?php endif; ?>
                    <div class="article-info clearfix border-bottom border-top" role="group" aria-label="<?php echo $GLOBALS['t']['post']['postInfo']; ?>">
                        <!--时间-->
                        <div class="info">
                            <i class="icon-calendar icon" aria-hidden="true"></i>
                            <span data-toggle="tooltip" data-placement="top" title="<?php echo $GLOBALS['t']['post']['publicationDate']; ?>">
                                <time datetime="<?php $this->date('c'); ?>"><?php echo postDateFormat($this->created); ?></time>
                            </span>
                        </div>
                        <!--作者-->
                        <div class="info">
                            <i class="icon-user icon" aria-hidden="true"></i>
                            <a data-toggle="tooltip" data-placement="top" href="<?php $this->author->permalink(); ?>" title="<?php echo $GLOBALS['t']['post']['author']; ?>"><?php $this->author(); ?></a>
                        </div>
                        <!--阅读量-->
                        <div class="info">
                            <i class="icon-eye icon" aria-hidden="true"></i>
                            <?php $views = postViews($this); ?>
                            <span data-toggle="tooltip" data-placement="top" title="<?php echo $GLOBALS['t']['post']['views']; ?>"><?php echo $views; ?></span>
                        </div>
                        <!--评论-->
                        <div class="info">
                            <i class="icon-bubbles2 icon" aria-hidden="true"></i>
                            <a data-toggle="tooltip" data-placement="top" title="<?php echo $GLOBALS['t']['post']['comments']; ?>" href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('%d ' . $GLOBALS['t']['post']['comments']); ?></a>
                        </div>
                        <!--分类-->
                        <div class="info category">
                            <i class="icon-folder-open icon" aria-hidden="true"></i>
                            <?php $this->category(''); ?>
                        </div>
                        <!--标签-->
                        <div class="info tags">
                            <i class="icon-price-tags icon" aria-hidden="true"></i>
                            <?php $this->tags(' ', true, '暂无标签'); ?>
                        </div>
                        <?php if ($this->user->hasLogin()): ?>
                            <div class="info d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">
                                <i class="icon icon-pencil"></i>
                                <a href="<?php echo $this->options->adminUrl . 'write-post.php?cid=' . $this->cid; ?>"><?php  echo $GLOBALS['t']['post']['edit']; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!--文章内容-->
                    <article>
                        <?php if (is_numeric($this->fields->expired) && (int)$this->fields->expired > 0 && $this->created + (int)$this->fields->expired * 86400 < time()): ?>
                            <div class="alert warning-info" role="alert">
                                <?php printf($GLOBALS['t']['post']['warningMessage'], getDays($this->created, time())); ?>
                            </div>
                        <?php endif; ?>
                        <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content">
                            <?php $GLOBALS['postPage'] = preg_split('/\[-page-]|<p>\[-page-]<\/p>/', $this->content); ?>
                            <?php $postPageNum = isset($_GET['post-page']) ? $_GET['post-page'] : 1; ?>
                            <?php if (!isset($GLOBALS['postPage'][$postPageNum - 1])) $postPageNum = 1; ?>
                            <?php $GLOBALS['post'] = articleDirectory($GLOBALS['postPage'][$postPageNum - 1]); ?>
                            <?php echo $this->options->imagelazyloading == 'on' ? replaceImgSrc($GLOBALS['post']['content']) : $GLOBALS['post']['content']; ?>
                        </div>
                        <?php if (count($GLOBALS['postPage']) > 1): ?>
                            <nav aria-label="<?php echo $GLOBALS['t']['pagination']['postContentPagination']; ?>" class="py-3 post-pagination">
                                <ol class="pagination justify-content-center">
                                    <?php if ($postPageNum > 1): ?>
                                        <li class="page-item">
                                            <a href="<?php echo $this->permalink . '?post-page=' . ($postPageNum - 1); ?>" class="page-link previous-page" aria-label="<?php echo $GLOBALS['t']['pagination']['previousPage']; ?>" title="<?php echo $GLOBALS['t']['pagination']['previousPage']; ?>" data-toggle="tooltip" data-placement="top">
                                                <i class="icon-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php for ($i = 0; $i < count($GLOBALS['postPage']); $i++): ?>
                                        <li class="page-item <?php if ($i == $postPageNum - 1) echo 'active'; ?>">
                                            <a href="<?php echo $this->permalink . '?post-page=' . ($i + 1); ?>" class="page-link" <?php if ($i == $postPageNum - 1) echo 'aria-current="page"'; ?>><?php echo $i + 1; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($postPageNum < count($GLOBALS['postPage'])): ?>
                                        <li class="page-item">
                                            <a href="<?php echo $this->permalink . '?post-page=' . ($postPageNum + 1); ?>" class="page-link next-page" aria-label="<?php echo $GLOBALS['t']['pagination']['nextPage']; ?>" title="<?php echo $GLOBALS['t']['pagination']['nextPage']; ?>" data-toggle="tooltip" data-placement="top">
                                                <i class="icon-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                        <div class="clearfix" id="copyright-info">
                            <?php if ($this->options->modified == 'show'): ?>
                                <span class="float-xl-left float-lg-left float-md-left d-block">
                                    <time datetime="<?php echo date('c', $this->modified); ?>">
                                        <?php printf($GLOBALS['t']['post']['updated'], postDateFormat($this->modified)); ?>
                                    </time>
                                </span>
                            <?php endif; ?>
                            <?php if ($this->fields->articleCopyright != 'hide'): ?>
                                <span tabindex="0" data-toggle="tooltip" data-placement="top" title="<?php printf($GLOBALS['t']['post']['copyrightNotice'][1], $this->options->title); ?>" class="mt-1 mt-sm-1 mt-md-0 mt-lg-0 mt-lg-0 mt-xl-0 float-xl-right float-lg-right float-md-right d-block"><?php echo $GLOBALS['t']['post']['copyrightNotice'][0]; ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php if ($engagementSection != ''): ?>
                        <div class="mb-3 agree-and-share">
                            <div class="text-center">
                                <?php foreach ($engagementSection as $val): ?>
                                    <?php if ($val == '点赞'): ?>
                                        <?php $agree = $this->hidden ? array('agree' => 0, 'recording' => true) : agreeNum($this->cid); ?>
                                        <button <?php echo $agree['recording'] ? 'disabled' : ''; ?> data-cid="<?php echo $this->cid; ?>" data-url="<?php $this->permalink(); ?>" id="agree-btn" type="button" class="btn mr-2">
                                            <i class="icon-thumbs-up"></i>
                                            <span><?php echo $GLOBALS['t']['post']['like']; ?></span>
                                            <span class="agree-num"><?php echo $agree['agree']; ?></span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($val == '分享'): ?>
                                        <button id="share-btn" data-url="<?php $this->permalink(); ?>" type="button" class="btn mr-2" data-toggle="collapse" data-target="#qr-link" aria-expanded="false" aria-controls="collapseExample">
                                            <i class="icon-share2"></i>
                                            <span><?php echo $GLOBALS['t']['post']['share']; ?></span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($val == '打赏'): ?>
                                        <button type="button" class="btn mr-2" data-toggle="collapse" data-target="#reward-qr" aria-expanded="false">
                                            <i class="icon-coffee"></i>
                                            <span><?php echo $GLOBALS['t']['post']['donate']; ?></span>
                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php if (in_array('打赏', $engagementSection)): ?>
                                <!--打赏二维码-->
                                <div class="collapse" id="reward-qr">
                                    <div class="mt-4 text-center qr">
                                        <img src="<?php $this->options->rewardQr(); ?>" alt="<?php echo $GLOBALS['t']['post']['QRCode']; ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('分享', $engagementSection)): ?>
                                <!--分享区域-->
                                <div class="collapse" id="qr-link">
                                    <div class="mt-4 qr-link">
                                        <p class="text-center mb-2"><?php echo $GLOBALS['t']['post']['scanTheQRCodeBelowToViewAndShareThisPageOnYourPhone']; ?></p>
                                        <div class="text-center">
                                            <div id="qr" class="mb-1"></div>
                                            <div class="link-box">
                                                <a class="text-danger" href="https://service.weibo.com/share/share.php?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>" target="_blank" rel="external nofollow" aria-label="<?php echo $GLOBALS['t']['post']['shareOnWeibo']; ?>" title="<?php echo $GLOBALS['t']['post']['shareOnWeibo']; ?>" data-toggle="tooltip" data-placement="top">
                                                    <i class="icon-sina-weibo mr-1"></i>
                                                </a>
                                                <a class="text-info" href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>&site=<?php $this->options->siteUrl(); ?>&summary=<?php $this->fields->summaryContent ? $this->fields->summaryContent() : $this->excerpt($this->options->summary, '...'); ?>" target="_blank" rel="external nofollow" aria-label="<?php echo $GLOBALS['t']['post']['shareOnQzone']; ?>" title="<?php echo $GLOBALS['t']['post']['shareOnQzone']; ?>" data-toggle="tooltip" data-placement="top">
                                                    <i class="icon-qzone-logo mr-1"></i>
                                                </a>
                                                <a class="text-info" href="https://twitter.com/intent/tweet?url=<?php $this->permalink(); ?>&text=<?php $this->title(); ?>" target="_blank" rel="external nofollow" aria-label="<?php echo $GLOBALS['t']['post']['shareOnTwitter']; ?>" title="<?php echo $GLOBALS['t']['post']['shareOnTwitter']; ?>" data-toggle="tooltip" data-placement="top">
                                                    <i class="icon-twitter mr-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <!--上一篇和下一篇文章的导航-->
                    <nav class="post-navigation border-top">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 previous">
                                <div id="previous-post-text"><?php echo $GLOBALS['t']['post']['previousPost']; ?></div>
                                <?php $this->thePrev('%s', $GLOBALS['t']['post']['none']); ?>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 next">
                                <div id="next-post-text" class="text-lg-right text-xl-right text-md-right"><?php echo $GLOBALS['t']['post']['nextPost']; ?></div class="text-lg-right text-xl-right text-md-right">
                                <div class="text-lg-right text-xl-right text-md-right next-box"><?php $this->theNext('%s', $GLOBALS['t']['post']['none']); ?></div>
                            </div>
                        </div>
                    </nav>
                    <?php $this->need('components/comments.php'); ?>
                </main>
            </div>
            <?php $this->need('components/sidebar.php'); ?>
        </div>
    </div>
    <?php if ($this->options->directoryMobile == 'enable'): ?>
        <div id="directory-mobile" class="border rounded" style="display: none;">
            <div class="title-bar border-bottom">
                <h5 class="m-0"><?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?></h5>
                <button type="button" class="btn btn-sm close-btn" aria-label="<?php echo $GLOBALS['t']['sidebar']['closeTableOfContents']; ?>" title="<?php echo $GLOBALS['t']['sidebar']['closeTableOfContents']; ?>">
                    <i class="icon-cancel-circle"></i>
                </button>
            </div>
            <div class="p-3 directory-list">
                <?php echo $GLOBALS['post']['directory']; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $this->need('components/footer.php'); ?>