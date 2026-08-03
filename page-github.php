<?php
/**
 * Github项目展示
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$GLOBALS['page'] = 'page-github';
// 让主题使用的时区跟随 Typecho 设置的时区
setTimezoneByOffset($this->options->timezone);
// 语言初始化
languageInit($this->options->language);

$this->need('components/header.php');
?>

<div id="main">
    <div class="container github-page main-content mb-0">
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
            <div class="archive col-md-12 col-lg-8 col-sm-12 content-area">
                <main class="mwordstar-block">
                    <header class="entry-header border-bottom">
                        <h2 class="entry-title p-name">
                            <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                        </h2>
                    </header>
                    <article>
                        <div class="post-content">
                            <?php echo addBootstrapTableClasses($this->content); ?>

                            <?php if ($this->options->githubUserName): ?>
                                <span id="github-username" style="display: none;" data-user="<?php $this->options->githubUserName(); ?>"></span>
                                <div class="row mb-3" id="repository-list">
                                    <div class="col-12 loading-animation mb-3">
                                        <div class="spinner-border spinner-border-sm mr-2" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <span><?php echo $GLOBALS['t']['loadMore']['loading']; ?></span>
                                    </div>
                                </div>
                                <button type="button" class="btn load-more-repository-btn btn-block mb-4 btn-sm" style="display: none;">
                                    <?php echo $GLOBALS['t']['loadMore']['loadMore']; ?>
                                </button>
                            <?php else: ?>
                                <!--没有填写github用户名-->
                                <div class="mb-3" id="repository-list">
                                    <div role="alert" class="alert"><?php echo $GLOBALS['t']['githubPage']['githubUsernameIsnotConfigured']; ?></div>
                                </div>
                            <?php endif; ?>

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