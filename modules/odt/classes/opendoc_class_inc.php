<?php

class opendoc extends object
{
	public $odt;

	public function init()
	{
		require_once($this->getPearResource('OpenDocument.php'));
	}

	public function setup($filename = NULL)
	{
		if(isset($filename))
		{
			$this->odt = new OpenDocument($filename);
		}
		else {
			$this->odt = new OpenDocument;
		}
	}

	public function getChildren()
	{
		return $this->odt->getChildren();
	}

	public function toHtml($obj)
	{
		$html = '';
		foreach ($obj->getChildren() as $child) {
			switch (get_class($child)) {
				case 'OpenDocument_TextElement':
					$html .= $child->text;
					break;
				case 'OpenDocument_Paragraph':
					$html .= '<p>';
					$html .= $this->toHTML($child);
					$html .= '</p>';
					break;
				case 'OpenDocument_Span':
					$html .= '<span>';
					$html .= $this->toHTML($child);
					$html .= '</span>';
					break;
				case 'OpenDocument_Heading':
					$html .= '<h' . $child->level . '>';
					$html .= $this->toHTML($child);
					$html .= '</h' . $child->level . '>';
					break;
				case 'OpenDocument_Hyperlink':
					$html .= '<a href="' . $child->location . '" target="' . $child->target . '">';
					$html .= $this->toHTML($child);
					$html .= '</a>';
					break;
				default:
					$html .= '<del>unknown element</del>';
			}
		}
		return $html;
	}

}
?>