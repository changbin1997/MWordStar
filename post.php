<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'post';

//  点赞
if (isset($_POST['agree'])) {
    if ($_POST['agree'] == $this->cid) {
        exit(agree($this->cid));
    }
    exit('error');
}

$this->need('components/header.php');
?>

<div class="container post-page main-content mb-0">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>">首页</a>
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
                        <a itemprop="url" href="<?php $this->permalink(); ?>" rel="bookmark"><?php $this->title(); ?></a>
                    </h1>
                </header>
                <?php $headerImg = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl); ?>
                <?php if ($headerImg): ?>
                    <div class="header-img border-top">
                        <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php echo $headerImg; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
                    </div>
                <?php endif; ?>
                <div class="article-info clearfix border-bottom border-top" role="group" aria-label="文章信息">
                    <!--时间-->
                    <div class="info">
                        <i class="icon-calendar icon" aria-hidden="true"></i>
                        <span data-toggle="tooltip" data-placement="top" tabindex="0" title="发布日期：<?php $this->date('Y年m月d日'); ?>"><?php $this->date('Y年m月d日'); ?></span>
                    </div>
                    <!--作者-->
                    <div class="info">
                        <i class="icon-user icon" aria-hidden="true"></i>
                        <a data-toggle="tooltip" data-placement="top" href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>"><?php $this->author(); ?></a>
                    </div>
                    <!--阅读量-->
                    <div class="info">
                        <i class="icon-eye icon" aria-hidden="true"></i>
                        <span data-toggle="tooltip" data-placement="top" tabindex="0" title="阅读量：<?php echo getPostView($this); ?>"><?php echo getPostView($this); ?></span>
                    </div>
                    <!--评论-->
                    <div class="info">
                        <i class="icon-bubbles2 icon" aria-hidden="true"></i>
                        <a data-toggle="tooltip" data-placement="top" title="评论" href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('%d 评论'); ?></a>
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
                            <a href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" >编辑</a>
                        </div>
                    <?php endif; ?>
                </div>
                <!--文章内容-->
                <article>
                    <?php if (is_numeric($this->fields->expired) && (int)$this->fields->expired > 0 && $this->created + (int)$this->fields->expired * 86400 < time()): ?>
                        <div class="alert warning-info" role="alert">这篇文章发布于 <?php echo getDays($this->created, time()); ?> 天前，其中的信息可能已经有所发展或是发生改变！</div>
                    <?php endif; ?>
                    <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content" data-code-line-num="<?php $this->options->codeLineNum(); ?>">
                        <?php $GLOBALS['post'] = articleDirectory($this->content); ?>
                        <?php echo $this->options->imagelazyloading == 'on'?replaceImgSrc($GLOBALS['post']['content']):$GLOBALS['post']['content']; ?>
                    </div>
                    <div class="clearfix" id="copyright-info">
                        <?php if ($this->options->modified == 'show'): ?>
                            <span class="float-xl-left float-lg-left float-md-left d-block" data-toggle="tooltip" data-placement="top" tabindex="0" title="发布时间：<?php $this->date('Y年m月d日'); ?>">最后编辑：<?php echo date('Y年m月d日', $this->modified);?></span>
                        <?php endif; ?>
                        <?php if ($this->fields->articleCopyright != 'hide'): ?>
                            <span tabindex="0" data-toggle="tooltip" data-placement="top" title="本文为原创文章，版权归 <?php $this->options->title(); ?> 所有，转载请联系博主获得授权。" class="mt-1 mt-sm-1 mt-md-0 mt-lg-0 mt-lg-0 mt-xl-0 float-xl-right float-lg-right float-md-right d-block">©著作权归作者所有</span>
                        <?php endif; ?>
                    </div>
                </article>
                <div class="mb-3 agree-and-share">
                    <div class="text-center">
                        <?php $agree = $this->hidden?array('agree' => 0, 'recording' => true):agreeNum($this->cid); ?>
                        <button <?php echo $agree['recording']?'disabled':''; ?> data-cid="<?php echo $this->cid; ?>" data-url="<?php $this->permalink(); ?>" id="agree-btn" type="button" class="btn mr-2">
                            <i class="icon-thumbs-up"></i>
                            <span>赞</span>
                            <span class="agree-num"><?php echo $agree['agree']; ?></span>
                        </button>
                        <button id="share-btn" data-url="<?php $this->permalink(); ?>" type="button" class="btn" data-toggle="collapse" data-target="#qr-link" aria-expanded="false" aria-controls="collapseExample">
                            <i class="icon-share2"></i>
                            <span>分享</span>
                        </button>
                    </div>
                    <div class="collapse" id="qr-link">
                        <div class="mt-4 qr-link">
                            <p class="text-center mb-2">用手机扫描下方二维码可在手机上浏览和分享</p>
                            <div class="text-center">
                                <div id="qr" class="mb-1"></div>
                                <div class="link-box">
                                    <a class="text-danger" href="https://service.weibo.com/share/share.php?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>" target="_blank" rel="external nofollow" aria-label="分享到新浪微博" title="分享到新浪微博" data-toggle="tooltip" data-placement="top">
                                        <i class="icon-sina-weibo mr-1"></i>
                                    </a>
                                    <a class="text-info" href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>&site=<?php $this->options->siteUrl(); ?>&summary=<?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?>" target="_blank" rel="external nofollow" aria-label="分享到QQ空间" title="分享到QQ空间" data-toggle="tooltip" data-placement="top">
                                        <i class="icon-qzone-logo mr-1"></i>
                                    </a>
                                    <a class="text-info" href="https://twitter.com/intent/tweet?url=<?php $this->permalink(); ?>&text=<?php $this->title(); ?>" target="_blank" rel="external nofollow" aria-label="分享到Twitter" title="分享到Twitter" data-toggle="tooltip" data-placement="top">
                                        <i class="icon-twitter mr-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--上一篇和下一篇文章的导航-->
                <nav class="post-navigation border-top">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 previous">
                            <div>上一篇</div>
                            <?php $this->thePrev('%s','没有了'); ?>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 next">
                            <div class="text-lg-right text-xl-right text-md-right">下一篇</div class="text-lg-right text-xl-right text-md-right">
                            <div class="text-lg-right text-xl-right text-md-right next-box"><?php $this->theNext('%s','没有了'); ?></div>
                        </div>
                    </div>
                </nav>
                <?php $this->need('components/comments.php'); ?>
            </main>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php require_once 'components/max-img.php'; ?>
<?php $this->need('components/footer.php'); ?>
