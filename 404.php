<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$color = color($this->options->color);  //  获取颜色设置
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';  //  获取元素风格设置
$this->need('components/header.php');
?>

<div class="container container-404 main-content">
    <h1>404</h1>
    <h2 role="alert">您访问的页面不存在！</h2>
    <h5>您还是再找找吧！</h5>
    <div class="search-box row">
        <div class="col-lg-6 col-md-8 col-sm-10 col-12 offset-lg-3 offset-md-2 offset-sm-1">
            <form action="<?php $this->options->siteUrl(); ?>" method="post">
                <div class="input-group">
                    <input type="search" class="form-control form-control-md <?php echo $rounded; ?>" placeholder="搜索" aria-label="搜索" aria-describedby="button-addon2" required="required" name="s">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-md <?php echo $color['btn']; ?> <?php echo $rounded; ?>" aria-label="搜索" title="搜索" data-toggle="tooltip" data-placement="top">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center">
        <a href="<?php $this->options->siteUrl(); ?>" class="btn <?php echo $color['btn']; ?> <?php echo $rounded; ?>">回到首页</a>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>