<?php
require_once 'OpenDocument.php'; // open document class

//open test.odt
$odt = new OpenDocument('test.odt');
//output content as html
echo toHTML($odt);

function toHTML($obj)
{
    $html = '';
    foreach ($obj->getChildren() as $child) {
        switch (get_class($child)) {
        case 'OpenDocument_TextElement':
            $html .= $child->text;
            break;
        case 'OpenDocument_Paragraph':
            $html .= '<p>';
            $html .= toHTML($child);
            $html .= '</p>';
            break;
        case 'OpenDocument_Span':
            $html .= '<span>';
            $html .= toHTML($child);
            $html .= '</span>';
            break;
        case 'OpenDocument_Heading':
            $html .= '<h' . $child->level . '>';
            $html .= toHTML($child);
            $html .= '</h' . $child->level . '>';
            break;
        case 'OpenDocument_Hyperlink':
            $html .= '<a href="' . $child->location . '" target="' . $child->target . '">';
            $html .= toHTML($child);
            $html .= '</a>';
            break;
        default:
            $html .= '<del>unknown element</del>';
        }
    }
    return $html;
}

?>