<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = '404';
$this->need('components/header.php');
?>

<div class="container container-404 main-content">
    <h1>404</h1>
    <h2 role="alert" class="mb-5">您访问的页面不存在！</h2>
    <div class="search-box row">
        <div class="col-lg-6 col-md-8 col-sm-10 col-12 offset-lg-3 offset-md-2 offset-sm-1">
            <form action="<?php $this->options->siteUrl(); ?>" method="post" role="search">
                <div class="input-group">
                    <input type="search" class="form-control form-control-md" placeholder="搜索" aria-label="搜索" aria-describedby="button-addon2" required="required" name="s">
                    <div class="input-group-append">
                        <button type="submit" class="search-btn btn btn-md" aria-label="搜索" title="搜索" data-toggle="tooltip" data-placement="top">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center">
        <a href="<?php $this->options->siteUrl(); ?>" class="btn to-home-link">回到首页</a>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>