<?php

/**
 * MWordStar 主题 - 数据统计与查询
 *
 * 包含函数：
 *  - postViews                         文章阅读量统计
 *  - agreeNum                          点赞数与是否已赞
 *  - agree                             点赞操作
 *  - categoryCount                     分类数量
 *  - tagCount                          标签数量
 *  - viewsCount                        总阅读量
 *  - agreeCount                        总点赞数
 *  - top5post                          阅读量 Top5 文章
 *  - top5CommentPost                   评论数 Top5 文章
 *  - postCalendar                      文章更新日历数据
 *  - commentCalendar                   评论动态日历数据
 *  - categoryPostCount                 各分类文章数量
 *  - getParentCategory                 父分类名称
 *  - getAdminInfo                      管理员用户信息
 *
 * @package MWordStar
 */

/**
 * 设置文章阅读量
 *
 * @param object $archive 文章
 * @return int 返回阅读量
 */
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

/**
 * 获取点赞数量
 *
 * @param int $cid 文章的cid
 * @return array 返回点赞数量和文章是否被点赞过
 */
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

/**
 * 点赞
 *
 * @param int $cid 文章的cid
 * @return mixed 返回赞数
 */
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

/**
 * 获取文章分类数量
 *
 * @return int 返回文章分类数量
 */
function categoryCount() {
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select('COUNT(*) AS cnt')->from('table.metas')->where('type = ?', 'category')
    );

    if (!$row) return 0;
    return (int) ($row['cnt'] ?? $row['COUNT(*)'] ?? $row['count'] ?? 0);
}

/**
 * 获取标签数量
 *
 * @return int 返回标签数量
 */
function tagCount() {
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select('COUNT(*) AS cnt')->from('table.metas')->where('type = ?', 'tag')
    );

    if (!$row) return 0;
    return (int) ($row['cnt'] ?? $row['COUNT(*)'] ?? $row['count'] ?? 0);
}

/**
 * 获取总阅读量
 *
 * @return int 返回总阅读量
 */
function viewsCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(views) AS viewsCount')->from('table.contents'));
    if ($count['viewsCount'] == null) $count['viewsCount'] = 0;
    return $count['viewsCount'];
}

/**
 * 获取总点赞数
 *
 * @return int 返回总点赞数
 */
function agreeCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(agree) AS agreeCount')->from('table.contents'));
    if ($count['agreeCount'] == null) $count['agreeCount'] = 0;
    return $count['agreeCount'];
}

/**
 * 获取阅读量排名前 5 的 5 篇文章的信息
 *
 * @return array 返回阅读量排名前5的文章标题、链接、阅读量
 */
function top5post() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('views', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList =array();
    foreach ($top5Post as $post) {
        // 生成文章链接
        $permalink = Typecho_Common::url(Typecho_Router::url('post', $post), Helper::options()->index);
        $postList[] = array(
            'title' => $post['title'],
            'link' => $permalink,
            'views' => $post['views']
        );
    }
    return $postList;
}

/**
 * 获取评论数排名前 5 的 5 篇文章的信息
 *
 * @return array 返回评论数排名前5的文章标题、链接、评论数
 */
function top5CommentPost() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('commentsNum', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList = array();
    foreach ($top5Post as $post) {
        // 生成文章链接
        $permalink = Typecho_Common::url(Typecho_Router::url('post', $post), Helper::options()->index);
        $postList[] = array(
            'title' => $post['title'],
            'link' => $permalink,
            'commentsNum' => $post['commentsNum']
        );
    }
    return $postList;
}

/**
 * 获取 ECharts 格式要求的文章更新日历
 *
 * @param int $start 起始时间戳
 * @param int $end 结束时间戳
 * @return array 返回用于日历的文章更新数据
 */
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
        $dateList[] = array(
            $key[$i],
            $dateList2[$key[$i]]
        );
    }

    return $dateList;
}

/**
 * 获取 ECharts 格式要求的评论更新日历
 *
 * @param int $start 起始时间戳
 * @param int $end 结束时间戳
 * @return array 返回用于日历的评论动态数据
 */
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
        $dateList[] = array(
            $key[$i],
            $dateList2[$key[$i]]
        );
    }

    return $dateList;
}

/**
 * 获取每个分类的文章数量
 *
 * @return array 返回每个分类的文章数量
 */
function categoryPostCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchAll($db->select('name', 'count AS value')->from('table.metas')->where('type = ?', 'category'));
    if (count($count) < 1) {
        return array();
    }
    return $count;
}

/**
 * 获取父分类的名称
 *
 * @param int $categoryId 分类id
 * @return string 返回父分类的名称
 */
function getParentCategory($categoryId) {
    $db = Typecho_Db::get();
    $category = $db->fetchRow($db->select()->from('table.metas')->where('mid = ?', $categoryId));
    return $category['name'];
}

/**
 * 获取网站管理员的用户信息
 *
 * @return object 管理员用户信息
 */
function getAdminInfo() {
    $db = Typecho_Db::get();
    $userInfo = $db->fetchRow($db->select('mail', 'url', 'screenName', 'created')->from('table.users')->where('group = ?', 'administrator'));
    return $userInfo;
}
