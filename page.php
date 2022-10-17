<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'page';
$this->need('components/header.php');
?>

<div class="container main-content">
    <?php if ($this->options->breadcrumb == 'on'): ?>
        <nav aria-label="路径" class="breadcrumb-nav">
            <ol class="breadcrumb m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="<?php $this->options->siteUrl(); ?>">首页</a>
                </li>
                <li tabindex="0" class="breadcrumb-item active" aria-current="page"><?php $this->title(); ?></li>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12 col-lg-8 col-sm-12 page content-area">
            <main class="mwordstar-block">
                <header class="entry-header">
                    <h1 class="entry-title p-name" itemprop="name headline">
                        <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h1>
                </header>
                <?php $headerImg = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl); ?>
                <?php if ($headerImg): ?>
                    <div class="header-img border-top">
                        <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php echo $headerImg; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
                    </div>
                <?php endif; ?>
                <div class="article-info clearfix border-bottom border-top" role="group" aria-label="页面信息">
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
                        <span data-toggle="tooltip" data-placement="top" tabindex="0" title="访问量：<?php echo getPostView($this); ?>"><?php echo getPostView($this); ?></span>
                    </div>
                </div>
                <article>
                    <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content" data-code-line-num="<?php $this->options->codeLineNum(); ?>">
                        <?php echo $this->options->imagelazyloading == 'on'?replaceImgSrc($this->content):$this->content; ?>
                    </div>
                </article>
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
<?php $this->need('components/footer.php'); ?>
