<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right form-inline">
      	<?php if (count($stores)>1) { ?>
	    <select class="form-control" onChange="location='<?php echo $module_link; ?>&store_id='+$(this).val()">
	     <?php foreach($stores as $store){ ?>
	     <?php if($store['store_id'] == $store_id){ ?>
	     <option value="<?php echo $store['store_id']; ?>" selected="selected" ><?php echo $store['name']; ?></option>
	     <?php }else{ ?>
	     <option value="<?php echo $store['store_id']; ?>" ><?php echo $store['name']; ?></option>
	     <?php } ?>
	     <?php } ?>
	    </select>
		<?php } ?>
		<?php if($module_id !== 0) {?>
			<button id="save_and_stay" data-toggle="tooltip" title="<?php echo $button_save_and_stay; ?>" class="btn btn-success"><i class="fa fa-save"></i></button>
        <?php } ?>
        <button id="save_and_exit" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
	  </div>
      <h1><?php echo $heading_title; ?> <?php echo $version; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
  	<?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
            <ul class="nav nav-tabs">
				<li class="active"><a href="#tab_setting" data-toggle="tab">
					<span class="fa fa-cog"></span>
					<?php echo $text_settings; ?>
				</a></li>
				<li><a href="#tab_design" data-toggle="tab">
					<span class="fa fa-eye"></span>
					<?php echo $text_design; ?>
				</a></li>
				<li><a href="#tab_instruction" data-toggle="tab">
					<span class="fa fa-graduation-cap"></span>
					<?php echo $text_instructions; ?>
				</a></li>
			</ul>

	      <div class="tab-content">
	      	<div class="tab-pane active" id="tab_setting">
	      		<div class="tab-body">
					<div class="form-group required">
					<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
						<div class="col-sm-10">
							<input type="text" name="<?php echo $id; ?>[name]" value="<?php echo $setting['name']; ?>" placeholder="<?php echo $entry_name; ?>" id="input_name" class="form-control" />
							<?php if ($error_name) { ?>
								<div class="text-danger"><?php echo $error_name; ?></div>
							<?php } ?>
						</div>
					</div>
          <div class="form-group">
            <label class="control-label col-md-2" for="input_status"><?php echo $entry_status; ?></label>
              <div class="col-md-10">
                    <input type="hidden" name="<?php echo $id;?>[status]" value="0" />
                    <input type="checkbox" class="switcher" id="input_status" name="<?php echo $id;?>[status]" <?php echo ($setting['status']) ? 'checked="checked"':'';?> value="1" />
              </div>
          </div> <!--status-->
					<div class="form-group">
						<div class="col-sm-12">
							<table id="table_social_likes">
								<thead>
									<tr>
										<td><label class="control-label"><?php echo $text_sort_order; ?></label></td>
										<td><label class="control-label"><?php echo $entry_icon_color; ?></label></td>
										<td><label class="control-label"><?php echo $entry_icon_color_active; ?></label></td>
										<td><label class="control-label"><?php echo $entry_background_color; ?></label></td>
										<td><label class="control-label"><?php echo $entry_background_color_active; ?></label></td>
										<td><label class="control-label"><span data-toggle="tooltip" title="<?php echo $help_width; ?>"><?php echo $entry_width; ?></span></label></td>
										<td><label class="control-label"><span data-toggle="tooltip" title="<?php echo $help_api; ?>"><?php echo $entry_api; ?></span></label></td>
										<td></td>
									</tr>
								</thead>
								<tbody id="social_likes" class="sortable">
								<?php foreach($setting['social_likes'] as $social_like) { ?>
									<tr class="sort-item" data-sort-order="<?php echo $social_like['sort_order']; ?>">
										<td>
											<input type="hidden" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][enabled]" value="0" />
											<input type="checkbox" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][enabled]" <?php echo ($social_like['enabled'])? 'checked="checked"':'';?> value="1" id="<?php echo $id; ?>_settings_social_likes_<?php echo $social_like['id']; ?>_enabled" class="switcher" data-size="mini"/>
											<label class="label-top" for="<?php echo $id; ?>_social_likes_<?php echo $social_like['id']; ?>_enabled"><i class="<?php echo $social_like['icon']; ?>"></i> <?php echo ${'text_'.$social_like['id']}; ?></label>
											<input type="hidden" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][sort_order]" class="sort-value" value="<?php echo $social_like['sort_order']; ?>" />
										</td>
										<td>
											<div class="input-group color-picker">
												<input type="text" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][icon_color]" class="form-control" value="<?php echo $social_like['icon_color']; ?>" />
												<span class="input-group-addon"><i></i></span>
											</div>
										</td>
										<td>
											<div class="input-group color-picker">
												<input type="text" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][icon_color_active]" class="form-control" value="<?php echo $social_like['icon_color_active']; ?>" />
												<span class="input-group-addon"><i></i></span>
											</div>
										</td>
										<td>
											<div class="input-group color-picker">
												<input type="text" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][background_color]" class="form-control" value="<?php echo $social_like['background_color']; ?>" />
												<span class="input-group-addon"><i></i></span>
											</div>
										</td>
										<td>
											<div class="input-group color-picker">
												<input type="text" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][background_color_active]" class="form-control" value="<?php echo $social_like['background_color_active']; ?>" />
												<span class="input-group-addon"><i></i></span>
											</div>
										</td>
										<td>
											<input type="text" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][width]" class="form-control" value="<?php echo $social_like['width']; ?>" placeholder="<?php echo $text_width; ?>" size="4"/>
										</td>
										<td>
											<?php if(isset($social_like['api'])){ ?>
												<input type="text" name="<?php echo $id; ?>[social_likes][<?php echo $social_like['id']; ?>][api]" class="form-control api" value="<?php echo $social_like['api']; ?>" size="4"/>
											<?php } ?>
										</td>
										<td><i class="icon-drag fa fa-bars"></i></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-language"><?php echo $entry_language; ?></label>
						<div class="col-sm-10">
							<select name="<?php echo $id; ?>[language_id]" id="input_language" class="form-control">
							<?php foreach ($languages as $language) { ?>
							<?php if ($language['language_id'] == $setting['language_id']) { ?>
								<option value="<?php echo $language['language_id']; ?>" selected="selected"><?php echo $language['name']; ?></option>
							<?php } else { ?>
								<option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
							<?php } ?>
							<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-store"><?php echo $entry_store; ?></label>
						<div class="col-sm-10">
							<select name="<?php echo $id; ?>[store_id]" id="input_store" class="form-control">
							<?php foreach ($stores as $store) { ?>
							<?php if ($store['store_id'] == $setting['store_id']) { ?>
								<option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
							<?php } else { ?>
								<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
							<?php } ?>
							<?php } ?>
							</select>
						</div>
					</div>
			        <?php if ($config_files) { ?>
			        <div class="form-group">
			            <label class="col-sm-2 control-label" for="input-config"><span data-toggle="tooltip" title="<?php echo $help_config_files; ?>"><?php echo $entry_config_files; ?></span></label>
			            <div class="col-sm-10">
			              <select name="<?php echo $id; ?>[config]" id="input_config_file" class="form-control">
			                <?php foreach ($config_files as $config_file) { ?>
			                <option value="<?php echo $config_file; ?>"><?php echo $config_file; ?></option>
			                <?php } ?>
			              </select>
			            </div>
			        </div>
			        <?php } ?>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-url"><span data-toggle="tooltip" title="<?php echo $help_url; ?>"><?php echo $entry_url; ?></span></label>
						<div class="col-sm-10">
							<input type="text" name="<?php echo $id; ?>[url]" value="<?php echo $setting['url']; ?>" placeholder="<?php echo $entry_url; ?>" class="form-control"/>
						</div>
					</div>

				</div>
			</div>
			<div class="tab-pane" id="tab_design">
				<div class="tab-body">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-view"><?php echo $entry_view; ?></label>
						<div class="col-sm-10">
							<select name="<?php echo $id; ?>[view_id]" class="form-control">
								<?php foreach ($views as $view) { ?>
								<?php if ($view['view_id'] == $setting['view_id']) { ?>
									<option value="<?php echo $view['view_id']; ?>" selected="selected"><?php echo $view['name']; ?></option>
								<?php } else { ?>
									<option value="<?php echo $view['view_id']; ?>"><?php echo $view['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-theme"><span data-toggle="tooltip" title="<?php echo $help_icon_theme; ?>"><?php echo $entry_icon_theme; ?></span></label>
						<div class="col-sm-10">
							<select name="<?php echo $id; ?>[design][icon_theme]" class="form-control">
							<?php foreach($icon_themes as $icon_theme ){ ?>
								<option <?php echo ($setting['design']['icon_theme'] == $icon_theme) ? 'selected="selected"' : ''; ?> value="<?php echo $icon_theme; ?>"><?php echo $icon_theme; ?></option>
							<?php } ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-background-color"><?php echo $entry_background_color; ?></label>
						<div class="input-group color-picker col-sm-2"><input type="text" name="<?php echo $id; ?>[design][background_color]" class="form-control" value="<?php echo $setting['design']['background_color']?>"/><span class="input-group-addon"><i></i></span></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-border"><?php echo $entry_border; ?></label>
						<div class="col-sm-10"><input type="hidden" name="<?php echo $id; ?>[design][border]" value="0" />
						<input type="checkbox" name="<?php echo $id; ?>[design][border]" <?php echo ($setting['design']['border']) ? 'checked="checked"' : ''; ?> value="1" class="switcher"/></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-border-color"><?php echo $entry_border_color; ?></label>
						<div class="input-group color-picker col-sm-2"><input type="text" name="<?php echo $id; ?>[design][border_color]" class="form-control" value="<?php echo $setting['design']['border_color']?>"/><span class="input-group-addon"><i></i></span></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-box-shadow"><?php echo $entry_box_shadow; ?></label>
						<div class="col-sm-10">
						<input type="hidden" name="<?php echo $id; ?>[design][box_shadow]" value="0" />
						<input type="checkbox" name="<?php echo $id; ?>[design][box_shadow]" <?php echo ($setting['design']['box_shadow']) ? 'checked="checked"' : ''; ?> value="1" class="switcher"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-shadow-color"><?php echo $entry_box_shadow_color; ?></label>
						<div class="input-group color-picker col-sm-2"><input type="text" name="<?php echo $id; ?>[design][box_shadow_color]" class="form-control" value="<?php echo $setting['design']['box_shadow_color']?>"/><span class="input-group-addon"><i></i></span></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-border-radius"><?php echo $entry_border_radius; ?></label>
						<div class="col-sm-2">
						<input type="hidden" name="<?php echo $id; ?>[design][border_radius]" value="0" />
						<input type="checkbox" name="<?php echo $id; ?>[design][border_radius]" <?php echo ($setting['design']['border_radius']) ? 'checked="checked"' : ''; ?> value="1" class="switcher"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-mobile"><?php echo $entry_popup_mobile; ?></label>
						<div class="col-sm-2">
						<input type="hidden" name="<?php echo $id; ?>[design][popup_mobile]" value="0" />
						<input type="checkbox" name="<?php echo $id; ?>[design][popup_mobile]" <?php echo ($setting['design']['popup_mobile']) ? 'checked="checked"' : ''; ?> value="1" class="switcher"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-style"><span data-toggle="tooltip" title="<?php echo $help_custom_style; ?>"><?php echo $entry_custom_style; ?></span></label>
						<div class="col-sm-10"><textarea name="<?php echo $id; ?>[design][custom_style]" id="design_custom_style" class="form-control"><?php echo $setting['design']['custom_style']; ?></textarea></div>
					</div>
				</div>
			</div>
	      	<div class="tab-pane" id="tab_instruction">
				<div class="tab-body"><?php echo $text_instructions_full; ?></div>
			</div>
	      </div>
        </form>
      </div>
    </div>
  </div>

<script type="text/javascript"><!--

$('.color-picker').colorpicker();

$('#social_likes > .sort-item').tsort({attr:'data-sort-order'});

Sortable.create(social_likes, {
	group: "sorting",
	sort: true,
	animation: 150,
	handle: ".icon-drag",
	onUpdate: function (event){
		$('#social_likes').find('.sort-item').each(function (i, row) {
			$(row).find('.sort-value').val(i)
		})
	}
});

$('.switcher[type=checkbox]').bootstrapSwitch({
    'onColor': 'success',
    'onText': '<?php echo $text_yes; ?>',
    'offText': '<?php echo $text_no; ?>'
});

</script>
<script type="text/javascript"><!--
function showAlert(json) {
	$('.alert, .text-danger').remove();
	$('.form-group').removeClass('has-error');

	if (json['error']) {
		if (json['error']['warning']) {
			$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		}

		for (i in json['error']) {
			var element = $('#input_' + i);

			if (element.parent().hasClass('input-group')) {
                $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
			} else {
				$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
			}
		}

		$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
	}

	if (json['success']) {
		$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '  <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
	}
}
</script>
<script type="text/javascript"><!--
$('body').on('click', '#save_and_stay', function(){
    $.ajax({
		type: 'post',
		url: $('#form').attr('action'),
		data: $('#form').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#content').fadeTo('slow', 0.5);
		},
		complete: function() {
			$('#content').fadeTo('slow', 1);
		},
		success: function(json) {
			showAlert(json);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
    });
});

$('body').on('click', '#save_and_exit', function(){
    $.ajax({
		type: 'post',
		url: $('#form').attr('action')+'&exit',
		data: $('#form').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#content').fadeTo('slow', 0.5);
		},
		complete: function() {
			$('#content').fadeTo('slow', 1);
		},
		success: function(json) {
			showAlert(json);
			if (json['success']) location = '<?php echo $get_cancel; ?>';
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
    });
});

$('body').on('click', '#button_update', function(){
    $.ajax({
		url: '<?php echo $get_update; ?>',
		type: 'post',
		dataType: 'json',

		beforeSend: function() {
			$('#button_update').find('.fa-refresh').addClass('fa-spin');
		},

		complete: function() {
			$('#button_update').find('.fa-refresh').removeClass('fa-spin');
		},

		success: function(json) {
			console.log(json);

			if (json['error']){
				$('#notification_update').html('<div class="alert alert-danger m-b-none">' + json['error'] + '</div>')
			}

			if (json['warning']){
				$html = '';

				if (json['update']){
					$.each(json['update'] , function(k, v) {
						$html += '<div>Version: ' +k+ '</div><div>'+ v +'</div>';
					});
				}
				$('#notification_update').html('<div class="alert alert-warning alert-inline">' + json['warning'] + $html + '</div>')
			}

			if(json['success']){
				$('#notification_update').html('<div class="alert alert-success alert-inline">' + json['success'] + '</div>')
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
    })
});

</script>
<?php echo $footer; ?>
