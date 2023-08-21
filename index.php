<?php
/**
 * 这是一套简洁的博客主题 <a href="https://mwordstar.misterma.com/" target="_blank">点击查看使用说明</a>
 *
 * @package MWordStar
 * @author Mr. Ma
 * @version 开发板（暂无版本号）
 * @link https://www.misterma.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
// 获取元素风格设置
$rounded = $this->options->rounded == 'rightAngle'?'rounded-0':'';

$GLOBALS['page'] = 'index';

// 检查数据库字段
checkField();
// 头文件
$this->need('components/header.php');
?>
<div class="container home main-content">
    <div class="row">
        <div class="article-list col-md-12 col-lg-8 col-sm-12 content-area">
            <?php $this->need('components/post-list.php'); ?>
            <nav aria-label="分页导航区" class="pagination-nav">
                <?php $this->pageNav('<i class="icon-chevron-left"></i>', '<i class="icon-chevron-right"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center', 'itemTag' => 'li',  'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => 'prev', 'nextClass' => 'next')); ?>
            </nav>
        </div>
    <?php $this->need('components/sidebar.php'); ?>
    </div>
</div>
<?php require_once 'components/max-img.php'; ?>
<?php $this->need('components/footer.php'); ?>
