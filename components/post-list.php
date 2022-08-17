<?php while ($this->next()):  ?>
    <div class="post post-list-item mwordstar-block">
        <?php $headerImg = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl); ?>
        <?php if (getPostListHeaderImageStyle($this->fields->postListHeaderImageStyle, $this->options->postListHeaderImageStyle) == 'max' && $headerImg): ?>
            <div class="header-img border-top">
                <a tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title() ?>的头图" style="background-image: url(<?php echo $headerImg; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
            </div>
        <?php endif; ?>
        <header class="entry-header border-bottom">
            <h2 class="entry-title p-name">
                <?php if ($this->sticky) $this->sticky(); ?>
                <a href="<?php $this->permalink() ?>" rel="bookmark" target="<?php $this->options->listLinkOpen(); ?>"><?php $this->title() ?></a>
            </h2>
        </header>
        <div class="entry-summary" data-header-image-type="<?php echo getPostListHeaderImageStyle($this->fields->postListHeaderImageStyle, $this->options->postListHeaderImageStyle); ?>">
            <?php if (getPostListHeaderImageStyle($this->fields->postListHeaderImageStyle, $this->options->postListHeaderImageStyle) == 'mini' && $headerImg): ?>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-5 mini-header-image">
                        <a href="<?php $this->permalink(); ?>" aria-hidden="true" aria-label="文章头图" style="background-image: url(<?php echo $headerImg; ?>);" tabindex="-1"></a>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-7 col-7 content-box">
                        <p><?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p><?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?></p>
            <?php endif; ?>
        </div>
        <div class="article-info clearfix border-top" role="group" aria-label="文章信息">
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
            <a href="<?php $this->permalink() ?>" target="<?php $this->options->listLinkOpen(); ?>" class="float-right d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">阅读全文</a>
            <?php if ($this->user->hasLogin()): ?>
                <a href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" class="float-right mr-3 d-sm-none d-none d-md-inline d-lg-inline d-xl-inline">编辑</a>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>
