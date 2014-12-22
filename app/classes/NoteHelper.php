<?php

class NoteHelper
{
	public static function Add($arr)
	{
		$user = $arr['user'];
		$createdby = $arr['createdby'];
		$bind = $arr['obj'];
		$type = $arr['type'];
		$text = $arr['text'];

		$note = new Note;
		$note->NTID = $type->NTID;
		$note->UID = $user->UID;
		$note->NNote = $text;
		if (isset($createdby))
			$note->NCreatedByUID = $createdby->UID;

		$note->save();

		$bind->notes()->attach($note);
	}
}
