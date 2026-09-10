<?php

/**
 * MWordStar 主题 - 主题初始化与环境
 *
 * 包含函数：
 *  - themeInit                         主题初始化钩子（验证码 action 分发、注册置顶 query 钩子）
 *  - themePinnedPostHandle             首页置顶文章处理
 *  - checkField                        检查 / 补齐 contents 表的 views、agree 字段
 *  - setTimezoneByOffset               根据秒数偏移量设置全局时区
 *
 * @package MWordStar
 */

/**
 * 主题初始化钩子
 *
 * @param Widget_Archive $archive 归档对象
 * @return void
 */
function themeInit($archive) {
    // 输出评论图片验证码
    if ((isset($_GET['action']) && $_GET['action'] == 'captcha') || (isset($_POST['action']) && $_POST['action'] == 'captcha')) {
        commentCaptchaImage();
        // 输出 JSON 后立即终止执行，避免把整个页面内容附加到验证码响应里
        exit;
    }

    // 在实际执行首页列表查询前触发。
    if ($archive->is('index')) {
        Typecho_Plugin::factory('Widget_Archive')->query = 'themePinnedPostHandle';
    }
}

/**
 * 处理文章置顶逻辑
 *
 * 从文章自定义字段中读取置顶文章，并将置顶文章优先压入输出栈。
 *
 * @param Widget_Archive $archive 归档对象
 * @param Typecho_Db_Query $select 数据库查询对象
 * @return void
 */
function themePinnedPostHandle($archive, $select) {
    $db = Typecho_Db::get();
    // 读取所有在文章编辑页标记为置顶的文章 CID。
    // 未发布文章和非文章内容不会进入首页列表。
    $pinnedPosts = $db->fetchAll(
        $db->select('table.fields.cid')
            ->from('table.fields')
            ->join('table.contents', 'table.contents.cid = table.fields.cid')
            ->where('name = ?', 'pinned')
            ->where('str_value = ?', 'on')
            ->order('table.contents.created', Typecho_Db::SORT_DESC)
    );

    $page = $archive->request->get('page', 1);

    foreach ($pinnedPosts as $pinnedPost) {
        if (empty($pinnedPost['cid']) || !is_numeric($pinnedPost['cid'])) {
            continue;
        }

        $cid = intval($pinnedPost['cid']);
        $post = $db->fetchRow($archive->select()->where('table.contents.cid = ?', $cid));

        if ($post) {
            // 仅在首页第一页追加置顶文章
            if ($page == 1) {
                // 注入置顶状态标识，供前端多语言判断使用
                $post['is_pinned'] = true;
                $archive->push($post);
            }
            // 从常规查询条件中排除该文章，避免列表重复输出
            $select->where('table.contents.cid != ?', $cid);
        }
    }
    // 挂载 query 钩子后，Typecho 会跳过默认的列表查询，
    // 因此这里需要手动执行查询并把常规文章追加到输出栈。
    $db->fetchAll($select, array($archive, 'push'));
}

/**
 * 检查数据库字段
 *
 * @return void
 */
function checkField() {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $adapter = $db->getAdapterName(); // 获取数据库驱动名称
    // 要检查的字段
    $fields = [
        'views' => 'INT DEFAULT 0 NOT NULL',
        'agree' => 'INT DEFAULT 0 NOT NULL'
    ];

    foreach ($fields as $colName => $colAttr) {
        $needAdd = true;
        // 针对 PostgreSQL 的特殊处理
        if (strpos($adapter, 'Pgsql') !== false) {
            // 查询 information_schema 检查字段是否存在
            $check = $db->fetchRow($db->select()->from('information_schema.columns')->where('table_name = ?', $prefix . 'contents')->where('column_name = ?', $colName));
            if (!empty($check)) {
                $needAdd = false; // 字段已存在，无需添加
            }
        }

        if ($needAdd) {
            try {
                // 根据数据库类型调整 SQL 语法
                if (strpos($adapter, 'Pgsql') !== false) {
                    // PostgreSQL: 使用双引号，移除 INT(10) 的长度限制（PgSQL不支持）
                    $pgAttr = str_replace('INT(10)', 'INTEGER', $colAttr);
                    $sql = 'ALTER TABLE "' . $prefix . 'contents" ADD COLUMN "' . $colName . '" ' . $pgAttr . ';';
                } else {
                    // MySQL / SQLite: 保持原有语法 (使用反引号)
                    $sql = 'ALTER TABLE `' . $prefix . 'contents` ADD `' . $colName . '` ' . $colAttr . ';';
                }

                $db->query($sql);
            } catch (Typecho_Db_Exception $e) {
                // 忽略错误
            }
        }
    }
}

/**
 * 根据秒数偏移量设置全局时区
 * * @param int|string $offset Typecho 格式的时区偏移量 (例如: "28800" 或 28800)
 * @return void
 */
function setTimezoneByOffset($offset) {
    // 强制转换为整数
    $offset = (int) $offset;

    // 尝试根据偏移量获取合法的时区名称 (例如 "Asia/Shanghai" 或 "Etc/GMT-8")
    $timezone_name = timezone_name_from_abbr('', $offset, 0);
    // 如果获取失败（极少数情况），或者获取到的是 false
    if ($timezone_name === false) {
        // 手动回退逻辑：构建 Etc/GMT 时区
        $hours = $offset / 3600;
        if ($hours > 0) {
            $timezone_name = 'Etc/GMT-' . $hours;
        } else {
            $timezone_name = 'Etc/GMT+' . abs($hours);
        }
    }

    // 设置全局时区
    @date_default_timezone_set($timezone_name);
}

/**
 * 兼容 Typecho 1.1 ~ 1.3 的字符串截断函数
 *
 * Typecho 1.2 起将 Typecho_Common 重命名为 Typecho\Common，
 * 此处根据当前运行版本动态选择可用的类，保证新旧版本都能正常调用。
 *
 * @param string $str 需要截取的字符串
 * @param integer $start 开始截取的位置
 * @param integer $length 需要截取的长度
 * @param string $trim 截取后的截断标示符
 * @return string
 */
function themeCommonSubStr($str, $start, $length, $trim = '...') {
    // 摘要长度选项未配置时可能为 null，先归一化为正整数（与 theme-config 默认摘要字数一致）
    $length = (int) $length;
    if ($length <= 0) {
        $length = 150;
    }

    if (class_exists('\\Typecho\\Common')) {
        return \Typecho\Common::subStr((string) $str, (int) $start, $length, (string) $trim);
    }
    return Typecho_Common::subStr((string) $str, (int) $start, $length, (string) $trim);
}
