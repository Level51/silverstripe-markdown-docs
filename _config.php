<?php
Config::inst()->update('LeftAndMain', 'extra_requirements_css', array(
        basename(dirname(__FILE__)) . '/css/markdown-docs.css'
    )
);
