<?php
function cura_table_info($tbname) {
	global $wpdb;
	
	$sql = "DESCRIBE `" . addslashes ( $tbname ) . "`";
	return $wpdb->get_results ( $sql );
}
function cura_create_table_main() {
	global $wpdb;
	
	$sql = "CREATE TABLE  `" . CURAH2O_TABLE . "` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`location_id` VARCHAR( 20 ) NOT NULL ,
		`station_name` VARCHAR( 80 ) NOT NULL ,
		INDEX (  `location_id` ,  `station_name` )
	) ENGINE = MYISAM";
	$wpdb->query ( $sql );
}
function cura_create_table_location() {
	global $wpdb;
	
	$sql = "CREATE TABLE  `" . CURAH2O_TABLE_LOCATION . "` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`watershed_name` VARCHAR( 80 ) NOT NULL ,
		`count` INT NOT NULL
	) ENGINE = MYISAM";
	$wpdb->query ( $sql );
}

function cura_add_field($fieldName, $value) {
	global $wpdb;
	
	$sql = "
	ALTER TABLE
	`" . CURAH2O_TABLE . "`
	ADD		`$fieldName`" . (is_numeric ( $value ) ? "
			FLOAT" : "
			VARCHAR( 80 )") . "
			NOT NULL";
	$wpdb->query ( $sql );
}

function cura_get_entry($id) {
	global $wpdb;
	
	$id = intval ( $id );
	$sql = "
		SELECT	*
				, DATE_FORMAT(datetime, '%m/%d/%Y %l:%i %p') datetime
		FROM	`" . CURAH2O_TABLE . "`
		WHERE	id = $id
	";
	return $wpdb->get_row ( $sql, ARRAY_A );
}

function cura_add_entry($params = array()) {
	global $wpdb;
	
	foreach ( $params as $k => $v ) {
		$values [] = "`$k` = '" . addslashes ( $v ) . "'";
	}
	$sql = "INSERT INTO `" . CURAH2O_TABLE . "` SET
	" . implode ( "
			, ", $values );
	$affectedRows = $wpdb->query ( $sql );
	
	$entry = cura_get_entry ( $wpdb->insert_id );
	return array ('affectedRows' => $affectedRows, 'data' => $entry, 'insertId' => $wpdb->insert_id );
}
function cura_update_entry($id, $params) {
	global $wpdb;
	
	$id = intval ( $id );
	foreach ( $params as $k => $v ) {
		$values [] = "`$k` = '" . addslashes ( $v ) . "'";
	}
	$sql = "UPDATE `" . CURAH2O_TABLE . "` SET
	" . implode ( "
			, ", $values ) . "
			WHERE	id = $id";
	$affectedRows = $wpdb->query ( $sql );
	
	$entry = cura_get_entry ( $id );
	return array ('affectedRows' => $affectedRows, 'data' => $entry, 'insertId' => 0 );
}
function cura_delete_entry($id) {
	global $wpdb;
	
	$id = intval ( $id );
	$sql = "DELETE FROM `" . CURAH2O_TABLE . "` WHERE id = $id";
	
	$affectedRows = $wpdb->query ( $sql );
	return $affectedRows;
}

function cura_get_observations($params = array()) {
	global $wpdb;
	
	$filters = ( array ) $params ['filters'];
	$sql_filter = array ();
	foreach ( $filters as $filter ) {
		$sql_filter [] = sprintf ( "`%s` = '%s'", $filter ['field'], addslashes ( $filter ['value'] ) );
	}
	
	$sql = "
		SELECT	*
				, DATE_FORMAT(datetime, '%m/%d/%Y %l:%i %p') datetime
		FROM	`" . CURAH2O_TABLE . "`
		WHERE	1" . (empty ( $sql_filter ) ? "" : "
			AND	" . implode ( "
			AND	", $sql_filter )) . "
	";
	return $wpdb->get_results ( $sql, ARRAY_A );
}

function cura_get_locations() {
	global $wpdb;
	
	$sql = "
		SELECT	*
		FROM	`" . CURAH2O_TABLE_LOCATION . "`
		WHERE	1
		ORDER BY
				count DESC
	";
	return $wpdb->get_results ( $sql, ARRAY_A );
}

function cura_update_locations() {
	global $wpdb;
	
	$sql = "TRUNCATE TABLE `" . CURAH2O_TABLE_LOCATION . "`";
	$wpdb->query($sql);
	
	$sql = "
		INSERT INTO
			`" . CURAH2O_TABLE_LOCATION . "` (
				watershed_name,
				count
			)
		SELECT	a.watershed_name, count(*)
		FROM	`" . CURAH2O_TABLE . "` a
		LEFT JOIN
				`" . CURAH2O_TABLE_LOCATION . "` b
			ON	a.watershed_name = b.watershed_name
		WHERE	a.watershed_name != ''
			AND	b.id IS NULL
		GROUP BY
				a.watershed_name
	";
	$wpdb->query ( $sql );
}

function cura_update_location($watershed_name) {
	global $wpdb;

	$sql = "
		SELECT	watershed_name
		FROM	`" . CURAH2O_TABLE_LOCATION . "`
		WHERE	watershed_name = '" . addslashes($watershed_name) . "'
		LIMIT	1
	";
	$row = $wpdb->get_row ( $sql, ARRAY_A );

	if ($row) {
		$sql = "
			SELECT	count(*)
			FROM	`" . CURAH2O_TABLE . "`
			WHERE	watershed_name = '" . addslashes($watershed_name) . "'
		";
		$count = $wpdb->get_var ( $sql );
		
		$sql = "
			UPDATE	`" . CURAH2O_TABLE_LOCATION . "`
			SET		count = $count
			WHERE	watershed_name = '" . addslashes($watershed_name) . "'
			LIMIT	1
		";
		$wpdb->query($sql);
		
		$sql = "
			DELETE FROM
					`" . CURAH2O_TABLE_LOCATION . "`
			WHERE	count = 0
		";
		$wpdb->query($sql);
	} else {
		$sql = "
			INSERT INTO
			`" . CURAH2O_TABLE_LOCATION . "`
			SET		count = 1
			, watershed_name = '" . addslashes($watershed_name) . "'
		";
		$wpdb->query($sql);
	}
}

function cura_get_typeaheads() {
	global $wpdb;
	
	$sql = "
		SELECT	location_id
				, station_name
				, watershed_name
		FROM	`" . CURAH2O_TABLE . "`
		WHERE	1
		GROUP BY
				watershed_name, location_id, station_name
	";
	return $wpdb->get_results ( $sql, ARRAY_A );
}









