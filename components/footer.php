<footer>
    <div class="container">
        <?php if ($this->options->icp): ?>
            <div>
                <span><?php $this->options->icp(); ?></span>
            </div>
        <?php endif; ?>
        <span>Powered by <a href="http://www.typecho.org/" target="_blank">Typecho</a> Theme by <a href="https://github.com/changbin1997/MWordStar" target="_blank">MWordStar</a></span>
    </div>
</footer>
<?php if ($this->options->toTop == 'show'): ?>
    <button type="button" class="btn to-top rounded-circle d-none" title="返回顶部" aria-label="返回顶部">
        <i class="icon-arrow-up"></i>
    </button>
<?php endif; ?>

<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/jquery-3.4.1.min.js'); ?>"></script>
<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/bootstrap.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/highlight.pack.js'); ?>"></script>
<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/jquery.qrcode.min.js'); ?>"></script>
<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/clipboard.min.js'); ?>"></script>
<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/app.js'); ?>"></script>
<!--统计数据的图表js-->
<?php if (isset($GLOBALS['page']) && $GLOBALS['page'] == 'page-data'): ?>
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/chart.js'); ?>"></script>
<?php endif; ?>

<?php if ($this->options->bodyHTML): ?>
    <?php $this->options->bodyHTML(); ?>
<?php endif; ?>
<?php $this->footer(); ?>
</body>
</html>
