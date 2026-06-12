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
        // Clear cache to ensure latest SVG/CSS config is applied
        self::$purifier = null;
        
        // Clear temp files
        $temp_dir = sys_get_temp_dir();
        if (function_exists('exec')) {
            @exec("rm -rf {$temp_dir}/htmlpurifier* {$temp_dir}/php* 2>/dev/null");
        }
        
        $cleaned = self::getPurifier()->purify($value);
        
        // Fix self-closing SVG elements (convert <ellipse> to <ellipse />)
        // Match empty SVG elements and add self-closing slash
        $cleaned = preg_replace_callback(
            '/<(ellipse|circle|rect|line|path|image|animate|animatetransform|use)([^>]*)>(?!\w)/',
            function($matches) {
                $tag = $matches[1];
                $attrs = rtrim($matches[2]);
                // Add space before /> if attributes exist
                $space = !empty($attrs) ? ' ' : '';
                return "<{$tag}{$attrs}{$space}/>";
            },
            $cleaned
        );
        
        return $cleaned;
    }

    /**
     * @return \HTMLPurifier
     */
    //Find full HTML5 config : https://github.com/kennberg/php-htmlpurfier-html5
    private static function getPurifier()
    {
        if (is_null(self::$purifier)) {
            // Clear old cache files to ensure fresh config
            $temp_dir = sys_get_temp_dir();
            if (function_exists('exec')) {
                @exec("rm -rf {$temp_dir}/htmlpurifier* 2>/dev/null");
            }

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
                // SVG elements
                'svg[width|height|viewbox|style|xmlns]',
                'ellipse[class|cx|cy|rx|ry|fill|stroke|stroke-width|style|transform|transform-origin]',
                'circle[class|cx|cy|r|fill|stroke|stroke-width|style]',
                'rect[class|x|y|width|height|fill|stroke|stroke-width|style]',
                'path[class|d|fill|stroke|stroke-width|style]',
                'text[class|x|y|font-size|font-weight|text-anchor|fill|letter-spacing|style]',
                'tspan[class|x|y|font-size|fill|style]',
                'g[class|style|transform]',
                'line[class|x1|y1|x2|y2|stroke|stroke-width|style]',
                'polyline[class|points|fill|stroke|stroke-width|style]',
                'polygon[class|points|fill|stroke|stroke-width|style]',
                'use[class|href|x|y|width|height|style]',
                'defs[class]',
                'symbol[class|id|viewbox]',
                'image[class|href|x|y|width|height|style]',
                'animate[class|attributename|from|to|dur|repeatcount|style]',
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
            $config->set('HTML.XHTML', true); // Keep self-closing tags like <ellipse />
            $config->set('CSS.AllowTricky', true);
            $config->set('CSS.Proprietary', true);
            // $config->set('CSS.Strict', false); // Disable strict CSS validation
            
            // Allow CSS properties for SVG and styling - use array of property names
            $config->set('CSS.AllowedProperties', array(
                // 'transform', 'transform-origin', 'transform-box', 'translate', 'scale', 'rotate',
                // 'filter', 'opacity', 'blur', 'drop-shadow',
                // 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-dasharray', 
                // 'stroke-dashoffset', 'stroke-opacity', 'stroke-miterlimit',
                // 'fill', 'fill-opacity', 'fill-rule', 'clip-rule',
                'font-size', 'font-weight', 'font-family', 'font-style', 'font-variant',
                // 'text-anchor', 'text-decoration', 'letter-spacing', 'word-spacing',
                // 'dominant-baseline', 'alignment-baseline',
                'color', 'background-color', 'visibility', 'display', 'overflow',
                'width', 'height', 'margin', 'padding', 'border', 'border-radius', 
                // 'box-sizing',
                // 'position', 'top', 'right', 'bottom', 'left', 'z-index',
                // 'flex', 'flex-direction', 'justify-content', 'align-items',
                // 'grid-template', 'grid-column', 'grid-row',
                // 'box-shadow', 'text-shadow', 'line-height',
                'text-align', 'vertical-align',
                // 'white-space', 'direction', 'unicode-bidi', 'cursor', 'pointer-events'
            ));

            $config->set('Cache.SerializerPath', sys_get_temp_dir());
            $config->set('Cache.DefinitionImpl', null); // Disable definition caching to ensure CSS config is applied

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

                // SVG elements support
                $def->addElement('svg', 'Block', 'Flow', 'Common', [
                    'width' => 'Length',
                    'height' => 'Length',
                    'viewbox' => 'CDATA',
                    'xmlns' => 'URI',
                    'style' => 'CDATA',
                ]);
                $def->addElement('ellipse', 'Inline', 'Empty', 'Common', [
                    'cx' => 'CDATA',
                    'cy' => 'CDATA',
                    'rx' => 'CDATA',
                    'ry' => 'CDATA',
                    'fill' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'transform' => 'CDATA',
                    'transform-origin' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('circle', 'Inline', 'Empty', 'Common', [
                    'cx' => 'CDATA',
                    'cy' => 'CDATA',
                    'r' => 'CDATA',
                    'fill' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('rect', 'Inline', 'Empty', 'Common', [
                    'x' => 'CDATA',
                    'y' => 'CDATA',
                    'width' => 'CDATA',
                    'height' => 'CDATA',
                    'fill' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('line', 'Inline', 'Empty', 'Common', [
                    'x1' => 'CDATA',
                    'y1' => 'CDATA',
                    'x2' => 'CDATA',
                    'y2' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('path', 'Inline', 'Empty', 'Common', [
                    'd' => 'CDATA',
                    'fill' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('text', 'Inline', 'Inline', 'Common', [
                    'x' => 'CDATA',
                    'y' => 'CDATA',
                    'font-size' => 'CDATA',
                    'font-weight' => 'CDATA',
                    'text-anchor' => 'CDATA',
                    'fill' => 'CDATA',
                    'letter-spacing' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('tspan', 'Inline', 'Inline', 'Common', [
                    'x' => 'CDATA',
                    'y' => 'CDATA',
                    'font-size' => 'CDATA',
                    'fill' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('g', 'Block', 'Flow', 'Common', [
                    'transform' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('defs', 'Block', 'Flow', 'Common');
                $def->addElement('symbol', 'Block', 'Flow', 'Common', [
                    'id' => 'CDATA',
                    'viewbox' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('use', 'Inline', 'Empty', 'Common', [
                    'href' => 'URI',
                    'x' => 'CDATA',
                    'y' => 'CDATA',
                    'width' => 'CDATA',
                    'height' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('image', 'Inline', 'Empty', 'Common', [
                    'href' => 'URI',
                    'x' => 'CDATA',
                    'y' => 'CDATA',
                    'width' => 'CDATA',
                    'height' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('polyline', 'Inline', 'Empty', 'Common', [
                    'points' => 'CDATA',
                    'fill' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('polygon', 'Inline', 'Empty', 'Common', [
                    'points' => 'CDATA',
                    'fill' => 'CDATA',
                    'stroke' => 'CDATA',
                    'stroke-width' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('animate', 'Inline', 'Empty', 'Common', [
                    'attributename' => 'CDATA',
                    'from' => 'CDATA',
                    'to' => 'CDATA',
                    'dur' => 'CDATA',
                    'repeatcount' => 'CDATA',
                    'style' => 'CDATA',
                ]);
                $def->addElement('animatetransform', 'Inline', 'Empty', 'Common', [
                    'attributename' => 'CDATA',
                    'type' => 'CDATA',
                    'from' => 'CDATA',
                    'to' => 'CDATA',
                    'dur' => 'CDATA',
                    'repeatcount' => 'CDATA',
                    'style' => 'CDATA',
                ]);

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
