<?php
/**
 * 网站数据
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 文章更新日历数据
$postCalendarData = postCalendar(time() - 20736000, time());
// 评论更新日历数据
$commentCalendarData = commentCalendar(time() - 20736000, time());
// 获取分类数据
$categoryPostCount = categoryPostCount();

$GLOBALS['page'] = 'page-data';
$this->need('components/header.php');
?>

<div id="main">
    <div class="container data-page main-content mb-0">
        <?php if ($this->options->breadcrumb == 'on'): ?>
            <!--面包屑导航-->
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
            <div class="archive col-md-12 col-lg-8 col-sm-12 content-area">
                <main class="mwordstar-block">
                    <header class="entry-header border-bottom">
                        <h2 class="entry-title p-name">
                            <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                        </h2>
                    </header>
                    <div class="article-info clearfix border-bottom border-top" role="group" aria-label="页面信息">
                        <!--时间-->
                        <div class="info">
                            <i class="icon-calendar icon" aria-hidden="true"></i>
                            <span data-toggle="tooltip" data-placement="top" title="发布日期">
                                <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y年m月d日'); ?></time>
                            </span>
                        </div>
                        <!--作者-->
                        <div class="info">
                            <i class="icon-user icon" aria-hidden="true"></i>
                            <a data-toggle="tooltip" data-placement="top" href="<?php $this->author->permalink(); ?>" title="作者"><?php $this->author(); ?></a>
                        </div>
                        <!--阅读量-->
                        <div class="info">
                            <i class="icon-eye icon" aria-hidden="true"></i>
                            <?php $views = postViews($this); ?>
                            <span data-toggle="tooltip" data-placement="top" title="访问量"><?php echo $views; ?></span>
                        </div>
                    </div>
                    <article>
                        <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content">
                            <h2>基本统计</h2>
                            <p>下面是网站的基本数据统计：</p>
                            <?php Typecho_Widget::widget('Widget_Stat')->to($quantity); ?>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php $quantity->publishedPostsNum(); ?></h3>
                                        <h4 class="text-center mb-0">文章数</h4>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php $quantity->publishedCommentsNum(); ?></h3>
                                        <h4 class="text-center mb-0">评论数</h4>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo categoryCount(); ?></h3>
                                        <h4 class="text-center mb-0">分类数</h4>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo tagCount(); ?></h3>
                                        <h4 class="text-center mb-0">标签数</h4>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo viewsCount(); ?></h3>
                                        <h4 class="text-center mb-0">文章阅读量</h4>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo agreeCount(); ?></h3>
                                        <h4 class="text-center mb-0">获赞数</h4>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h2>分类占比</h2>
                            <?php if (empty($categoryPostCount)): ?>
                                <p>目前暂无分类数据</p>
                            <?php else: ?>    
                                <p>下面是个分类的文章占比：</p>
                                <div id="category-chart" style="height: 390px;"></div>
                            <?php endif; ?>
                            <hr>
                            <h2>文章更新</h2>
                            <p>下面是 <?php echo date('Y年m月d日', time() - 20736000); ?> 到 <?php echo date('Y年m月d日', time()); ?> 的文章更新情况</p>
                            <div id="post-chart" style="height: 250px;"></div>
                            <hr>
                            <h2>评论动态</h2>
                            <p>下面是 <?php echo date('Y年m月d日', time() - 20736000); ?> 到 <?php echo date('Y年m月d日', time()); ?> 的评论动态</p>
                            <div id="comment-chart" style="height: 250px;"></div>
                            <hr>
                            <h2>最多阅读的文章</h2>
                            <?php $top5Post = top5post(); ?>
                            <?php if (count($top5Post)): ?>
                                <p>下面是阅读量排名前 <?php echo count($top5Post); ?> 的 <?php echo count($top5Post); ?> 篇文章</p>
                                <table>
                                    <thead>
                                    <tr>
                                        <th>排名</th>
                                        <th>文章</th>
                                        <th>阅读量</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $top = 1; ?>
                                    <?php foreach ($top5Post as $post): ?>
                                        <tr>
                                            <td><?php echo $top; ?></td>
                                            <td><a href="<?php echo $post['link']; ?>"><?php echo $post['title']; ?></a></td>
                                            <td><?php echo $post['views']; ?></td>
                                        </tr>
                                        <?php $top ++; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>目前没有任何文章</p>
                            <?php endif; ?>    
                            <hr>
                            <h2>最多评论的文章</h2>
                            <?php $top5CommentPost = top5CommentPost(); ?>
                            <?php if (count($top5CommentPost)): ?>
                                <p>下面是评论数排名前 <?php echo count($top5CommentPost); ?> 的 <?php echo count($top5CommentPost); ?> 篇文章：</p>
                                <table>
                                    <thead>
                                    <tr>
                                        <th>排名</th>
                                        <th>文章</th>
                                        <th>评论数</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $top = 1; ?>
                                    <?php foreach ($top5CommentPost as $post): ?>
                                        <tr>
                                            <td><?php echo $top; ?></td>
                                            <td><a href="<?php echo $post['link']; ?>"><?php echo $post['title']; ?></a></td>
                                            <td><?php echo $post['commentsNum']; ?></td>
                                        </tr>
                                        <?php $top ++; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>目前没有任何文章</p>
                            <?php endif; ?>    
                        </div>
                    </article>
                    <?php $this->need('components/comments.php'); ?>
                </main>
            </div>
            <?php $this->need('components/sidebar.php'); ?>
        </div>
        <script type="text/javascript">
          var data = {
            post: <?php echo json_encode($postCalendarData); ?>,
            comment: <?php echo json_encode($commentCalendarData); ?>,
            category: <?php echo json_encode($categoryPostCount); ?>
          };
          if (data.category.length !== undefined && data.category.length < 1) data.category = undefined;
        </script>
        <?php $id = $this->options->pjax == 'on' ? '?id=' . mt_rand(1, 99999) : ''; ?>
        <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/chart.js'); ?><?php echo $id; ?>"></script>
    </div>
</div>
<?php $this->need('components/footer.php'); ?>