<?php $color = color($this->options->color); ?>

<?php while ($this->next()):  //  开始循环  ?>
    <div class="post <?php echo $rounded; ?>">
        <?php if ($this->options->headerImage && in_array('home', $this->options->headerImage)): ?>
            <?php $img = postImg($this); ?>
            <?php if ($img): ?>
                <div class="header-img">
                    <?php if ($this->options->headerImageProportion == 'not-fixed'): ?>
                    <a target="<?php $this->options->listLinkOpen(); ?>" href="<?php $this->permalink(); ?>">
                        <img src="<?php echo $img; ?>" alt="<?php $this->title(); ?>的头图" style="background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;">
                    </a>
                    <?php else: ?>
                    <a target="<?php $this->options->listLinkOpen(); ?>" tabindex="-1" aria-hidden="true" href="<?php $this->permalink() ?>" aria-label="<?php $this->title(); ?>的头图" style="background-image: url(<?php echo $img; ?>);background-color: <?php echo headerImageBgColor($this->options->headerImageBg); ?>;" class="fixed"></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <header class="entry-header border-bottom">
            <h2 class="entry-title p-name">
                <?php if ($this->sticky) $this->sticky(); ?>
                <a href="<?php $this->permalink() ?>" rel="bookmark" target="<?php $this->options->listLinkOpen(); ?>"><?php $this->title() ?></a>
            </h2>
        </header>
        <div class="entry-summary">
            <p><?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?></p>
        </div>
        <div class="article-info clearfix border-top" role="group" aria-label="文章信息">
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
            <a href="<?php $this->permalink() ?>" target="<?php $this->options->listLinkOpen(); ?>" class="float-right d-sm-none d-none d-md-inline d-lg-inline d-xl-inline <?php echo $color['link']; ?>">阅读全文</a>
            <?php if ($this->user->hasLogin()): ?>
                <a href="<?php echo $this->options->siteUrl . 'admin/write-post.php?cid=' . $this->cid; ?>" class="float-right mr-3 d-sm-none d-none d-md-inline d-lg-inline d-xl-inline <?php echo $color['link']; ?>">编辑</a>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>
