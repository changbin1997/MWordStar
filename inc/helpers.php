<?php

// 检测是否是QQ邮箱
function isQQEmail($email) {
    $re = '/^\d{6,11}\@qq\.com$/';
    preg_match($re, $email, $result);
    if (count($result)) {
        return true;
    }
    return false;
}

// 获取QQ头像
function QQAvatar($email, $name, $size) {
    $qq = str_replace('@qq.com', '', $email);  // 获取QQ号
    $imgUrl = 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qq . '&spec=' . $size;
    echo '<img src="' . $imgUrl . '" alt="' . $name . '" class="avatar">';
}

// 检查数据库字段
function checkField() {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 获取文章表的字段
    $postFields = $db->fetchAll($db->query('PRAGMA table_info(' . $prefix . 'contents)'));
    // 如果阅读量的字段不存在就创建字段
    if (array_search('views', array_column($postFields, 'name')) == false) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) NOT NULL DEFAULT 0;');
    }

    // 如果点赞字段不存在就创建字段
    if (array_search('agree', array_column($postFields, 'name')) == false) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `agree` INT(10) NOT NULL DEFAULT 0;');
    }
}

// 设置文章阅读量
function postViews($archive) {
    // 获取文章的 cid
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    // 查询出阅读量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    // 是否是内容页
    if ($archive->is('single')) {
        // 获取阅读 cookie
        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            $views = array();
        } else {
            $views = explode(',', $views);
        }
        // 如果 cookie 不存在
        if (!in_array($cid, $views)) {
            // 阅读量 +1
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views'] + 1))->where('cid = ?', $cid));
            $views[] = $cid;
            $views = implode(',', $views);
            // 写入阅读 cookie
            Typecho_Cookie::set('extend_contents_views', $views);
            // 返回的最终阅读量 +1
            $row['views'] ++;
        }
    }
    return $row['views'];
}

// 日期格式化
function dateFormat($date, $options = 'format1') {
    if ($options == 'format1') {
        return date('Y年m月d日 H:i', $date);
    }
    if ($options == 'format2') {
        return date('Y-m-d H:i', $date);
    }
    if ($options == 'format3') {
        return date('F jS, Y \a\t h:i a', $date);
    }
    if ($options == 'format4') {
        $time = time() - $date;
        if ($time < 1) {
            return '1秒前';
        }else if ($time < 60) {
            return $time . '秒前';
        }else if ($time > 60 && $time < 3600) {
            return round($time / 60, 0) . '分钟前';
        }else if ($time > 3600 && $time < 86400) {
            return round($time / 3600, 0) . '小时前';
        }else {
            return round($time / 86400, 0) . '天前';
        }
    }
}

// 获取父评论的姓名
function reply($parent) {
    if ($parent == 0) {
        return '';
    }

    $db = Typecho_Db::get();
    $commentInfo = $db->fetchRow($db->select('author,status,mail')->from('table.comments')->where('coid = ?', $parent));
    $link = '<a class="parent" href="#comment-' . $parent . '">@' . $commentInfo['author'] .  '</a>';
    return $link;
}

// 获取点赞数量
function agreeNum($cid) {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $AgreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($AgreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array(0)));
    }

    return array(
        //  点赞数量
        'agree' => $agree['agree'],
        //  文章是否点赞过
        'recording' => in_array($cid, json_decode(Typecho_Cookie::get('typechoAgreeRecording')))?true:false
    );
}

// 点赞
function agree($cid) {
    $db = Typecho_Db::get();
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $agreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($agreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array($cid)));
    }else {
        $agreeRecording = json_decode($agreeRecording);
        //  判断文章是否点赞过
        if (in_array($cid, $agreeRecording)) {
            //  如果当前文章的 cid 在 cookie 中就返回文章的赞数，不再往下执行
            return $agree['agree'];
        }
        array_push($agreeRecording, $cid);
        Typecho_Cookie::set('typechoAgreeRecording', json_encode($agreeRecording));
    }

    $db->query($db->update('table.contents')->rows(array('agree' => (int)$agree['agree'] + 1))->where('cid = ?', $cid));
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    return $agree['agree'];
}

// 生成文章头图背景颜色
function headerImageBgColor($color) {
    if ($color == null or $color == '') {
        return '#CCCCCC';
    }

    $bgColor = array(
        'random' => 'rgb(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ')',
        'gray' => '#CCCCCC',
        'white' => '#FFFFFF'
    );
    return $bgColor[$color];
}

// 获取文章头图显示设置
function headerImageDisplay($t, $options, $defaultImageUrl) {
    // 在文章列表和文章页显示文章头图
    if ($t->fields->headerImgDisplay == 'post-page-list') {
        return postImg($t, $defaultImageUrl);
    }
    // 在文章列表显示文章头图
    if ($t->fields->headerImgDisplay == 'post-list' && $t->is('index') or $t->fields->headerImgDisplay == 'post-list' && $t->is('archive')) {
        return postImg($t, $defaultImageUrl);
    }
    // 在文章页显示文章头图
    if ($t->fields->headerImgDisplay == 'post-page' && $t->is('post') or $t->fields->headerImgDisplay == 'post-page' && $t->is('page')) {
        return postImg($t, $defaultImageUrl);
    }
    // 使用系统文章头图设置
    if ($t->fields->headerImgDisplay == 'default' or $t->fields->headerImgDisplay == null) {
        // 在文章列表显示头图
        if (is_array($options) && in_array('home', $options) && $t->is('index')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在分类页、标签页、归档页的文章列表显示文章头图
        if (is_array($options) && in_array('home', $options) && $t->is('archive')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在文章页显示头图
        if (is_array($options) && in_array('post', $options) && $t->is('post')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在独立页显示文章头图
        if (is_array($options) && in_array('post', $options) && $t->is('page')) {
            return postImg($t, $defaultImageUrl);
        }
    }
    // 不显示文章头图
    if ($t->fields->headerImgDisplay == 'hide') return false;
    return false;
}

// 根据设置获取文章头图
function postImg($a, $defaultUrl) {
    // 手动输入文章头图
    if ($a->fields->imageSource == 'url' && $a->fields->thumb != '') {
        return $a->fields->thumb;
    }
    if ($a->fields->imageSource == 'default') {
        return randomHeaderImage($defaultUrl);
    }
    // 默认使用第一张图片作为文章头图
    $img = getPostImg($a);
    return $img == 'none'?false:$img;
}

// 获取文章的第一张图片
function getPostImg($archive) {

    $img = array();
    preg_match_all("/<img.*?src=\"(.*?)\".*?\/?>/i", $archive->content, $img);
    if (count($img) > 0 && count($img[0]) > 0) {
        $img_url = $img[1][0];
        return $img_url;
    } else {
        return false;
    }
}

// 获取随机文章头图
function randomHeaderImage($imgUrl) {
    if ($imgUrl == null or $imgUrl == '') return false;
    // 把 URL 按行拆分为数组
    $imgUrl = explode(PHP_EOL, $imgUrl);
    // 删除因为空行生成的数组空值
    $imgUrl = array_filter($imgUrl);
    // 如果只有一个 URL 就直接返回 URL
    if (count($imgUrl) < 2) return $imgUrl[0];
    // 随机返回一个 URL
    return $imgUrl[mt_rand(0, count($imgUrl) - 1)];
}

// 获取文章列表的文章头图样式
function getPostListHeaderImageStyle($postStyle, $optionsStyle) {
    if ($postStyle == 'max' or $postStyle == 'mini') {
        return $postStyle;
    }
    if ($postStyle == 'default' or $postStyle == null) {
        if ($optionsStyle == 'max' or $optionsStyle == 'mini') {
            return $optionsStyle;
        }
        return 'max';
    }
    return 'max';
}

// 获取文章列表显示设置
function postListStyle($option, $postOption) {
    // 判断单篇文章的列表显示设置
    if ($postOption == 'summary' or $postOption == 'fullText') {
        return $postOption;
    }
    // 判断列表全局设置
    if ($option == 'fullText' or $option == 'summary') {
        return $option;
    }
    // 如果出现异常就默认显示文章摘要和
    return 'summary';
}

// 根据文章内的标题生成目录
function articleDirectory($content) {
    $re = '#<h(\d)(.*?)>(.*?)</h\d>#im';
    preg_match_all($re, $content, $result);
    if (!is_array($result) or count($result[0]) < 1) {
        return array(
            'content' => $content,
            'directory' => null
        );
    }

    $treeList = array();
    $id = 1;
    foreach ($result[1] as $i => $level) {
        $treeList[$id] = array(
            'id' => $id,
            'parent_id' => 0,
            'level' => $level,
            'name' => trim(strip_tags($result[3][$i])),
            'rand' => mt_rand(1000, 9999)
        );
        $id ++;
    }

    for ($i = 2;$i <= count($treeList);$i ++) {
        $item = $treeList[$i];
        $prevItem = $treeList[$i - 1];
        if ($item['level'] == $prevItem['level']) {
            $treeList[$i]['parent_id'] = $prevItem['parent_id'];
            continue;
        }
        if ($item['level'] > $prevItem['level']) {
            $treeList[$i]['parent_id'] = $prevItem['id'];
            continue;
        }
        $parentId = 0;
        while ($item['level'] <= $prevItem['level']) {
            $parentId = $prevItem['parent_id'];
            if (!isset($treeList[($prevItem['id'] - 1)])) {
                break;
            }
            $prevItem = $treeList[($prevItem['id'] - 1)];
        }
        $treeList[$i]['parent_id'] = $parentId;
    }

    $tree = array();
    foreach ($treeList as $item) {
        if ($item[ 'parent_id' ] != 0 && !isset($treeList[$item['parent_id']])) {
            continue;
        }
        if (isset($treeList[$item['parent_id']])) {
            $treeList[$item['parent_id']]['children'][] = &$treeList[$item['id']];
        } else {
            $tree[] = &$treeList[$item['id']];
        }
    }

    $GLOBALS['directory'] = $treeList;
    $GLOBALS['directoryIndex'] = 1;
    $content = preg_replace_callback($re, function ($matches) {
        $name = urlencode(strip_tags($matches[3]));
        $span = '<span data-title="' . $name . $GLOBALS['directory'][$GLOBALS['directoryIndex']]['rand'] . '" id="' . $name . $GLOBALS['directory'][$GLOBALS['directoryIndex']]['rand'] . '"></span>' . $matches[0];
        $GLOBALS['directoryIndex'] ++;
        return $span;
    }, $content);

    return array(
        'content' => $content,
        'directory' => renderArticleDirectory($tree, '')
    );
}

// 生成目录 HTML
function renderArticleDirectory($tree, $parent = '') {
    $index = 1;
    $ariaLabel = $tree[0]['parent_id'] == 0?'aria-label="目录"':'';
    $htmlStr = '<ul class="article-directory"' . $ariaLabel . '>';
    foreach ($tree as $item) {
        $num = $parent == ''?$index:$parent . '.' . $index;
        $htmlStr .= sprintf('<li class="border-bottom"><a rel="bookmark" data-directory="%s" class="directory-link" href="#%s">%s</a></li>', urlencode($item['name']) . $item['rand'], urlencode($item['name']) . $item['rand'], '<span class="mr-2 directory-num">' . $num . '</span>' . $item['name']);
        if (isset($item['children']) && count($item['children']) > 0) {
            $htmlStr .= renderArticleDirectory($item['children'], $num);
        }
        $index ++;
    }
    $htmlStr .= '</ul>';
    return $htmlStr;
}

// 获取月份
function getMonth() {
    $path = $_SERVER['PHP_SELF'];  // 获取路劲
    preg_match('/\d{4}\/\d{2}\/\d{2}|\d{4}\/\d{2}/', $path, $date);  // 匹配路劲中的日期
    if (is_array($date) && count($date)) {
        $date = explode('/', $date[0]);  // 如果匹配到就分割日期
    }else {
        $date = date('Y/m/d', time());  // 如果没有匹配到就获取当前月
        $date = explode('/', $date);  // 分割日期
    }
    return $date;
}

// 获取指定月份的文章
function getMonthPost() {
    $date = getMonth();  // 获取要查询文章的月份

    $start = $date[0] . '-' . $date[1] . '-01 00:00:00';  // 月的第一天
    $end = date('Y-m-t', strtotime($date[0] . '-' . $date[1] . '-' . '1 23:59:59'));  // 月最后一天
    $start = strtotime($start);  // 把月的第一天转换为时间戳
    $end = strtotime($end . ' 23:59:59');  // 把月的最后一天转换为时间戳
    $db = Typecho_Db::get();
    // 按照提供的月份查询出文件的时间
    $post = $db->fetchAll($db->select('table.contents.created')->from('table.contents')->where('created >= ?', $start)->where('created <= ?', $end)->where('type = ?', 'post')->where('status = ?', 'publish'));
    // 按照提供的月份查询前一个月的文章
    $previous = $db->fetchAll($db->select('table.contents.created')->from('table.contents')->where('created < ?', $start)->where('type = ?', 'post')->where('status = ?', 'publish')->offset(0)->limit(1)->order('created', Typecho_Db::SORT_DESC));
    // 按照提供的月份查询后一个月的文章
    $next = $db->fetchAll($db->select('table.contents.created')->from('table.contents')->where('created > ?', $end)->where('type = ?', 'post')->where('status = ?', 'publish')->offset(0)->limit(1)->order('created', Typecho_Db::SORT_ASC));

    if (count($next)) {
        $next = date('Y/m/', $next[0]['created']);  // 格式化前一个月的文章时间
    }

    if (count($previous)) {
        $previous = date('Y/m/', $previous[0]['created']);  // 格式化后一个月的文章时间
    }

    $day = array();
    foreach ($post as $val) {
        array_push($day, date('j', $val['created']));  // 把查询出的文章日加入数组
    }
    return array(
        'post'=> $day,
        'previous' => $previous,
        'next' => $next
    );
}

// 生成日历
function calendar($month, $url, $rewrite) {
    $monthArr = getMonth();  // 获取月份
    $post = getMonthPost();  // 获取文章日期

    // 判断是否启用了地址重写功能
    if ($rewrite) {
        $monthUrl = $url . $monthArr[0] . '/' . $monthArr[1] . '/';  // 生成日期链接前缀
        $previousUrl = is_array($post['previous'])?'':$url . $post['previous'];  // 生成前一个月的跳转链接地址
        $nextUrl = is_array($post['next'])?'':$url . $post['next'];  // 生成后一个月的跳转链接地址
    }else {
        $monthUrl = $url . 'index.php/' . $monthArr[0] . '/' . $monthArr[1] . '/';  // 生成日期链接前缀
        $previousUrl = is_array($post['previous'])?'':$url . 'index.php/' . $post['previous'];  // 生成前一个月的跳转链接地址
        $nextUrl = is_array($post['next'])?'':$url . 'index.php/' . $post['next'];  // 生成后一个月的跳转链接地址
    }

    $postCount = array_count_values($post['post']);  // 统计每天的文章数量

    $calendar = '';  // 初始化
    $week_arr = ['日', '一', '二', '三', '四', '五', '六'];  // 表头
    $this_month_days = (int)date('t', strtotime($month));  // 本月共多少天
    $this_month_one_n = (int)date('w', strtotime($month));  // 本月1号星期几
    $calendar .= '<table aria-label="' . $monthArr[0] . '年' . $monthArr[1] . '月日历" class="table table-bordered table-sm m-0"><thead><tr>';  // 表头

    foreach ($week_arr as $k => $v){
        if($k == 0){
            $class = ' class="sunday"';
        }elseif ($k == 6){
            $class = ' class="saturday"';
        }else{
            $class = '';
        }
        $calendar .= '<th class="text-center py-2">' . $v . '</th>';
    }
    $calendar .= '</tr></thead><tbody>';
    // 表身
    // 计算本月共几行数据
    $total_rows = ceil(($this_month_days - (7 - $this_month_one_n)) / 7) + 1;
    $number = 1;
    $flag = 0;
    for ($row = 1;$row <= $total_rows;$row++){
        $calendar .= '<tr>';
        for ($week = 0;$week <= 6;$week ++){
            if($number < 10){
                $numbera = '0' . $number;
            }else{
                $numbera = $number;
            }

            if($number <= $this_month_days){
                if ($number < 10) {
                    $zero = '0';
                }else {
                    $zero = '';
                }

                if($row == 1){
                    if($week >= $this_month_one_n){
                        if (in_array($number, $post['post'])) {
                            $calendar .= '<td class="active text-center py-2">' . '<a rel="archives" href="' . $monthUrl . $zero . $number . '/' . '" class="p-0" title="' . $postCount[$number] . '篇文章" data-toggle="tooltip" data-placement="top"><b>' . $number . '</b></a>' . '</td>';
                        }else {
                            $calendar .= '<td class="text-center py-2">' . $number . '</td>';
                        }
                        $flag = 1;
                    }else{
                        $calendar .= '<td></td>';
                    }
                }else{
                    if (in_array($number, $post['post'])) {
                        $calendar .= '<td class="active text-center py-2">' . '<a rel="archives" href="' . $monthUrl . $zero . $number . '/' . '" class="p-0" title="' . $postCount[$number] . '篇文章" data-toggle="tooltip" data-placement="top"><b>' . $number . '</b></a>' . '</td>';
                    }else {
                        $calendar .= '<td class="text-center py-2">' . $number . '</td>';
                    }
                }
                if($flag){
                    $number ++;
                }
            }else{
                $calendar .= '<td></td>';
            }
        }
        $calendar .= '</tr>';
    }

    $calendar .= '</tbody></table>';

    return array(
        'calendar' => $calendar,
        'previous' => is_array($post['previous'])?false:$post['previous'],
        'next' => is_array($post['next'])?false:$post['next'],
        'previousUrl' => $previousUrl,
        'nextUrl' => $nextUrl
    );
}

// 获取分类数量
function categoryCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('COUNT(*)')->from('table.metas')->where('type = ?', 'category'));
    return $count['COUNT(*)'];
}

// 获取标签数量
function tagCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('COUNT(*)')->from('table.metas')->where('type = ?', 'tag'));
    return $count['COUNT(*)'];
}

// 获取总阅读量
function viewsCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(views) AS viewsCount')->from('table.contents'));
    if ($count['viewsCount'] == null) $count['viewsCount'] = 0;
    return $count['viewsCount'];
}

// 获取总点赞数
function agreeCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(agree) AS agreeCount')->from('table.contents'));
    if ($count['agreeCount'] == null) $count['agreeCount'] = 0;
    return $count['agreeCount'];
}

// 获取阅读量排名前 5 的 5 篇文章的信息
function top5post() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('views', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList =array();
    foreach ($top5Post as $post) {
        $post = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($post);
        array_push($postList, array(
            'title' => $post['title'],
            'link' => $post['permalink'],
            'views' => $post['views']
        ));
    }
    return $postList;
}

// 获取评论数排名前 5 的 5 篇文章的信息
function top5CommentPost() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('commentsNum', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList = array();
    foreach ($top5Post as $post) {
        $post = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($post);
        array_push($postList, array(
            'title' => $post['title'],
            'link' => $post['permalink'],
            'commentsNum' => $post['commentsNum']
        ));
    }
    return $postList;
}

// 获取 ECharts 格式要求的文章更新日历
function postCalendar($start, $end) {
    $db = Typecho_Db::get();
    $dateList = $db->fetchAll($db->select('created')->from('table.contents')->where('created > ?', $start)->where('created < ?', $end));
    if (count($dateList) < 1) {
        return array();
    }
    $dateList2 = array();
    foreach ($dateList as $val) {
        array_push($dateList2, date('Y-m-d', $val['created']));
    }
    $dateList2 = array_count_values($dateList2);
    $key = array_keys($dateList2);
    $dateList = array();

    for ($i = 0;$i < count($dateList2);$i ++) {
        array_push($dateList, array(
            $key[$i],
            $dateList2[$key[$i]]
        ));
    }

    return $dateList;
}

// 获取 ECharts 格式要求的评论更新日历
function commentCalendar($start, $end) {
    $db = Typecho_Db::get();
    $dateList = $db->fetchAll($db->select('created')->from('table.comments')->where('created > ?', $start)->where('created < ?', $end));
    if (count($dateList) < 1) {
        return array();
    }
    $dateList2 = array();
    foreach ($dateList as $val) {
        array_push($dateList2, date('Y-m-d', $val['created']));
    }
    $dateList2 = array_count_values($dateList2);
    $key = array_keys($dateList2);
    $dateList = array();

    for ($i = 0;$i < count($dateList2);$i ++) {
        array_push($dateList, array(
            $key[$i],
            $dateList2[$key[$i]]
        ));
    }

    return $dateList;
}

// 获取个分类的文章数量
function categoryPostCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchAll($db->select('name', 'count AS value')->from('table.metas')->where('type = ?', 'category'));
    if (count($count) < 1) {
        return array();
    }
    return $count;
}

// 获取父分类的名称
function getParentCategory($categoryId) {
    $db = Typecho_Db::get();
    $category = $db->fetchRow($db->select()->from('table.metas')->where('mid = ?', $categoryId));
    return $category['name'];
}

// 获取管理员信息
function getAdminInfo() {
    $db = Typecho_Db::get();
    $userInfo = $db->fetchRow($db->select('mail', 'url', 'screenName', 'created')->from('table.users')->where('group = ?', 'administrator'));
    return $userInfo;
}

// 获取 Gravatar 头像
function gravatar($email, $size, $gravatarUrl = '', $alt = '') {
    $url = $gravatarUrl . md5(strtolower(trim($email))) . '?s=' . $size;
    if ($gravatarUrl == '' or $gravatarUrl == null) {
        $url = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size;
    }
    echo '<img src="' . $url . '" alt="' . $alt . '" class="avatar" />';
}

// 计算两个时间之间相差的天数
function getDays($time1, $time2) {
    return floor(($time2 - $time1) / 86400);
}

// 把图片的 src 替换为 data-src，用于图片懒加载
function replaceImgSrc($content) {
    $pattern = '/<img(.*?)src(.*?)=(.*?)"(.*?)">/i';
    $replacement = '<img$1data-src$3="$4"$5 class="load-img">';
    return preg_replace($pattern, $replacement, $content);
}