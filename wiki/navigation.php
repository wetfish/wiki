<?php


class Navigation
{
	public $Links = array();
	public $Template = array();

	public function Add($Link, $URL)
		{ $this->Links[$Link]['URL'] = $URL; }

	public function Remove($Link)
		{ unset($this->Links[$Link]); }

	public function Template($Data)
	{
		$this->Template['Title'] = $Data['Title'];
		$this->Template['Header'] = $Data['Header'];
		$this->Template['Footer'] = $Data['Footer'];
	}

	public function Active($Link)
	{
		$Activated = FALSE;
		$Links = $this->Links;

		foreach($Links as $Text=>$Data)
		{
			if(is_array($Link) and in_array($Text, $Link))
				$Links[$Text]['Active'] = TRUE;
			elseif($Text == $Link)
				$Links[$Text]['Active'] = TRUE;
			else
				$Links[$Text]['Active'] = FALSE;
		}

		# Update the links container.
		$this->Links = $Links;
	}

	public function Export()
	{
		$Buffer = array();
		$Links = $this->Links;
		$Template = $this->Template;

		foreach($Links as $Text=>$Data)
		{
			$Class = '';

			if($Data['Active']) {
				$Class = 'title'; }

			# If there's no $Path, you wind up with //, which is very different from /.
			$Buffer[] = "<a href='".str_replace("//", "/", $Data['URL'])."' class='$Class' rel='nofollow'>$Text</a>";
		}

		$Buffer = implode(" | ", $Buffer);
		return "{$Template['Title']} {$Template['Header']}$Buffer{$Template['Footer']}";
	}
}

?>