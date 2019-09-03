<footer>
    <div class="container">
        <?php if ($this->options->icp): ?>
            <div>
                <span><?php $this->options->icp(); ?></span>
            </div>
        <?php endif; ?>
        <span>Powered by <a href="http://www.typecho.org/" target="_blank">Typecho</a> Theme by <a href="https://www.misterma.com/archives/812/" target="_blank">MWordStar</a></span>
    </div>
</footer>
<?php if ($this->options->bodyHTML): ?>
    <?php $this->options->bodyHTML(); ?>
<?php endif; ?>
</body>
</html>