<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title></title>
<link rel="stylesheet"
	href="<?php echo $base_url?>/lib/jquery.mobile/jquery.mobile-1.1.1.min.css" />
<link rel="stylesheet"
	href="<?php echo $base_url?>/lib/jqm-datebox-1.1.0/jqm-datebox-1.1.0.min.css" />
<link rel='stylesheet'
	href="<?php echo $base_url?>/../vendor/bootstrap/css/typeahead.css" />
<style>
/* App custom styles */
#details .field {
	background-color: #f0f0f0;
	height: 2em;
	padding: 0.5em 0.5em 0 0;
	margin: 1px 1px 0 -1px;
	text-align: right;
}

#details .value {
	background-color: white;
	height: 2em;
	padding: 0.5em 0 0 0.5em;
	margin: 1px 0 0 0;
}

#newob form .ui-block-a {
	width: 49%;
	margin-right: 1%;
}

#newob form .ui-block-b {
	width: 49%;
	margin-left: 1%;
}

.ui-input-datebox {
	width: 92% !important;
}

#list-observations li .date {
	float: right;
	display: block;
	color: silver;
	font-size: 10px;
	text-align: right;
	height: 1.5em;
	margin: -1.5em 40px 0 0;
}

#lab_sample_label {
	display: block;
	margin: 0 0 10px 0;
}

#div_coliform {
	text-align: center;
	margin: 0.5em 0 1.5em 0
}

div.ui-slider-switch {
	width: 150px !important;
	vertical-align: middle;
}

form input.error {
	border: 1px solid red
}

form label.error {
	color: red;
}
</style>
<script src="<?php echo $jq_src?>"></script>
<script
	src="<?php echo $base_url?>/lib/jquery.mobile/jquery.mobile-1.1.1.min.js"></script>
<script type="text/javascript"
	src="<?php echo $base_url?>/lib/jqm-datebox-1.1.0/jqm-datebox-1.1.0.core.min.js"></script>
<script type="text/javascript"
	src="<?php echo $base_url?>/lib/jqm-datebox-1.1.0/jqm-datebox-1.1.0.mode.calbox.min.js"></script>
<script type="text/javascript"
	src="<?php echo $base_url?>/lib/jqm-datebox-1.1.0/jqm-datebox-1.1.0.mode.datebox.min.js"></script>
<script type='text/javascript'
	src="<?php echo $base_url?>/../vendor/bootstrap/js/bootstrap-typeahead-2.1.0-customized.js"></script>
<script type='text/javascript'
	src="<?php echo $base_url?>/../vendor/jquery-validation/jquery.validate.js"></script>
<script type='text/javascript'
	src="<?php echo $base_url?>/../vendor/jquery-validation/additional-methods.js"></script>
<script type='text/javascript'>
var cura_validation_options = <?php echo json_encode( cura_validation_options() )?>;
for (var i in cura_validation_options.rules) {
	var rules = cura_validation_options.rules[i];
	for (var j in rules) {
		if (j == 'pattern') {
			rules[j] = new RegExp(rules[j].substr(1, rules[j].length - 2));
		}
	}
}
var observationFields = <?php echo json_encode( cura_fields() )?>;
</script>
<script src="<?php echo $base_url?>/app.js?<?php echo CURAH2O_VERSION?>"></script>
</head>

<body>
	<!-- Home -->
	<div data-role="page" data-theme="b" id="home">
		<div data-theme="a" data-role="header">
			<a id="reload-locations" data-role="button" data-transition="fade"
				data-icon="refresh" data-iconpos="left" class="ui-btn-left">Refresh</a>
			<a href="#newob" data-role="button" data-transition="slide"
				data-icon="add" data-iconpos="right" class="ui-btn-right">New Ob</a>
			<h3>CuraH2O Mobile</h3>
		</div>
		<div data-role="content" style="padding: 15px">
			<ul id="list-locations" data-role="listview" data-divider-theme="b"
				data-inset="false">
				<li data-role="list-divider" role="heading">Community groups</li>
			</ul>
		</div>
		<div data-role="footer" data-position="fixed">
			<a href="../../water-quality/" rel="external" data-role="button"
				data-inline="false">Desktop Site</a>
		</div>
	</div>
	<!-- Observation List -->
	<div data-role="page" data-theme="b" id="observations">
		<div data-theme="a" data-role="header">
			<a data-role="button" data-inline="true" href="#home"
				data-transition="slide" data-direction="reverse" data-icon="home"
				class="ui-btn-left">Home</a> <a data-role="button"
				data-transition="slide" href="#newob" data-icon="plus"
				data-iconpos="right" class="ui-btn-right">New Ob</a>
			<h3>Annapolis</h3>
		</div>
		<div data-role="content" style="padding: 15px">
			<ul id="list-observations" data-role="listview"
				data-divider-theme="b" data-inset="false">
				<li data-role="list-divider" role="heading">Observations</li>
			</ul>
		</div>
	</div>
	<!-- Observation Details -->
	<div data-role="page" data-theme="b" id="details">
		<div data-theme="a" data-role="header">
			<a data-role="button" data-inline="true" data-rel="back"
				data-transition="slide" href="#observations" data-icon="back"
				class="ui-btn-left">Back</a>
			<h3></h3>
		</div>
		<div data-role="content" style="padding: 5px"></div>
	</div>
	<div data-role="page" data-theme="b" id="newob">
		<div data-theme="a" data-role="header">
			<a data-role="button" data-inline="true" href="#home"
				data-transition="slide" data-direction="reverse" data-icon="home"
				class="ui-btn-left">Home</a><a data-role="button" data-inline="true"
				data-icon="back" data-iconpos="right" data-rel="back">Back </a>
			<h3>New Observation</h3>
		</div>
		<div data-role="content" style="padding: 5px">
			<form>
				<input type="hidden" name="id" value="0" /> <label for="textinput1">
					Community group Name </label> <input name="watershed_name"
					id="textinput1" type="text" />
				<fieldset class="ui-grid-a">
					<div class="ui-block-a">
						<label for="textinput2"> Station Name </label> <input
							name="station_name" id="textinput2" type="text" />
					</div>
					<div class="ui-block-b">
						<label for="textinput3"> Location ID </label> <input
							name="location_id" id="textinput3" type="text" />
					</div>
				</fieldset>
				<div class="ui-grid-a">
					<div class="ui-block-a">
						<label for="textinput6">Date</label> <input id="date" name="date"
							type="text" data-role="datebox"
							data-options='{"mode":"calbox", "useFocus":true}' />
					</div>
					<div class="ui-block-b">
						<label for="textinput10">Time </label> <input id="time"
							name="time" type="text" data-role="datebox"
							data-options='{"mode":"timebox", "useFocus":true}' />
					</div>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('latitude')?>
					<?php echo cura_form_field('longitude', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('do_mgl')?>
					<?php echo cura_form_field('do_%', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('cond')?>
					<?php echo cura_form_field('salinity', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('temp')?>
					<?php echo cura_form_field('air_temp', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('secchi_a')?>
					<?php echo cura_form_field('secchi_b', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('secchi_d')?>
					<?php echo cura_form_field('ph', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('lab_sample')?>
					<?php echo cura_form_field('lab_id', 'right')?>
				</div>
				<div class="ui-grid-a">
					<?php echo cura_form_field('nitrate')?>
					<?php echo cura_form_field('phosphate', 'right')?>
				</div>
				<?php echo cura_form_field('coliform')?>
				<?php echo cura_form_field('note')?>
				<button id="save" type="submit" data-icon="check"
					data-iconpos="right" data-theme="b">Submit</button>
			</form>
		</div>
	</div>
</body>
</html>