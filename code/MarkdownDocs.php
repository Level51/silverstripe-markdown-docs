<?php

class MarkdownDocs extends LeftAndMain implements PermissionProvider {

    private static $url_segment = 'docs';

    private static $menu_title = 'Documents';

    private static $menu_icon = 'markdown-docs/img/documents.png';

    private static $menu_priority = -1;

    /**
     * @param null $id     Not used.
     * @param null $fields Not used.
     *
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {

        // Get markdown docs and flags from config
        $docs = Config::inst()->get(self::class, 'docs');
        $doTransformNames = Config::inst()->get(self::class, 'transform_names');

        // Bypass whole logic if there are no docs configured
        if (!is_array($docs) ||
            empty($docs)) {
            return parent::getEditForm($id, $fields);
        }

        // Build Tabs with LiteralFields
        $parsedown = new Parsedown();
        $tabs = array_map(function ($doc) use ($doTransformNames, $parsedown) {
            $documentData = $this->getDocumentData($doc);
            $realPath = realpath("../" . $documentData['path']);
            $rawContent = file_get_contents($realPath);
            $rawContent = $this->transformImageLinks($rawContent, 'smb-backend/docs');

            $content = $parsedown->text($rawContent);
            if ($doTransformNames === true && is_string($doc)) {
                $documentData['name'] = ucfirst(mb_strtolower($documentData['name']));
            }

            return new Tab($documentData['name'], LiteralField::create(null, $content));
        }, $docs);

        // Set TabSet
        $tabSet = new TabSet("Root");
        foreach ($tabs as $tab) {
            $tabSet->push($tab);
        }
        $fields = new FieldList($tabSet);

        // Create and config form
        $form = CMSForm::create(
            $this, 'EditForm', $fields, FieldList::create()
        )->addExtraClass('cms-content center cms-edit-form');
        if ($form->Fields()->hasTabset()) $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        return $form;
    }

    /**
     * Generates a unified form of document data.
     *
     * @param $document
     *
     * @return array "path" (from root) and "name"
     */
    private function getDocumentData($document) {
        if (!is_array($document)) {
            return [
                'path' => "$document",
                'name' => substr($document, 0, strlen($document) - 3)
            ];
        }

        return $document[array_keys($document)[0]];
    }

    /**
     * Replaces relative image links with absolute ones.
     *
     * @param $rawContent
     * @param $path
     *
     * @return null|string|string[]
     */
    private function transformImageLinks($rawContent, $path) {
        return preg_replace_callback(
            "/\!\[(.+)\]\((.*\.(jpg|jpeg|png|gif))\)/",
            function ($matches) use ($path) {
                return "![" . $matches[1] . "](" . $path . DIRECTORY_SEPARATOR . $matches[2] . ")";
            },
            $rawContent);
    }

    public function providePermissions() {
        $permissions = parent::providePermissions();
        $permissions['CMS_ACCESS_' . self::class] = [
            'name'     => _t('MarkdownDocs.CMS_ACCESS'),
            'category' => _t('Permission.CMS_ACCESS_CATEGORY')
        ];

        return $permissions;
    }
}
