<?php

namespace App\Extensions\Notes;

use App\Note;

class NoteHelper
{
	public static function Add($arr)
	{
		$user = $arr['user'];
		$createdby = $arr['createdby'];
		$org = isset($arr['org']) ? $arr['org'] : null;
		$group = isset($arr['group']) ? $arr['group'] : null;
		$type = $arr['type'];
		$subject = $arr['subject'];
		$message = $arr['message'];

		$note = new Note;
		$note->NTID = $type->NTID;
		$note->UID = $user->UID;
		$note->NMessage = trim($message);

		// Subject is optional.
		if (isset($subject))
			$note->NSubject = trim($subject);

		// Created by is optional.
		if (isset($createdby))
			$note->NCreatedByUID = $createdby->UID;

		// If no org or group is set, make it global.
		if (!isset($org) && !isset($group))
			$note->NGlobal = true;

		$note->save();

		if (isset($org))
			$org->notes()->attach($note);
		if (isset($group))
			$group->notes()->attach($note);
	}
}
