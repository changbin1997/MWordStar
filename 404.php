<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container container-404">
    <h1>404</h1>
    <h2>您访问的页面不存在！</h2>
    <h5>您还是再找找吧！</h5>
    <div class="search-box row">
        <div class="col-lg-6 col-md-8 col-sm-10 col-12 offset-lg-3 offset-md-2 offset-sm-1">
            <form action="<?php $this->options->siteUrl(); ?>" method="post">
                <div class="input-group">
                    <input type="search" class="form-control form-control-md" placeholder="搜索" aria-label="搜索" aria-describedby="button-addon2" required="required" name="s">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary btn-md">搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 col-12">
            <h4>最新文章</h4>
            <ul>
                <?php
                $this->widget('Widget_Contents_Post_Recent')->parse('<li><a href="{permalink}">{title}</a></li>');
                ?>
            </ul>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-12">
            <h4>文章分类</h4>
            <ul>
                <?php $this->widget('Widget_Metas_Category_List')->parse('<li><a href="{permalink}">{name}</a> ({count})</li>');
                ?>
            </ul>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-12">
            <h4>文章归档</h4>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=Y年m月')->parse('<li><a href="{permalink}">{date}</a></li>');
                ?>
            </ul>
        </div>
    </div>
</div>
<?php $this->need('footer.php'); ?>