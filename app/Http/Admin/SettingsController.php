<?php
namespace App\Http\Admin;

class SettingsController extends AdminController
{
    public function seo()
    {
        return $this->render('them/admin/pages/setting/seo.php', ['data' => $this->db->last('seo')]);
    }

    public function saveSeo()
    {
        $this->save('seo', ['logo', 'header'], 'SuccessSeo');
    }

    public function header()
    {
        return $this->render('them/admin/pages/setting/header.php', ['data' => $this->db->last('header')]);
    }

    public function saveHeader()
    {
        $this->save('header', ['img_one', 'img_two', 'img_tree'], 'SuccessHeader');
    }

    public function footer()
    {
        return $this->render('them/admin/pages/setting/footer.php', ['data' => $this->db->last('footer')]);
    }

    public function saveFooter()
    {
        $this->save('footer', ['img_footer'], 'SuccessFooter');
    }

    private function save($table, array $imageFields, $flashKey)
    {
        $data = $this->request->input();
        $id = !empty($data['id']) ? (int) $data['id'] : 0;
        unset($data['id']);

        $current = $id ? $this->db->find($table, $id) : null;

        foreach ($imageFields as $field) {
            $file = $this->request->file($field);
            if ($file && ($saved = $this->uploads->image($file, 'them/admin/dist/img', $table . '-' . $field . '-'))) {
                if ($current && !empty($current[$field])) $this->uploads->remove($current[$field]);
                $data[$field] = $saved;
            }
        }

        if ($id) $this->db->updateById($table, $id, $data);
        else $this->db->insert($table, $data);

        $this->site->clear();
        flash($flashKey, $id ? 'اطلاعات بروز شد' : 'اطلاعات ثبت شد');
        $this->back('admin/settings/' . $table);
    }
}
