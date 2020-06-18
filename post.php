<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$color = color($this->options->color);
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';  //  获取元素风格设置

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
                    <a href="<?php $this->options->siteUrl(); ?>" class="<?php echo $color['link']; ?>">首页</a>
                </li>
                <li class="breadcrumb-item <?php echo $color['link']; ?>">
                    <?php $this->category(' '); ?>
                </li>
                <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->title(); ?></li>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 article-page content-area">
            <main class="<?php echo $rounded; ?>">
                <header class="entry-header">
                    <h1 class="entry-title p-name" itemprop="name headline">
                        <a itemprop="url" href="<?php $this->permalink(); ?>" rel="bookmark"><?php $this->title(); ?></a>
                    </h1>
                </header>
                <?php if ($this->options->headerImage && in_array('post', $this->options->headerImage)): ?>
                    <?php $img = postImg($this); ?>
                    <?php if ($img): ?>
                        <div class="header-img border-top">
                            <?php if ($this->options->headerImageProportion == 'not-fixed' or $this->options->headerImageProportion == 'post-page-fixed'): ?>
                                <a target="<?php $this->options->listLinkOpen(); ?>" href="<?php $this->permalink(); ?>">
                                    <img src="<?php echo $img; ?>" alt="<?php $this->title(); ?>的头图" style="background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;">
                                </a>
                            <?php else: ?>
                                <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php echo $img; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="article-info clearfix border-bottom border-top" role="group" aria-label="文章信息">
                    <!--时间-->
                    <div class="info">
                        <i class="icon-calendar icon <?php echo $color['link']; ?>" aria-hidden="true"></i>
                        <span class="<?php echo $color['link']; ?>" data-toggle="tooltip" data-placement="top" tabindex="0" title="发布日期：<?php $this->date('Y年m月d日'); ?>"><?php $this->date('Y年m月d日'); ?></span>
                    </div>
                    <!--作者-->
                    <div class="info">
                        <i class="icon-user icon <?php echo $color['link']; ?>" aria-hidden="true"></i>
                        <a class="<?php echo $color['link']; ?>" data-toggle="tooltip" data-placement="top" href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>"><?php $this->author(); ?></a>
                    </div>
                    <!--阅读量-->
                    <div class="info">
                        <i class="icon-eye icon <?php echo $color['link']; ?>" aria-hidden="true"></i>
                        <span class="<?php echo $color['link']; ?>" data-toggle="tooltip" data-placement="top" tabindex="0" title="阅读量：<?php echo getPostView($this); ?>"><?php echo getPostView($this); ?></span>
                    </div>
                    <!--评论-->
                    <div class="info">
                        <i class="icon-bubbles2 icon <?php echo $color['link']; ?>" aria-hidden="true"></i>
                        <a class="<?php echo $color['link']; ?>" data-toggle="tooltip" data-placement="top" title="评论" href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('%d 评论'); ?></a>
                    </div>
                    <!--分类-->
                    <div class="info category">
                        <i class="icon-folder-open icon <?php echo $color['link']; ?>" aria-hidden="true" data-color="<?php echo $color['link']; ?>"></i>
                        <?php $this->category(''); ?>
                    </div>
                    <!--标签-->
                    <div class="info tags" data-color="<?php echo $color['link']; ?>">
                        <i class="icon-price-tags icon <?php echo $color['link']; ?>" aria-hidden="true"></i>
                        <?php $this->tags(' ', true, '暂无标签'); ?>
                    </div>
                    <?php if ($this->user->hasLogin()): ?>
                        <div class="info d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">
                            <i class="icon icon-pencil <?php echo $color['link']; ?>"></i>
                            <a class="<?php echo $color['link']; ?>" href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" >编辑</a>
                        </div>
                    <?php endif; ?>
                </div>
                <!--文章内容-->
                <article>
                    <div data-target="<?php $this->options->postLinkOpen(); ?>" data-color="<?php echo $color['link']; ?>" class="post-content">
                        <?php $this->options->atalog == 'show'?catalog($this->content):$this->content(); ?>
                    </div>
                    <div class="clearfix">
                        <?php if ($this->options->modified == 'show'): ?>
                            <span class="float-xl-left float-lg-left float-md-left d-block" data-toggle="tooltip" data-placement="top" tabindex="0" title="发布时间：<?php $this->date('Y年m月d日'); ?>">最后编辑：<?php echo date('Y年m月d日', $this->modified);?></span>
                        <?php endif; ?>
                        <?php if ($this->fields->articleCopyright != 'hide'): ?>
                            <span tabindex="0" data-toggle="tooltip" data-placement="top" title="本文为原创文章，版权归 <?php $this->options->title(); ?> 所有，转载请联系博主获得授权。" class="mt-1 mt-sm-1 mt-md-0 mt-lg-0 mt-lg-0 mt-xl-0 float-xl-right float-lg-right float-md-right d-block">©著作权归作者所有</span>
                        <?php endif; ?>
                    </div>
                    <div class="pt-3 text-center">
                        <?php $agree = $this->hidden?array('agree' => 0, 'recording' => true):agreeNum($this->cid); ?>
                        <button <?php echo $agree['recording']?'disabled':''; ?> data-cid="<?php echo $this->cid; ?>" data-url="<?php $this->permalink(); ?>" id="agree-btn" type="button" class="btn mr-2 <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>">
                            <i class="icon-thumbs-up"></i>
                            <span>赞</span>
                            <span class="agree-num"><?php echo $agree['agree']; ?></span>
                        </button>
                        <button id="share-btn" data-url="<?php $this->permalink(); ?>" type="button" class="btn <?php echo $color['btnOutline']; ?> <?php echo $rounded; ?>" data-toggle="modal" data-target="#share-box">
                            <i class="icon-share2"></i>
                            <span>分享</span>
                        </button>
                    </div>
                </article>
                <!--上一篇和下一篇文章的导航-->
                <nav class="post-navigation navbar border-top row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 previous">
                        <div>上一篇</div>
                        <?php $this->thePrev('%s','没有了'); ?>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 next">
                        <div class="text-lg-right text-xl-right text-md-right">下一篇</div class="text-lg-right text-xl-right text-md-right">
                        <div class="text-lg-right text-xl-right text-md-right next-box"><?php $this->theNext('%s','没有了'); ?></div>
                    </div>
                </nav>
                <?php $this->need('components/comments.php'); ?>
            </main>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<div id="max-img" role="dialog">
    <img src="" alt="" class="shadow-lg">
    <div class="btn-group" role="group" aria-label="图片工具栏" id="img-control">
        <button type="button" class="btn btn-dark big" title="放大" aria-label="放大">
            <i class="icon-zoom-in"></i>
        </button>
        <button type="button" class="btn btn-dark small" title="缩小" aria-label="缩小">
            <i class="icon-zoom-out"></i>
        </button>
        <button type="button" class="btn btn-dark spin-left" title="左旋转90度" aria-label="左旋转90度">
            <i class="icon-undo"></i>
        </button>
        <button type="button" class="btn btn-dark spin-right" title="右旋转90度" aria-label="右旋转90度">
            <i class="icon-redo"></i>
        </button>
        <button type="button" class="btn btn-dark hide-img" title="关闭大图（ESC）" aria-label="关闭大图（ESC）">
            <i class="icon-cancel-circle"></i>
        </button>
    </div>
</div>
<div class="modal fade bd-example-modal-sm" id="share-box" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content <?php echo $rounded; ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">分享</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="关闭">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode"></div>
                <p>用微信扫一扫点击右上角分享</p>
                <div>
                    <a target="_blank" href="https://service.weibo.com/share/share.php?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>" class="btn btn-danger btn-block <?php echo $rounded; ?>">
                        <i class="icon-sina-weibo"></i>
                        <span>分享到新浪微博</span>
                    </a>
                    <a target="_blank" href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<?php $this->permalink(); ?>&title=<?php echo $this->title(); ?>&site=<?php $this->options->siteUrl(); ?>&summary=<?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?>" class="btn btn-primary btn-block <?php echo $rounded; ?>">
                        <i class="icon-qzone"></i>
                        <span>分享到QQ空间</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>
