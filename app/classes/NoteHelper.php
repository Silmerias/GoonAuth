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
		if (!isset($bind))
			$note->NGlobal = true;

		$note->save();

		if (isset($bind))
			$bind->notes()->attach($note);
	}
}
