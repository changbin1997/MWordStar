<?php
/**
 * 文章归档
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="container archive-page">
    <div class="row">
        <div class="archive col-md-12 col-lg-8 col-sm-12">
            <main>
                <header class="entry-header">
                    <h2 class="entry-title p-name">
                        <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                    </h2>
                </header>
                <article>
                    <div class="post-content" itemprop="articleBody">
                        <?php
                        $stat = Typecho_Widget::widget('Widget_Stat');
                        Typecho_Widget::widget('Widget_Contents_Post_Recent', 'pageSize='.$stat->publishedPostsNum)->to($archives);
                        $year=0; $mon=0; $i=0; $j=0;
                        $output = '<div class="archives">';
                        while($archives->next()){
                            $year_tmp = date('Y',$archives->created);
                            $mon_tmp = date('m',$archives->created);
                            $y=$year; $m=$mon;
                            if ($year > $year_tmp || $mon > $mon_tmp) {
                                $output .= '</ul></div>';
                            }
                            if ($year != $year_tmp || $mon != $mon_tmp) {
                                $year = $year_tmp;
                                $mon = $mon_tmp;
                                $output .= '<div class="archives-item"><h4>'.date('Y年m月',$archives->created).'</h4><hr><ul class="archives_list">'; //输出年份
                            }
                            $output .= '<li>'.date('d日',$archives->created).' <a href="'.$archives->permalink .'">'. $archives->title .'</a></li>'; //输出文章
                        }
                        $output .= '</ul></div></div>';
                        echo $output;
                        ?>
                    </div>
                </article>
            </main>
        </div>
        <?php $this->need('sidebar.php'); ?>
    </div>
</div>
<?php $this->need('footer.php'); ?>