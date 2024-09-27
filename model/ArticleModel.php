<?php

class ArticleModel extends \Models\ModelExtends
{
    const PAGE_LIMIT = 25;
    const STATUS_SHOW = 0;
    const STATUS_HIDE = 1;

    public function createTable()
    {
        // TODO
    }

    public function __construct($id = null)
    {
        parent::__construct('articles', $id);
    }

    public function count($statuses = [])
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE 1 { AND `status` IN (?a) }", $this->table, empty($statuses) ? DBSIMPLE_SKIP : $statuses);
    }

    private function paramAsArray($value)
    {
        $ret = $value;
        return $ret ? json_decode($ret, true) : [];
    }

    public function __get($key)
    {
        switch ($key) {
            case 'desc':
                $ret = TextWithMaxLen($this->text, 250);
                break;
            case 'author_info':
                $avatar_dir = str_replace(BASEPATH, '', Config(['uploader', 'users', 'upload_path']));
                $ret        = [
                    'name'   => $this->author_name,
                    'avatar' => "{$avatar_dir}{$this->author_avatar}"
                ];
                break;
            case 'tags__asArray':
                $ret = $this->paramAsArray($this->tags);
                break;
            case 'preview_image_url':
                $preview_image_uri = str_replace(BASEPATH, '', Config(['uploader', 'articles', 'upload_path']));
                $ret               = Root("{$preview_image_uri}{$this->preview_image}");
                break;
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }

    public function getAllPossibleTags()
    {
        $tags = $this->db->selectCol("SELECT `tags` FROM ?#", $this->table);
        $tags = empty($tags) ? [] : $tags;
        $ret  = [];
        foreach ($tags as $group) {
            foreach (json_decode($group) as $tag) {
                $ret[$tag] = $tag;
            }
        }
        return $ret;
    }
}