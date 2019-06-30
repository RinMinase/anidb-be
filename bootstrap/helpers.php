<?php

if (! function_exists('mongo_json')) {
	function mongo_json($query_result)
	{
		foreach($query_result as $k => $row) {
			$data[] = $row;
		}

		return json_encode($data);
	}
}
