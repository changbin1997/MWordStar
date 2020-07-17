<?php $color = color($this->options->color); ?>
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
    <button type="button" class="btn to-top rounded-circle d-none <?php echo $color['link']; ?>" title="返回顶部" aria-label="返回顶部">
        <i class="icon-arrow-up"></i>
    </button>
<?php endif; ?>
<?php if ($this->options->bodyHTML): ?>
    <?php $this->options->bodyHTML(); ?>
<?php endif; ?>
<?php $this->footer(); ?>
</body>
</html>
