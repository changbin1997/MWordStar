<?php
/**
 * 网站数据
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 让主题使用的时区跟随 Typecho 设置的时区
setTimezoneByOffset($this->options->timezone);
// 文章更新日历数据
$postCalendarData = postCalendar(time() - 20736000, time());
// 评论更新日历数据
$commentCalendarData = commentCalendar(time() - 20736000, time());
// 获取分类数据
$categoryPostCount = categoryPostCount();

$GLOBALS['page'] = 'page-data';

// 语言初始化
languageInit($this->options->language);
$this->need('components/header.php');
?>

<div id="main">
    <div class="container data-page main-content mb-0">
        <?php if ($this->options->breadcrumb == 'on'): ?>
            <!--面包屑导航-->
            <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="breadcrumb-nav">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a>
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
                    <div class="article-info clearfix border-bottom border-top" role="group" aria-label="<?php echo $GLOBALS['t']['post']['postInfo']; ?>">
                        <!--时间-->
                        <div class="info">
                            <i class="icon-calendar icon" aria-hidden="true"></i>
                            <span data-toggle="tooltip" data-placement="top" title="<?php echo $GLOBALS['t']['post']['publicationDate']; ?>">
                                <time datetime="<?php echo date('c', $this->created); ?>"><?php echo postDateFormat($this->created); ?></time>
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
                    </div>
                    <article>
                        <div data-target="<?php $this->options->postLinkOpen(); ?>" class="post-content">
                            <h2><?php echo $GLOBALS['t']['dataPage']['basicStatistics']; ?></h2>
                            <p><?php echo $GLOBALS['t']['dataPage']['basicStatisticsDescription']; ?></p>
                            <?php Typecho_Widget::widget('Widget_Stat')->to($quantity); ?>
                            <div class="row">
                                <!--文章数-->
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php $quantity->publishedPostsNum(); ?></h3>
                                        <h4 class="text-center mb-0"><?php echo $GLOBALS['t']['dataPage']['totalPosts']; ?></h4>
                                    </div>
                                </div>
                                <!--评论数-->
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php $quantity->publishedCommentsNum(); ?></h3>
                                        <h4 class="text-center mb-0"><?php echo $GLOBALS['t']['dataPage']['totalComments']; ?></h4>
                                    </div>
                                </div>
                                <!--分类数-->
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo categoryCount(); ?></h3>
                                        <h4 class="text-center mb-0"><?php echo $GLOBALS['t']['dataPage']['categories']; ?></h4>
                                    </div>
                                </div>
                                <!--标签数-->
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo tagCount(); ?></h3>
                                        <h4 class="text-center mb-0"><?php echo $GLOBALS['t']['dataPage']['tags']; ?></h4>
                                    </div>
                                </div>
                                <!--文章阅读量-->
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo viewsCount(); ?></h3>
                                        <h4 class="text-center mb-0"><?php echo $GLOBALS['t']['dataPage']['totalViews']; ?></h4>
                                    </div>
                                </div>
                                <!--获赞数-->
                                <div class="col-lg-4 col-md-4 col-sm-6 col-6 pb-3">
                                    <div class="py-3 statistics-card">
                                        <h3 class="text-center mb-2"><?php echo agreeCount(); ?></h3>
                                        <h4 class="text-center mb-0"><?php echo $GLOBALS['t']['dataPage']['totalLikes']; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <!--分类占比图-->
                            <h2><?php echo $GLOBALS['t']['dataPage']['categoryDistribution']; ?></h2>
                            <?php if (empty($categoryPostCount)): ?>
                                <p><?php echo $GLOBALS['t']['dataPage']['NoCategoryDataAvailableAtTheMoment']; ?></p>
                            <?php else: ?>    
                                <p><?php echo $GLOBALS['t']['dataPage']['categoryDistributionDescription']; ?></p>
                                <div id="category-chart" style="height: 320px;"></div>
                            <?php endif; ?>
                            <hr>
                            <!--文章更新日历图-->
                            <h2><?php echo $GLOBALS['t']['dataPage']['postUpdates']; ?></h2>
                            <p><?php printf($GLOBALS['t']['dataPage']['postUpdateDescription'], postDateFormat(time() - 20736000), postDateFormat(time())); ?></p>
                            <div id="post-chart" style="height: 180px;"></div>
                            <hr>
                            <!--评论动态日历图-->
                            <h2><?php echo $GLOBALS['t']['dataPage']['commentActivity']; ?></h2>
                            <p><?php printf($GLOBALS['t']['dataPage']['commentActivityDescription'], postDateFormat(time() - 20736000), postDateFormat(time())); ?></p>
                            <div id="comment-chart" style="height: 180px;"></div>
                            <hr>
                            <!--最多阅读的文章表格-->
                            <h2><?php echo $GLOBALS['t']['dataPage']['mostViewedPosts']; ?></h2>
                            <?php $top5Post = top5post(); ?>
                            <?php if (count($top5Post)): ?>
                                <p><?php printf($GLOBALS['t']['dataPage']['mostViewedPostDescription'], count($top5Post)); ?></p>
                                <table>
                                    <thead>
                                    <tr>
                                        <th><?php echo $GLOBALS['t']['dataPage']['rank']; ?></th>
                                        <th><?php echo $GLOBALS['t']['dataPage']['title']; ?></th>
                                        <th><?php echo $GLOBALS['t']['dataPage']['views']; ?></th>
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
                                <p><?php echo $GLOBALS['t']['dataPage']['NoPostsAvailableAtTheMoment']; ?></p>
                            <?php endif; ?>    
                            <hr>
                            <!--最多评论的文章表格-->
                            <h2><?php echo $GLOBALS['t']['dataPage']['mostCommentedPosts']; ?></h2>
                            <?php $top5CommentPost = top5CommentPost(); ?>
                            <?php if (count($top5CommentPost)): ?>
                                <p><?php printf($GLOBALS['t']['dataPage']['mostCommentedPostDescription'], count($top5CommentPost)); ?></p>
                                <table>
                                    <thead>
                                    <tr>
                                        <th><?php echo $GLOBALS['t']['dataPage']['rank']; ?></th>
                                        <th><?php echo $GLOBALS['t']['dataPage']['title']; ?></th>
                                        <th><?php echo $GLOBALS['t']['dataPage']['comments']; ?></th>
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
                                <p><?php echo $GLOBALS['t']['dataPage']['NoPostsAvailableAtTheMoment']; ?></p>
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