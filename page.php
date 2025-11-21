<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'page';

// 语言初始化
languageInit($this->options->language);
$this->need('components/header.php');
?>

<div id="main">
    <div class="container main-content">
        <?php if ($this->options->breadcrumb == 'on'): ?>
            <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="breadcrumb-nav">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a>
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
                            <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php echo $GLOBALS['t']['post']['featuredImage']; ?>" style="background-image: url(<?php echo $headerImg; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
                        </div>
                    <?php endif; ?>
                    <div class="article-info clearfix border-bottom border-top" role="group">
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
                        <?php if ($this->user->hasLogin()): ?>
                            <div class="info d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">
                                <i class="icon icon-pencil"></i>
                                <a href="<?php echo $this->options->adminUrl . 'write-page.php?cid=' . $this->cid; ?>" ><?php  echo $GLOBALS['t']['post']['edit']; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <article>
                        <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content">
                            <?php echo $this->options->imagelazyloading == 'on'?replaceImgSrc($this->content):$this->content; ?>
                        </div>
                    </article>
                    <?php $this->need('components/comments.php'); ?>
                </main>
            </div>
            <?php $this->need('components/sidebar.php'); ?>
        </div>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>
