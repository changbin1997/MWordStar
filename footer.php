<footer>
    <div class="container">
        <?php if ($this->options->homeLinks): ?>
            <nav aria-label="友情链接" role="navigation">
                <h5>友情链接</h5>
                <?php $links = json_decode($this->options->homeLinks); ?>
                <?php for ($i = 0;$i < count($links);$i ++): ?>
                    <a target="_blank" href="<?php echo $links[$i]->url; ?>" title="<?php echo $links[$i]->title; ?>"><?php echo $links[$i]->name; ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
        <span>Powered by <a href="http://www.typecho.org/" target="_blank">Typecho</a> Theme by <a href="https://www.misterma.com" target="_blank">MWordStar</a></span>
    </div>
</footer>
<?php if ($this->options->bodyHTML): ?>
    <?php $this->options->bodyHTML(); ?>
<?php endif; ?>
</body>
</html>