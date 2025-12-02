<?php

namespace App\Support;

class HTMLPurifierSupport
{
    private static $purifier;

    /**
     * @return mixed
     */
    public static function clean($value)
    {
        //        return $value;
        return self::getPurifier()->purify($value);
    }

    /**
     * @return \HTMLPurifier
     */
    //Find full HTML5 config : https://github.com/kennberg/php-htmlpurfier-html5
    private static function getPurifier()
    {
        if (is_null(self::$purifier)) {

            //Find full HTML5 config : https://github.com/kennberg/php-htmlpurfier-html5
            // EDIT: modify this to whatever you need.
            $allowed = [
//                'link[href|rel|type]',
                'img[src|alt|title|width|height|style|data-mce-src|data-mce-json|class]',
                'figure', 'figcaption',
                'video[src|type|width|height|poster|preload|controls|class]', 'source[src|type|class]',
                'a[href|target|style]',
                'iframe[width|height|src|frameborder|allowfullscreen|class]',
                'strong', 'b', 'i', 'u', 'em', 'br', 'font',
                'h1[style|class]', 'h2[style|class]', 'h3[style|class]', 'h4[style|class]', 'h5[style|class]', 'h6[style|class]',
                'p[style|class]', 'div[style|class]', 'center', 'address[style|class]',
                'span[style|class]', 'pre[style|class]',
                'i[class]',
                'ul', 'ol', 'li',
                'table[width|height|border|style|class]', 'th[width|height|border|style|class]',
                'tr[width|height|border|style|class]', 'td[width|height|border|style|class]',
                'hr',
                //                'button'
                //                'maction','math','menclose','merror','mfenced','mfrac','mglyph','mi','mlabeledtr','mmultiscripts','mn','mo','mover','mpadded','mphantom','mroot','mrow','ms','mspace','msqrt','mstyle','msub','msubsup','msup','mtable','mtd','mtext','mtr','munder','munderover','semantics',
            ];

            //Find full HTML5 config : https://github.com/kennberg/php-htmlpurfier-html5
            $config = \HTMLPurifier_Config::createDefault();

            if (0) {
                if (isSupperAdmin_()) {
                    //For mathMl mathtype
                    //https://github.com/xemlock/htmlpurifier-html5/issues/12
                    $config = \HTMLPurifier_Config::create([
                        'HTML.AllowedElements' => ['maction', 'math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msubsup', 'msup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover', 'semantics'],
                    ]);
                }
            }

            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('CSS.AllowTricky', true);

            $config->set('Cache.SerializerPath', sys_get_temp_dir());

            // Allow iframes from:
            // o YouTube.com
            // o Vimeo.com
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');

            $config->set('HTML.Allowed', implode(',', $allowed));

            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addBlankElement('maction'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('math'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('menclose'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('merror'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mfenced'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mfrac'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mglyph'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mi'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mlabeledtr'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mmultiscripts'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mn'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mo'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mover'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mpadded'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mphantom'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mroot'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mrow'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('ms'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mspace'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('msqrt'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mstyle'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('msub'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('msubsup'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('msup'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mtable'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mtd'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mtext'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('mtr'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('munder'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('munderover'));
            //            $config->set('HTML.Allowed', $config->getHTMLDefinition(true)->addAttribute('semantics'));

            // Set some HTML5 properties
            $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
            $config->set('HTML.DefinitionRev', 1);

            if ($def = $config->maybeGetRawHTMLDefinition()) {
                // http://developers.whatwg.org/sections.html

                //                $def->addElement('math', 'Block', 'Flow', 'Common');

                $def->addElement('section', 'Block', 'Flow', 'Common');
                $def->addElement('nav', 'Block', 'Flow', 'Common');
                $def->addElement('article', 'Block', 'Flow', 'Common');
                $def->addElement('aside', 'Block', 'Flow', 'Common');
                $def->addElement('header', 'Block', 'Flow', 'Common');
                $def->addElement('footer', 'Block', 'Flow', 'Common');

                // Content model actually excludes several tags, not modelled here
                $def->addElement('address', 'Block', 'Flow', 'Common');
                $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

                // http://developers.whatwg.org/grouping-content.html
                $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
                $def->addElement('figcaption', 'Inline', 'Flow', 'Common');

                // http://developers.whatwg.org/the-video-element.html#the-video-element
                $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src' => 'URI',
                    'type' => 'Text',
                    'width' => 'Length',
                    'height' => 'Length',
                    'poster' => 'URI',
                    'preload' => 'Enum#auto,metadata,none',
                    'controls' => 'Bool',
                ]);
                $def->addElement('source', 'Block', 'Flow', 'Common', [
                    'src' => 'URI',
                    'type' => 'Text',
                ]);

                // http://developers.whatwg.org/text-level-semantics.html
                $def->addElement('s', 'Inline', 'Inline', 'Common');
                $def->addElement('var', 'Inline', 'Inline', 'Common');
                $def->addElement('sub', 'Inline', 'Inline', 'Common');
                $def->addElement('sup', 'Inline', 'Inline', 'Common');
                $def->addElement('mark', 'Inline', 'Inline', 'Common');
                $def->addElement('wbr', 'Inline', 'Empty', 'Core');

                // http://developers.whatwg.org/edits.html
                $def->addElement('ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);
                $def->addElement('del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);

                // TinyMCE
                $def->addAttribute('img', 'data-mce-src', 'Text');
                $def->addAttribute('img', 'data-mce-json', 'Text');

                // Others
                $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
                $def->addAttribute('table', 'height', 'Text');
                $def->addAttribute('td', 'border', 'Text');
                $def->addAttribute('th', 'border', 'Text');
                $def->addAttribute('tr', 'width', 'Text');
                $def->addAttribute('tr', 'height', 'Text');
                $def->addAttribute('tr', 'border', 'Text');
            }

            self::$purifier = new \HTMLPurifier($config);
        }

        return self::$purifier;
    }
}
